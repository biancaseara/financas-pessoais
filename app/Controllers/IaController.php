<?php
require_once BASE_PATH . '/core/Controller.php';

class IaController extends Controller {

    public function analisar() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }

        $id_usuario = $_SESSION['id_usuario'];

        $perfilModel = $this->model('PerfilFinanceiro');
        $motorPreditivo = $this->model('MotorPreditivo');
        $metaModel = $this->model('Meta');
        $conselhoModel = $this->model('ConselhoIa');
        $logModel = $this->model('LogApi');

        $perfil = $perfilModel->buscarPorIdUsuario($id_usuario);
        $projecao = $motorPreditivo->calcularProjecaoMensal($id_usuario);
        $raloDinheiro = $motorPreditivo->encontrarRaloDinheiro($id_usuario);
        $metas = $metaModel->listarTodos($id_usuario);

        $textoRalo = "";
        if (!empty($raloDinheiro)) {
            foreach ($raloDinheiro as $item) {
                $textoRalo .= "- " . $item['descricao'] . " (" . $item['quantidade'] . "x): R$ " . number_format($item['total_gasto'], 2, ',', '.') . "\n";
            }
        } else {
            $textoRalo = "Nenhum padrão de gasto impulsivo detectado neste mês.";
        }

        $textoMetas = "";
        if (!empty($metas)) {
            foreach ($metas as $meta) {
                $textoMetas .= "- " . $meta['titulo_meta'] . ": R$ " . number_format($meta['valor_atual'], 2, ',', '.') . " de R$ " . number_format($meta['valor_objetivo'], 2, ',', '.') . "\n";
            }
        } else {
            $textoMetas = "Nenhuma meta ativa no momento.";
        }

        $historicoConselhos = $conselhoModel->buscarUltimosConselhos($id_usuario, 2);
        $textoHistorico = "";
        if (!empty($historicoConselhos)) {
            foreach ($historicoConselhos as $index => $conselhoAntigo) {
                $textoHistorico .= ($index + 1) . ". " . substr($conselhoAntigo['mensagem'], 0, 100) . "...\n";
            }
        }

        $limiteSeguro = (float)($perfil['renda_exata'] ?? 0) * 0.8;

        $prompt = "Você é o PREDITIV.IA, um assistente financeiro de inteligência artificial altamente analítico e direto. Não use markdown na resposta.\n\n";
        
        $prompt .= "CONTEXTO DO USUÁRIO:\n";
        $prompt .= "- Renda Mensal: R$ " . number_format((float)($perfil['renda_exata'] ?? 0), 2, ',', '.') . " (" . ($perfil['tipo_renda'] ?? '') . ")\n";
        $prompt .= "- Dívida Atual: R$ " . number_format((float)($perfil['valor_divida_exata'] ?? 0), 2, ',', '.') . "\n";
        $prompt .= "- Objetivo Principal: " . ($perfil['objetivo_principal'] ?? '') . "\n";
        $prompt .= "- Maior Dificuldade: " . ($perfil['maior_problema'] ?? '') . "\n\n";

        $prompt .= "MATEMÁTICA PREDITIVA DO MÊS ATUAL:\n";
        $prompt .= "- Gasto acumulado: R$ " . number_format($projecao['total_gasto_ate_agora'], 2, ',', '.') . "\n";
        $prompt .= "- Taxa de Queima (Gasto Médio Diário): R$ " . number_format($projecao['burn_rate_diario'], 2, ',', '.') . "\n";
        $prompt .= "- PROJEÇÃO PARA O FIM DO MÊS: R$ " . number_format($projecao['projecao_fim_mes'], 2, ',', '.') . "\n\n";

        $prompt .= "RALO DE DINHEIRO (Gastos Variáveis Repetidos):\n";
        $prompt .= $textoRalo . "\n\n";

        $prompt .= "METAS ATIVAS:\n";
        $prompt .= $textoMetas . "\n\n";

        if (!empty($textoHistorico)) {
            $prompt .= "ÚLTIMOS CONSELHOS DADOS (Não repita a mesma ideia):\n" . $textoHistorico . "\n\n";
        }

        $prompt .= "INSTRUÇÃO DE COMPORTAMENTO (MUITO IMPORTANTE):\n";
        $prompt .= "Aja como um professor financeiro acolhedor, didático e paciente. O usuário é iniciante. NUNCA use jargões como 'Taxa de Queima', 'Burn Rate', 'Alavancagem' ou 'Projeção' sem explicar o que significam usando analogias simples do dia a dia.\n\n";

        $prompt .= "INSTRUÇÃO DE SAÍDA EXIGIDA:\n";
        $prompt .= "Você DEVE retornar a sua resposta EXCLUSIVAMENTE como um JSON válido, seguindo exatamente esta estrutura:\n";
        $prompt .= "{
    \"titulo\": \"Frase curta, acolhedora e encorajadora (ex: Atenção aos Gastos, ou Excelente Caminho)\",
    \"analise\": \"Análise do cenário em linguagem muito simples, como se explicasse para um amigo, focando no ritmo de gastos e nas metas.\",
    \"acao_imediata\": \"Um passo prático, fácil e indolor para fazer hoje.\",
    \"aprendizado\": \"Um parágrafo didático chamado 'PREDITIV.IA Ensina', explicando de forma simples um conceito de educação financeira. O tema deve ser escolhido de forma inteligente, começando pelos fundamentos e evoluindo gradualmente para assuntos mais avançados. Sempre considere os conceitos já apresentados anteriormente, evitando repetições e construindo o conhecimento de forma progressiva, para que cada novo aprendizado complemente e faça sentido em relação aos anteriores. Utilize linguagem clara, prática e acessível, com exemplos simples quando necessário.\",
    \"dados_grafico\": {
        \"gasto_atual\": " . round($projecao['total_gasto_ate_agora'], 2) . ",
        \"projecao_fim_mes\": " . round($projecao['projecao_fim_mes'], 2) . ",
        \"limite_seguro\": " . round($limiteSeguro, 2) . "
    }
}";

        $chavesRaw = getenv('GEMINI_API_KEY') ?: $_ENV['GEMINI_API_KEY'];
        $chavesRaw = trim($chavesRaw, " '\"\t\n\r\0\x0B"); 
        
        if (!$chavesRaw) {
            die("Erro Crítico: A Chave de API do Gemini não foi encontrada no arquivo .env.");
        }

        $listaChaves = array_map('trim', explode(',', $chavesRaw));

        $dados = [
            "contents" => [
                ["parts" => [["text" => $prompt]]]
            ],
            "generationConfig" => [
                "responseMimeType" => "application/json"
            ]
        ];

        $modelosDisponiveis = [
            'gemini-2.5-flash-lite',
            'gemini-2.0-flash',
            'gemini-flash-latest',
        ];

        // JSON de Erro Fallback
        $mensagemIA = json_encode([
            "titulo" => "Análise Indisponível",
            "analise" => "Os servidores da inteligência artificial estão processando um alto volume de dados.",
            "acao_imediata" => "Tente clicar em atualizar predição novamente em alguns minutos.",
            "aprendizado" => "O Preditiv.ia está temporariamente indisponível devido a alta demanda. Por favor, tente novamente mais tarde.",
            "dados_grafico" => [
                "gasto_atual" => round($projecao['total_gasto_ate_agora'], 2),
                "projecao_fim_mes" => round($projecao['projecao_fim_mes'], 2),
                "limite_seguro" => round($limiteSeguro, 2)
            ]
        ]);

        $sucessoAPI = false;

        // Loop Triplo Mágico: Tenta chave por chave, e modelo por modelo
        foreach ($listaChaves as $chaveApi) {
            if ($sucessoAPI) break;
            if (empty($chaveApi)) continue;

            foreach ($modelosDisponiveis as $modelo) {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent?key=" . $chaveApi;

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); 
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);

                $inicioTimer = microtime(true); 

                $resposta = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $erroCurl = curl_error($ch); 
                curl_close($ch);

                $tempoRespostaMs = round((microtime(true) - $inicioTimer) * 1000); 

                if ($erroCurl) {
                    error_log("Erro de cURL no Preditiv.ia: " . $erroCurl);
                    $logModel->registrar($id_usuario, "analisar (Erro cURL)", 500, 0, 0, $tempoRespostaMs);
                    continue; // Erro de conexão, tenta o próximo modelo
                }

                $resultado = json_decode($resposta, true);

                $tokensPrompt = $resultado['usageMetadata']['promptTokenCount'] ?? 0;
                $tokensCompletion = $resultado['usageMetadata']['candidatesTokenCount'] ?? 0;

                $logModel->registrar($id_usuario, "analisar ({$modelo})", $httpCode, $tokensPrompt, $tokensCompletion, $tempoRespostaMs);

                if (isset($resultado['error'])) {
                    error_log("Erro na API do Gemini ({$modelo} - {$httpCode}): " . json_encode($resultado['error']));
                    continue; // Erro da API (ex: 429 quota exceeded), tenta o próximo modelo/chave
                }

                if ($httpCode === 200 && isset($resultado['candidates'][0]['content']['parts'][0]['text'])) {
                    $textoBruto = $resultado['candidates'][0]['content']['parts'][0]['text'];
                    $textoBruto = str_replace(['```json', '```'], '', $textoBruto);
                    $mensagemIA = trim($textoBruto);
                    $sucessoAPI = true;
                    break; // Sai do loop de modelos
                }
            }
        }

        $conselhoModel->salvarConselho($id_usuario, $mensagemIA);

        $this->setFlash('success', 'Nova análise preditiva gerada pela IA!');
        header("Location: /financas/dashboard");
        exit;
    }

    public function analisarMeta($id_meta) {
        if (!isset($_SESSION['id_usuario'])) { header("Location: /financas/auth/login"); exit; }
        
        $id_usuario = $_SESSION['id_usuario'];
        $metaModel = $this->model('Meta');
        $motorPreditivo = $this->model('MotorPreditivo');
        
        $meta = $metaModel->buscarPorId($id_meta, $id_usuario);
        $projecao = $motorPreditivo->calcularProjecaoMensal($id_usuario);
        $raloDinheiro = $motorPreditivo->encontrarRaloDinheiro($id_usuario);
        
        $textoRalo = "";
        if (!empty($raloDinheiro)) {
            foreach ($raloDinheiro as $item) {
                $textoRalo .= "- " . $item['descricao'] . " (R$ " . number_format($item['total_gasto'], 2, ',', '.') . ")\n";
            }
        }

        $prompt = "Você é o PREDITIV.IA, um instrutor financeiro didático. Não use jargões.\n\n";
        $prompt .= "OBJETIVO DO USUÁRIO:\n";
        $prompt .= "Atingir a meta '{$meta['titulo_meta']}'. Ele já tem R$ " . number_format($meta['valor_atual'], 2, ',', '.') . " de R$ " . number_format($meta['valor_objetivo'], 2, ',', '.') . " e o prazo final é " . date('d/m/Y', strtotime($meta['data_limite'])) . ".\n\n";
        $prompt .= "ONDE ELE ESTÁ GASTANDO POR IMPULSO NESTE MÊS:\n{$textoRalo}\n\n";
        $prompt .= "Gasto diário atual (Taxa de Queima): R$ " . number_format($projecao['burn_rate_diario'], 2, ',', '.') . ".\n\n";
        
        $prompt .= "INSTRUÇÃO:\nRetorne APENAS um JSON válido com a seguinte estrutura:\n";
        $prompt .= "{\"titulo\": \"Frase de motivação\", \"analise\": \"Explique de forma simples quanto ele precisa guardar por mês para bater a meta a tempo\", \"acao_imediata\": \"Diga exatamente o que ele deve cortar do 'Ralo de Dinheiro' para acelerar a meta\", \"aprendizado\": \"Um conceito simples sobre juros compostos ou sacrifício temporário\"}";

        $_SESSION['insight_temporario'] = $this->_chamarGemini($prompt, 'analisarMeta');
        
        $this->setFlash('success', 'Plano de ação para sua meta gerado com sucesso!');
        header("Location: /financas/metas");
        exit;
    }

    public function analisarRendaExtra() {
        if (!isset($_SESSION['id_usuario'])) { header("Location: /financas/auth/login"); exit; }
        
        $id_usuario = $_SESSION['id_usuario'];
        $perfilModel = $this->model('PerfilFinanceiro');
        $perfil = $perfilModel->buscarPorIdUsuario($id_usuario);
        
        $prompt = "Você é o PREDITIV.IA, um instrutor financeiro criativo e didático.\n\n";
        $prompt .= "PERFIL DO USUÁRIO PARA RENDA EXTRA:\n";
        $prompt .= "- Habilidades: " . ($perfil['habilidades'] ?? 'Não informou') . "\n";
        $prompt .= "- Tempo Livre Semanal: " . ($perfil['horas_disponiveis'] ?? 'Pouco tempo') . "\n";
        $prompt .= "- Ferramentas: " . ($perfil['acesso_tecnologia'] ?? 'Celular com internet') . "\n\n";
        
        $prompt .= "INSTRUÇÃO:\nRetorne APENAS um JSON válido com a seguinte estrutura:\n";
        $prompt .= "{\"titulo\": \"Nome da ideia criativa de renda extra\", \"analise\": \"Um plano de ação em 3 passos simples cruzando as habilidades com as ferramentas que ele tem\", \"acao_imediata\": \"O que ele deve fazer hoje, em 10 minutos, para começar\", \"aprendizado\": \"Dica sobre como não misturar o dinheiro da renda extra com a conta pessoal\"}";

        $_SESSION['insight_temporario'] = $this->_chamarGemini($prompt, 'analisarRendaExtra');
        
        $this->setFlash('success', 'Ideias de renda extra personalizadas geradas!');
        header("Location: /financas/perfil");
        exit;
    }

    public function analisarExtratoCSV($jsonCsv) {
        $apiKey = $_ENV['GEMINI_API_KEY'];
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

        $prompt = "Você é um categorizador financeiro de banco de dados.
        O array JSON abaixo possui transações bancárias. Os valores, datas e tipos já estão 100% corretos.
        Sua ÚNICA tarefa é analisar cada objeto e devolver O MESMO ARRAY, alterando estritamente 2 chaves:
        
        1. 'descricao': Limpe o nome. Remova instituições repetitivas (ex: MERCADO PAGO, NU PAGAMENTOS, IP LTDA), remova 'Transferência enviada pelo Pix -' e deixe um nome curto de quem enviou/recebeu ou o estabelecimento comercial.
        2. 'categoria': Mude de 'Outros' para a categoria mais lógica (ex: Alimentação, Transporte, Saúde, Moradia, Serviços, Educação, Lazer, Receitas, Cartões).
        
        REGRA: Devolva APENAS o array JSON limpo. Nenhuma palavra a mais, nenhuma marcação markdown.
        
        JSON DE ENTRADA:
        " . $jsonCsv;

        $data = [
            "contents" => [["parts" => [["text" => $prompt]]]],
            "generationConfig" => ["temperature" => 0.0],
            "safetySettings" => [
                ["category" => "HARM_CATEGORY_DANGEROUS_CONTENT", "threshold" => "BLOCK_NONE"],
                ["category" => "HARM_CATEGORY_HARASSMENT", "threshold" => "BLOCK_NONE"],
                ["category" => "HARM_CATEGORY_HATE_SPEECH", "threshold" => "BLOCK_NONE"],
                ["category" => "HARM_CATEGORY_SEXUALLY_EXPLICIT", "threshold" => "BLOCK_NONE"]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        curl_close($ch);

        $resultado = json_decode($response, true);
        if (isset($resultado['candidates'][0]['content']['parts'][0]['text'])) {
            $textoIA = $resultado['candidates'][0]['content']['parts'][0]['text'];
            $inicio = strpos($textoIA, '[');
            $fim = strrpos($textoIA, ']');
            if ($inicio !== false && $fim !== false) {
                return substr($textoIA, $inicio, $fim - $inicio + 1);
            }
        }
        return "[]"; 
    }

    private function _chamarGemini($prompt,$endpoint = 'generico') {
        $id_usuario =$_SESSION['id_usuario'] ?? null;
        $logModel = clone$this->model('LogApi');
        
        $chavesRaw = getenv('GEMINI_API_KEY') ?:$_ENV['GEMINI_API_KEY'];
        $chavesRaw = trim($chavesRaw, " '\"\t\n\r\0\x0B"); 
        $listaChaves = array_map('trim', explode(',', $chavesRaw));$dados = [
            "contents" => [["parts" => [["text" => $prompt]]]],
            "generationConfig" => ["responseMimeType" => "application/json"]
        ];

        $modelosDisponiveis = ['gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-flash-latest'];
        
        foreach ($listaChaves as$chaveApi) {
            if (empty($chaveApi)) continue;

            foreach ($modelosDisponiveis as $modelo) {$url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent?key=" . $chaveApi;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                
                curl_setopt($ch, CURLOPT_TIMEOUT, 60); 

                $inicioTimer = microtime(true);

                $resposta = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $tempoRespostaMs = round((microtime(true) -$inicioTimer) * 1000);
                $resultado = json_decode($resposta, true);

                $tokensPrompt =$resultado['usageMetadata']['promptTokenCount'] ?? 0;
                $tokensCompletion =$resultado['usageMetadata']['candidatesTokenCount'] ?? 0;

                if ($id_usuario) {
                    $logModel->registrar($id_usuario, "{$endpoint} ({$modelo})", $httpCode,$tokensPrompt, $tokensCompletion,$tempoRespostaMs);
                }

                if ($httpCode === 200 && isset($resultado['candidates'][0]['content']['parts'][0]['text'])) {
                    $textoBruto =$resultado['candidates'][0]['content']['parts'][0]['text'];
                    return trim(str_replace(['```json', '```'], '', $textoBruto));
                }
            }
        }
        
        return json_encode([
            "titulo" => "Análise Indisponível", 
            "analise" => "Os servidores estão processando muitos dados.", 
            "acao_imediata" => "Tente novamente mais tarde.", 
            "aprendizado" => "O sistema possui um mecanismo de fallback para proteger sua experiência."
        ]);
    }

    public function processarExtrato() {
        set_time_limit(180); 
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' \vert{}\vert{} !isset($_FILES['arquivo_extrato'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Arquivo CSV não enviado.']);
            return;
        }

        $arquivo =$_FILES['arquivo_extrato']['tmp_name'];
        $textoCru = file_get_contents($arquivo);

        $linhas = explode("\n", $textoCru);
        $textoCruLimitado = implode("\n", array_slice($linhas, 0, 300));

        $prompt = "Você é um processador financeiro corporativo. 
        Abaixo está o conteúdo bruto de um extrato bancário (CSV). Pode ser do Nubank, Bradesco, Banco do Brasil, PicPay, etc. Os separadores podem ser vírgulas ou pontos e vírgulas, os valores podem estar separados em colunas de 'Crédito' e 'Débito', e podem conter sujeira como '−R$ 10,00'.
        Sua tarefa é analisar as linhas brutas, ignorar cabeçalhos inúteis e extrair as transações, devolvendo um ARRAY JSON limpo.

        REGRAS PARA CADA OBJETO DO JSON:
        1. 'data': Converta a data da transação para o formato 'YYYY-MM-DD'.
        2. 'valor': Extraia o valor financeiro como um FLOAT ABSOLUTO (apenas números e ponto. Ex: 15.90). Remova sinais de menos, 'R$' ou vírgulas.
        3. 'tipo_transacao': Se o dinheiro saiu da conta (débito/sinal negativo), use 'Saida'. Se entrou na conta (crédito/sinal positivo), use 'Entrada'.
        4. 'descricao': Limpe a descrição. Remova instituições repetitivas (MERCADO PAGO, etc), retire CNPJs/códigos e deixe o nome limpo de quem enviou/recebeu ou da loja.
        5. 'forma_pagamento': Deduza pelo texto se foi 'Pix', 'Crédito', 'Débito', 'Boleto' ou 'Outros'. Se for Resgate/Aplicação RDB, use 'Outros'.
        6. 'categoria': Categorize (Alimentação, Transporte, Saúde, Moradia, Serviços, Educação, Lazer, Renda, Transferência, Outros).

        REGRA DE RETORNO ESTREITA: Devolva EXCLUSIVAMENTE o array JSON puro (iniciando em [ e terminando em ]). Sem formatação markdown (```json), sem backticks, sem introduções.
        
        EXTRATO BRUTO:
        " . $textoCruLimitado;

        $jsonTransacoes = $this->_chamarGemini($prompt, 'processarExtrato');
        
        $testeJson = json_decode($jsonTransacoes, true);
        
        if (empty($testeJson) || !is_array($testeJson) || isset($testeJson['titulo'])) {
            http_response_code(500);
            echo json_encode(['erro' => 'A IA demorou muito para responder ou não identificou o formato.']);
            return;
        }

        header('Content-Type: application/json');
        echo $jsonTransacoes;
    }
}