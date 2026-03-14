<?php
require_once BASE_PATH . '/core/Controller.php';

class TransacoesController extends Controller {

    public function index() {
        $transacaoModel = $this->model('Transacao');
        $contaModel = $this->model('Conta');
        $categoriaModel = $this->model('Categoria');

        $this->view('transacoes/index', [
            'titulo' => 'Extrato de Transações',
            'transacoes' => $transacaoModel->listarTodos(),
            'contas' => $contaModel->listarTodos(),
            'categorias' => $categoriaModel->listarTodos()
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $transacaoModel = $this->model('Transacao');
            
            $id_conta_destino = !empty($_POST['id_conta_destino']) ? $_POST['id_conta_destino'] : null;
            
            // Sem acento na palavra Transferencia
            $id_categoria = ($_POST['tipo_transacao'] == 'Transferencia') ? null : $_POST['id_categoria'];

            $transacaoModel->cadastrar(
                $_POST['id_conta'], 
                $id_categoria, 
                $_POST['descricao'], 
                $_POST['valor'], 
                $_POST['data_transacao'], 
                $_POST['tipo_transacao'],
                $id_conta_destino
            );
            header("Location: /financas/transacoes");
        }
    }

    public function edit($id) {
        $transacaoModel = $this->model('Transacao');
        $transacao = $transacaoModel->buscarPorId($id);

        if ($transacao) {
            $contaModel = $this->model('Conta');
            $categoriaModel = $this->model('Categoria');

            $this->view('transacoes/edit', [
                'titulo' => 'Editar Transação',
                'transacao' => $transacao,
                'contas' => $contaModel->listarTodos(),
                'categorias' => $categoriaModel->listarTodos()
            ]);
        } else {
            header("Location: /financas/transacoes");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $transacaoModel = $this->model('Transacao');
            
            $id_conta_destino = !empty($_POST['id_conta_destino']) ? $_POST['id_conta_destino'] : null;
            
            // Sem acento na palavra Transferencia
            $id_categoria = ($_POST['tipo_transacao'] == 'Transferencia') ? null : $_POST['id_categoria'];

            $transacaoModel->atualizar(
                $id,
                $_POST['id_conta'], 
                $id_categoria, 
                $_POST['descricao'], 
                $_POST['valor'], 
                $_POST['data_transacao'], 
                $_POST['tipo_transacao'],
                $id_conta_destino
            );
            header("Location: /financas/transacoes");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $transacaoModel = $this->model('Transacao');
            $transacaoModel->deletar($id);
            header("Location: /financas/transacoes");
        }
    }
}