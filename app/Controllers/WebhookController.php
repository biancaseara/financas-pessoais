<?php
require_once BASE_PATH . '/core/Controller.php';

class WebhookController extends Controller {

    public function telegram() {
        try {
            $conteudoJson = file_get_contents("php://input");
            $payload = json_decode($conteudoJson, true);

            if (!$payload or !isset($payload['chat_id']) or !isset($payload['dados_ia'])) {
                http_response_code(400);
                echo json_encode(["erro" => "Payload invalido ou dados ausentes"]);
                exit;
            }

            $chat_id =$payload['chat_id'];
            $dados = is_array($payload['dados_ia']) ? $payload['dados_ia'] : json_decode($payload['dados_ia'], true);

            // 1. Identifica o Usuário
            $db = new Database();
            $pdo =$db->getConnection();
            $stmt =$pdo->prepare("SELECT id_usuario FROM usuarios WHERE chat_id_telegram = ?");
            $stmt->execute([$chat_id]);
            $usuario =$stmt->fetch();

            if (!$usuario) {$msgErro = "⚠️ *Conta Não Vinculada!*\n\nSeu Código de Vínculo é: `" . $chat_id . "`";
                $this->enviarMensagemTelegram($chat_id,$msgErro);
                http_response_code(200); exit;
            }
            $id_usuario =$usuario['id_usuario'];

            // 2. Extrai e Formata Dados Básicos (Com as Novas Formas de Pagamento)
            $data =$dados['data'] ?? date('Y-m-d');
            if (strpos($data, '/') !== false) $data = implode('-', array_reverse(explode('/',$data)));
            
            $fp_raw = strtolower(trim($dados['forma_pagamento'] ?? 'debito'));
            if (strpos($fp_raw, 'credit') !== false or strpos($fp_raw, 'crédit') !== false) {
                $forma_pagamento = 'Crédito';
            } elseif (strpos($fp_raw, 'pix') !== false) {$forma_pagamento = 'Pix';
            } elseif (strpos($fp_raw, 'boleto') !== false) {$forma_pagamento = 'Boleto';
            } elseif (strpos($fp_raw, 'dinheiro') !== false or strpos($fp_raw, 'vivo') !== false) {$forma_pagamento = 'Dinheiro';
            } else {
                $forma_pagamento = 'Débito'; // Padrão
            }
            
            $nome_categoria_ia =$dados['categoria'] ?? 'Outros';
            $descricao =$dados['descricao'] ?? 'Gasto via Telegram';
            $parcelas = max(1, (int)($dados['parcelas'] ?? 1));
            
            $valor_bruto =$dados['valor'] ?? 0;
            if (is_string($valor_bruto)) {
                $valor_bruto = str_replace('.', '',$valor_bruto);
                $valor_bruto = str_replace(',', '.',$valor_bruto);
            }
            $valor_total = (float)$valor_bruto;
            $valor_parcela = ($parcelas > 1) ? round($valor_total / $parcelas, 2) :$valor_total;

            // 3. Processa Categoria Dinâmica
            $categoriaModel =$this->model('Categoria');
            $categorias =$categoriaModel->listarTodos($id_usuario);$id_categoria = null;
            foreach ($categorias as$cat) {
                if (strtolower(trim($cat['nome_categoria'])) == strtolower(trim($nome_categoria_ia))) {
                    $id_categoria =$cat['id_categoria']; break;
                }
            }
            if (!$id_categoria) {$categoriaModel->cadastrar($id_usuario,$nome_categoria_ia, 'D', null);
                $id_categoria =$this->model('Categoria')->pdo->lastInsertId();
            }

            // 4. Lógica Inteligente de Conta / Cartão
            $nome_origem_ia = strtolower(trim($dados['conta_origem'] ?? ''));$id_conta = null;
            $id_fatura = null;
            $nome_destino_final = '';
            
            $faturaModel =$this->model('Fatura');

            if ($forma_pagamento == 'Crédito') {
                $cartaoModel = $this->model('Cartao');$cartoes = $cartaoModel->listarTodos($id_usuario);
                
                if (empty($cartoes)) {
                    $this->enviarMensagemTelegram($chat_id, "⚠️ Você não tem cartões de crédito cadastrados para lançar essa despesa.");
                    http_response_code(400); exit;
                }
                
                $cartaoSelecionado =$cartoes[0];
                foreach ($cartoes as$c) {
                    if (strpos(strtolower($c['nome_cartao']),$nome_origem_ia) !== false) {
                        $cartaoSelecionado =$c; break;
                    }
                }
                
                $mes_ano = date('Y-m', strtotime($data));
                $id_fatura =$faturaModel->buscarOuCriarAberta($cartaoSelecionado['id_cartao'],$mes_ano);
                $nome_destino_final = "Cartão " . $cartaoSelecionado['nome_cartao'];
                
            } else {
                $contaModel = $this->model('Conta');$contas = $contaModel->listarTodos($id_usuario);
                
                if (empty($contas)) {
                    $this->enviarMensagemTelegram($chat_id, "⚠️ Cadastre uma conta bancária primeiro.");
                    http_response_code(400); exit;
                }

                $contaSelecionada =$contas[0];
                foreach ($contas as$c) {
                    if (strpos(strtolower($c['nome_banco']),$nome_origem_ia) !== false) {
                        $contaSelecionada =$c; break;
                    }
                }
                $id_conta =$contaSelecionada['id_conta'];
                $nome_destino_final = "Conta " . $contaSelecionada['nome_banco'];
            }

            // 5. Salva a Transação
            $transacaoModel =$this->model('Transacao');
            
            for ($i = 1; $i <= $parcelas; $i++) {
                $desc_final = ($parcelas > 1) ? $descricao . " ($i/$parcelas)" : $descricao;
                $data_lancamento = date('Y-m-d', strtotime("+$i months -1 month", strtotime($data)));
                
                $id_fatura_lancamento =$id_fatura;
                
                if ($forma_pagamento == 'Crédito' and $i > 1) {
                    $mes_ano_futuro = date('Y-m', strtotime($data_lancamento));
                    $id_fatura_lancamento =$faturaModel->buscarOuCriarAberta($cartaoSelecionado['id_cartao'],$mes_ano_futuro);
                }

                $transacaoModel->cadastrar(
                    $id_usuario,$id_conta, 
                    $id_categoria,$desc_final, 
                    $valor_parcela,$data_lancamento, 
                    'Saida', 
                    $forma_pagamento,
                    null,
                    $id_fatura_lancamento
                );
                
                if ($forma_pagamento == 'Crédito' and $id_fatura_lancamento) {
                    $faturaModel->atualizarValorTotal($id_fatura_lancamento);
                }
            }

            // 6. Confirmação
            $msgSucesso = "✅ *Despesa Registrada!*\n\n";
            $msgSucesso .= "💰 *Valor:* R$ " . number_format($valor_total, 2, ',', '.') . ($parcelas > 1 ? " (em {$parcelas}x de R$ " . number_format($valor_parcela, 2, ',', '.') . ")" : "") . "\n";
            $msgSucesso .= "💳 *Tipo:* " . $forma_pagamento . "\n";
            $msgSucesso .= "🏷️ *Categoria:* " . htmlspecialchars($nome_categoria_ia) . "\n";
            $msgSucesso .= "🏦 *Origem:* " . htmlspecialchars($nome_destino_final) . "\n";
            
            $this->enviarMensagemTelegram($chat_id,$msgSucesso);

            http_response_code(200); echo json_encode(["status" => "sucesso"]);
            
        } catch (Exception $e) {
            if (isset($chat_id)) $this->enviarMensagemTelegram($chat_id, "❌ *Erro interno:* Não foi possível processar sua despesa no momento.");
            http_response_code(500); echo json_encode(["erro_interno" => $e->getMessage()]);
        }
        exit;
    }

    private function enviarMensagemTelegram($chat_id,$mensagem) {
        $token =$_ENV['TELEGRAM_BOT_TOKEN'] ?? getenv('TELEGRAM_BOT_TOKEN');
        if (!$token) return false;
        
        $url = "https://api.telegram.org/bot" . $token . "/sendMessage";
        $dados = ['chat_id' => $chat_id, 'text' =>$mensagem, 'parse_mode' => 'Markdown'];
        $opcoes = ['http' => ['header' => "Content-type: application/x-www-form-urlencoded\r\n", 'method' => 'POST', 'content' => http_build_query($dados)]];
        file_get_contents($url, false, stream_context_create($opcoes));
    }
}
?>