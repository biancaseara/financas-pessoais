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
        
        $this->view('investimentos/index', [
            'titulo' => 'Meus Investimentos',
            'investimentos' => $investimentos
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $investimentoModel = $this->model('Investimento');
            
            $vencimento = !empty($_POST['vencimento']) ? $_POST['vencimento'] : null;

            $investimentoModel->cadastrar(
                $_SESSION['id_usuario'], 
                $_POST['nome_investimento'], 
                $_POST['tipo'], 
                $_POST['corretora'],
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
            $this->view('investimentos/edit', [
                'titulo' => 'Atualizar Investimento',
                'investimento' => $investimento
            ]);
        } else {
            header("Location: /financas/investimentos");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $investimentoModel = $this->model('Investimento');
            
            $vencimento = !empty($_POST['vencimento']) ? $_POST['vencimento'] : null;

            $investimentoModel->atualizar(
                $id,
                $_SESSION['id_usuario'], 
                $_POST['nome_investimento'], 
                $_POST['tipo'], 
                $_POST['corretora'],
                $_POST['valor_aplicado'],
                $_POST['data_aplicacao'],
                $vencimento
            );
            header("Location: /financas/investimentos");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $investimentoModel = $this->model('Investimento');
            $investimentoModel->deletar($id, $_SESSION['id_usuario']);
            header("Location: /financas/investimentos");
        }
    }
}