<?php
require_once BASE_PATH . '/core/Controller.php';

class ContasController extends Controller {

    public function index() {
        $contaModel = $this->model('Conta');
        $contas = $contaModel->listarTodos();
        
        $this->view('contas/index', [
            'titulo' => 'Minhas Contas',
            'contas' => $contas
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $contaModel = $this->model('Conta');
            $id_usuario = $_SESSION['id_usuario'] ?? 7; 
            
            $contaModel->cadastrar($id_usuario, $_POST['nome_banco'], $_POST['saldo_inicial'], $_POST['cor_identificacao']);
            header("Location: /financas/contas");
        }
    }

    public function edit($id) {
        $contaModel = $this->model('Conta');
        $conta = $contaModel->buscarPorId($id);

        if ($conta) {
            $this->view('contas/edit', [
                'titulo' => 'Editar Conta',
                'conta' => $conta
            ]);
        } else {
            header("Location: /financas/contas");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $contaModel = $this->model('Conta');
            $contaModel->atualizar($id, $_POST['nome_banco'], $_POST['saldo_inicial'], $_POST['cor_identificacao']);
            header("Location: /financas/contas");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $contaModel = $this->model('Conta');
            $contaModel->deletar($id);
            header("Location: /financas/contas");
        }
    }
}