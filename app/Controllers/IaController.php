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

        $chaveApi = getenv('GEMINI_API_KEY') ?: $_ENV['GEMINI_API_KEY'];
        $chaveApi = trim($chaveApi, " '\"\t\n\r\0\x0B"); 
        
        if (!$chaveApi) {
            die("Erro Crítico: A Chave de API do Gemini não foi encontrada no arquivo .env.");
        }

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
                continue; 
            }

            $resultado = json_decode($resposta, true);

            $tokensPrompt = $resultado['usageMetadata']['promptTokenCount'] ?? 0;
            $tokensCompletion = $resultado['usageMetadata']['candidatesTokenCount'] ?? 0;

            $logModel->registrar($id_usuario, "analisar ({$modelo})", $httpCode, $tokensPrompt, $tokensCompletion, $tempoRespostaMs);

            if (isset($resultado['error'])) {
                error_log("Erro na API do Gemini ({$modelo}): " . json_encode($resultado['error']));
                continue;
            }

            if (isset($resultado['candidates'][0]['content']['parts'][0]['text'])) {
                $textoBruto = $resultado['candidates'][0]['content']['parts'][0]['text'];
                $textoBruto = str_replace(['```json', '```'], '', $textoBruto);
                $mensagemIA = trim($textoBruto);
                break;
            }
        }

        $conselhoModel->salvarConselho($id_usuario, $mensagemIA);

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
        
        header("Location: /financas/perfil");
        exit;
    }

    private function _chamarGemini($prompt, $endpoint = 'generico') {
        $id_usuario = $_SESSION['id_usuario'] ?? null;
        $logModel = clone $this->model('LogApi');
        
        $chaveApi = getenv('GEMINI_API_KEY') ?: $_ENV['GEMINI_API_KEY'];
        $chaveApi = trim($chaveApi, " '\"\t\n\r\0\x0B"); 
        
        $dados = [
            "contents" => [["parts" => [["text" => $prompt]]]],
            "generationConfig" => ["responseMimeType" => "application/json"]
        ];

        $modelosDisponiveis = ['gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-flash-latest'];
        
        foreach ($modelosDisponiveis as $modelo) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent?key=" . $chaveApi;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);

            $inicioTimer = microtime(true);

            $resposta = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $tempoRespostaMs = round((microtime(true) - $inicioTimer) * 1000);
            $resultado = json_decode($resposta, true);

            $tokensPrompt = $resultado['usageMetadata']['promptTokenCount'] ?? 0;
            $tokensCompletion = $resultado['usageMetadata']['candidatesTokenCount'] ?? 0;

            if ($id_usuario) {
                $logModel->registrar($id_usuario, "{$endpoint} ({$modelo})", $httpCode, $tokensPrompt, $tokensCompletion, $tempoRespostaMs);
            }

            if (isset($resultado['candidates'][0]['content']['parts'][0]['text'])) {
                $textoBruto = $resultado['candidates'][0]['content']['parts'][0]['text'];
                return trim(str_replace(['```json', '```'], '', $textoBruto));
            }
        }
        
        return json_encode([
            "titulo" => "Análise Indisponível", 
            "analise" => "Os servidores estão processando muitos dados.", 
            "acao_imediata" => "Tente novamente mais tarde.", 
            "aprendizado" => "O sistema possui um mecanismo de fallback para proteger sua experiência."
        ]);
    }
}