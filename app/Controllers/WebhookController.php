<?php
require_once BASE_PATH . '/core/Controller.php';

class WebhookController extends Controller {

    public function telegram() {
        try {
            $conteudoJson = file_get_contents("php://input");
            $payload = json_decode($conteudoJson, true);

            if (!$payload || !isset($payload['chat_id']) || !isset($payload['dados_ia'])) {
                http_response_code(400);
                echo json_encode(["erro" => "Payload invalido ou dados ausentes"]);
                exit;
            }

            $chat_id = $payload['chat_id'];
            
            $dados = is_array($payload['dados_ia']) ? $payload['dados_ia'] : json_decode($payload['dados_ia'], true);

            $db = new Database();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE chat_id_telegram = ?");
            $stmt->execute([$chat_id]);
            $usuario = $stmt->fetch();

            if (!$usuario) {
                http_response_code(403);
                echo json_encode(["erro" => "Usuario nao encontrado pelo chat_id"]);
                exit;
            }
            $id_usuario = $usuario['id_usuario'];

            $data = $dados['data'] ?? date('Y-m-d');
            if (strpos($data, '/') !== false) {
                $data = implode('-', array_reverse(explode('/', $data)));
            }
            
            $forma_pagamento = $dados['forma_pagamento'] ?? 'Outros';
            $nome_categoria_ia = $dados['categoria'] ?? 'Outros';
            
            $valor_bruto = $dados['valor'] ?? 0;
            if (is_string($valor_bruto)) {
                $valor_bruto = str_replace('.', '', $valor_bruto);
                $valor_bruto = str_replace(',', '.', $valor_bruto);
            }
            $valor = (float) $valor_bruto;
            
            $descricao = $dados['descricao'] ?? 'Gasto via Telegram';
            $tipo_transacao = 'Saida';
            $tipo_categoria = 'Despesa';

            $categoriaModel = $this->model('Categoria');
            $categorias = $categoriaModel->listarTodos($id_usuario);
            $id_categoria = null;

            foreach ($categorias as $cat) {
                if (strtolower(trim($cat['nome_categoria'])) == strtolower(trim($nome_categoria_ia))) {
                    $id_categoria = $cat['id_categoria'];
                    break;
                }
            }

            if (!$id_categoria) {
                $categoriaModel->cadastrar($id_usuario, $nome_categoria_ia, $tipo_categoria, null);
                $id_categoria = $pdo->lastInsertId();
            }

            $contaModel = $this->model('Conta');
            $contas = $contaModel->listarTodos($id_usuario);
            
            if (empty($contas)) {
                http_response_code(400);
                echo json_encode(["erro" => "O usuario precisa de pelo menos uma conta cadastrada"]);
                exit;
            }
            $id_conta = $contas[0]['id_conta'];

            $transacaoModel = $this->model('Transacao');
            $transacaoModel->cadastrar(
                $id_usuario, $id_conta, $id_categoria, $descricao, $valor, 
                $data, $tipo_transacao, $forma_pagamento, null, null
            );
            
            http_response_code(200);
            echo json_encode(["status" => "sucesso"]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["erro_interno" => $e->getMessage(), "linha" => $e->getLine()]);
        }
        exit;
    }
}