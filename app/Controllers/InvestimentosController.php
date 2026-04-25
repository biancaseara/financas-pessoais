<?php
require_once BASE_PATH . '/core/Controller.php';

class InvestimentosController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }
    }

    public function index() {
        $investimentoModel = $this->model('Investimento');
        $investimentos = $investimentoModel->listarTodos($_SESSION['id_usuario']);
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->view('investimentos/index', [
            'titulo' => 'Meus Investimentos',
            'investimentos' => $investimentos,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $investimentoModel = $this->model('Investimento');
            $vencimento = !empty($_POST['vencimento']) ? $_POST['vencimento'] : null;

            $investimentoModel->cadastrar(
                $_SESSION['id_usuario'], 
                strip_tags(trim($_POST['nome_investimento'])), 
                $_POST['tipo'], 
                strip_tags(trim($_POST['corretora'])),
                $_POST['valor_aplicado'],
                $_POST['data_aplicacao'],
                $vencimento
            );
            header("Location: /financas/investimentos");
        }
    }

    public function edit($id) {
        $investimentoModel = $this->model('Investimento');
        $investimento = $investimentoModel->buscarPorId($id, $_SESSION['id_usuario']);

        if ($investimento) {
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            $this->view('investimentos/edit', [
                'titulo' => 'Atualizar Investimento',
                'investimento' => $investimento,
                'csrf_token' => $_SESSION['csrf_token']
            ]);
        } else {
            header("Location: /financas/investimentos");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $investimentoModel = $this->model('Investimento');
            $vencimento = !empty($_POST['vencimento']) ? $_POST['vencimento'] : null;

            $investimentoModel->atualizar(
                $id,
                $_SESSION['id_usuario'], 
                strip_tags(trim($_POST['nome_investimento'])), 
                $_POST['tipo'], 
                strip_tags(trim($_POST['corretora'])),
                $_POST['valor_aplicado'],
                $_POST['data_aplicacao'],
                $vencimento
            );
            header("Location: /financas/investimentos");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $investimentoModel = $this->model('Investimento');
            $investimentoModel->deletar($id, $_SESSION['id_usuario']);
            header("Location: /financas/investimentos");
        }
    }
}