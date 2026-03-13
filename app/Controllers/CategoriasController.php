<?php
require_once BASE_PATH . '/core/Controller.php';

class CategoriasController extends Controller {

    public function index() {
        $categoriaModel = $this->model('Categoria');
        $categorias = $categoriaModel->listarTodos();
        
        $this->view('categorias/index', [
            'titulo' => 'Minhas Categorias',
            'categorias' => $categorias
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $categoriaModel = $this->model('Categoria');
            // Como ainda não temos o login 100% isolado, vamos usar o ID da sessão
            $id_usuario = $_SESSION['id_usuario'] ?? 7; 
            
            $categoriaModel->cadastrar($id_usuario, $_POST['nome_categoria'], $_POST['tipo']);
            header("Location: /financas/categorias");
        }
    }

    public function edit($id) {
        $categoriaModel = $this->model('Categoria');
        $categoria = $categoriaModel->buscarPorId($id);

        if ($categoria) {
            $this->view('categorias/edit', [
                'titulo' => 'Editar Categoria',
                'categoria' => $categoria
            ]);
        } else {
            header("Location: /financas/categorias");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $categoriaModel = $this->model('Categoria');
            $categoriaModel->atualizar($id, $_POST['nome_categoria'], $_POST['tipo']);
            header("Location: /financas/categorias");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $categoriaModel = $this->model('Categoria');
            $categoriaModel->deletar($id);
            header("Location: /financas/categorias");
        }
    }
}