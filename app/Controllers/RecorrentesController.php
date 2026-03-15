<?php
require_once BASE_PATH . '/core/Controller.php';

class RecorrentesController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }
    }

    public function index() {
        $recorrenteModel = $this->model('DespesaRecorrente');
        $contaModel = $this->model('Conta');
        $categoriaModel = $this->model('Categoria');

        $this->view('recorrentes/index', [
            'titulo' => 'Despesas Fixas e Assinaturas',
            'recorrentes' => $recorrenteModel->listarTodos($_SESSION['id_usuario']),
            'contas' => $contaModel->listarTodos(),
            'categorias' => $categoriaModel->listarTodos()
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recorrenteModel = $this->model('DespesaRecorrente');
            $recorrenteModel->cadastrar(
                $_SESSION['id_usuario'],
                $_POST['id_conta'],
                $_POST['id_categoria'],
                $_POST['descricao'],
                $_POST['valor'],
                $_POST['dia_vencimento']
            );
            header("Location: /financas/recorrentes");
        }
    }

    public function edit($id) {
        $recorrenteModel = $this->model('DespesaRecorrente');
        $recorrente = $recorrenteModel->buscarPorId($id, $_SESSION['id_usuario']);

        if ($recorrente) {
            $contaModel = $this->model('Conta');
            $categoriaModel = $this->model('Categoria');

            $this->view('recorrentes/edit', [
                'titulo' => 'Editar Despesa Fixa',
                'recorrente' => $recorrente,
                'contas' => $contaModel->listarTodos(),
                'categorias' => $categoriaModel->listarTodos()
            ]);
        } else {
            header("Location: /financas/recorrentes");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recorrenteModel = $this->model('DespesaRecorrente');
            $recorrenteModel->atualizar(
                $id,
                $_SESSION['id_usuario'],
                $_POST['id_conta'],
                $_POST['id_categoria'],
                $_POST['descricao'],
                $_POST['valor'],
                $_POST['dia_vencimento'],
                $_POST['status']
            );
            header("Location: /financas/recorrentes");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recorrenteModel = $this->model('DespesaRecorrente');
            $recorrenteModel->deletar($id, $_SESSION['id_usuario']);
            header("Location: /financas/recorrentes");
        }
    }
}