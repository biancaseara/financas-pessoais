<?php
require_once BASE_PATH . '/core/Controller.php';

class TransacoesController extends Controller
{

    public function __construct(){
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }
    }

    public function index() {
        $transacaoModel = $this->model('Transacao');
        $contaModel = $this->model('Conta');
        $categoriaModel = $this->model('Categoria');
        $cartaoModel = $this->model('Cartao');
        $id_usuario = $_SESSION['id_usuario'];

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->view('transacoes/index', [
            'titulo' => 'Extrato de Transações',
            'transacoes' => $transacaoModel->listarTodos($id_usuario),
            'contas' => $contaModel->listarTodos($id_usuario),
            'categorias' => $categoriaModel->listarTodos($id_usuario),
            'cartoes' => $cartaoModel->listarTodos($id_usuario),
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $transacaoModel = $this->model('Transacao');
            $faturaModel = $this->model('Fatura');
            $id_usuario = $_SESSION['id_usuario'];
            
            $id_conta_destino = !empty($_POST['id_conta_destino']) ? $_POST['id_conta_destino'] : null;
            $id_categoria = ($_POST['tipo_transacao'] == 'Transferencia') ? null : $_POST['id_categoria'];
            $tipo_transacao = $_POST['tipo_transacao'];
            
            $forma_pagamento = $_POST['forma_pagamento'] ?? 'Outros';
            $is_credito = ($forma_pagamento === 'Crédito');
            $id_conta = (!$is_credito) ? $_POST['id_conta'] : null;

            $valor = $_POST['valor'];
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
            $valor = (float) $valor;

            $descricao = strip_tags(trim($_POST['descricao']));

            if ($is_credito && !empty($_POST['id_cartao'])) {
                $parcelas = (int) ($_POST['parcelas'] ?? 1);
                $valor_parcela = $valor / $parcelas;

                for ($i = 0; $i < $parcelas; $i++) {
                    $novaData = date('Y-m-d', strtotime("+$i month", strtotime($_POST['data_transacao'])));
                    $dataFatura = date('Y-m', strtotime($novaData));
                    
                    $id_fatura = $faturaModel->buscarOuCriarAberta($_POST['id_cartao'], $dataFatura);

                    $desc_parcelada = $descricao;
                    if ($parcelas > 1) {
                        $num_parcela = $i + 1;
                        $desc_parcelada .= " ({$num_parcela}/{$parcelas})";
                    }

                    $transacaoModel->cadastrar(
                        $id_usuario, null, $id_categoria, $desc_parcelada, $valor_parcela, 
                        $novaData, $tipo_transacao, $forma_pagamento, null, $id_fatura
                    );
                    $faturaModel->atualizarValorTotal($id_fatura);
                }

            } else {
                $transacaoModel->cadastrar(
                    $id_usuario, $id_conta, $id_categoria, $descricao, $valor, 
                    $_POST['data_transacao'], $tipo_transacao, $forma_pagamento, $id_conta_destino, null
                );
            }

            header("Location: /financas/transacoes");
        }
    }

    public function edit($id) {
        $transacaoModel = $this->model('Transacao');
        $id_usuario = $_SESSION['id_usuario'];

        $transacao = $transacaoModel->buscarPorId($id, $id_usuario);

        if ($transacao) {
            $contaModel = $this->model('Conta');
            $categoriaModel = $this->model('Categoria');

            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            $this->view('transacoes/edit', [
                'titulo' => 'Editar Transação',
                'transacao' => $transacao,
                'contas' => $contaModel->listarTodos($id_usuario),
                'categorias' => $categoriaModel->listarTodos($id_usuario),
                'csrf_token' => $_SESSION['csrf_token']
            ]);
        } else {
            header("Location: /financas/transacoes");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $transacaoModel = $this->model('Transacao');
            $id_usuario = $_SESSION['id_usuario'];
            
            $id_conta_destino = !empty($_POST['id_conta_destino']) ? $_POST['id_conta_destino'] : null;
            $id_categoria = ($_POST['tipo_transacao'] == 'Transferencia') ? null : $_POST['id_categoria'];
            $tipo_transacao = $_POST['tipo_transacao'];
            $id_conta = $_POST['id_conta'] ?? null;
            
            $forma_pagamento = $_POST['forma_pagamento'] ?? 'Outros';

            $valor = $_POST['valor'];
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
            $valor = (float) $valor;

            $descricao = strip_tags(trim($_POST['descricao']));

            $transacaoModel->atualizar(
                $id, $id_usuario, $id_conta, $id_categoria, $descricao, $valor, 
                $_POST['data_transacao'], $tipo_transacao, $forma_pagamento, $id_conta_destino
            );
            header("Location: /financas/transacoes");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $transacaoModel = $this->model('Transacao');
            $id_usuario = $_SESSION['id_usuario'];
            
            $transacaoModel->deletar($id, $id_usuario);
            header("Location: /financas/transacoes");
        }
    }

    public function pagarFatura($id_fatura) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha CSRF.");
            }

            $faturaModel = $this->model('Fatura');
            $transacaoModel = $this->model('Transacao');
            $id_usuario = $_SESSION['id_usuario'];
            $id_conta = $_POST['id_conta_pagamento']; 

            $fatura = $faturaModel->buscarPorId($id_fatura, $id_usuario);

            if ($fatura && $fatura['status'] !== 'Paga') {
                try {
                    $db = new Database();
                    $pdo = $db->getConnection();
                    $pdo->beginTransaction();

                    $faturaModel->mudarStatus($id_fatura, $id_usuario, 'Paga');

                    $descricao = "Pagamento Fatura: " . $fatura['nome_cartao'] . " (" . $fatura['mes_ano'] . ")";
                    $transacaoModel->cadastrar($id_usuario,$id_conta, null, $descricao, $fatura['valor_total'], date('Y-m-d'), 'Saida', null, null);

                    $pdo->commit();
                    header("Location: /financas/cartoes");
                } catch (Exception $e) {
                    if (isset($pdo)) $pdo->rollBack();
                    throw new Exception("Erro ao pagar fatura: " . $e->getMessage());
                }
            }
        }
    }
}