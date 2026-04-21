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
            $id_usuario = $_SESSION['id_usuario'] ?? 7; 
            
            $limite_mensal = !empty($_POST['limite_mensal']) ? $_POST['limite_mensal'] : null;
            
            $categoriaModel->cadastrar($id_usuario, $_POST['nome_categoria'], $_POST['tipo'], $limite_mensal);
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
            
            $limite_mensal = !empty($_POST['limite_mensal']) ? $_POST['limite_mensal'] : null;

            $categoriaModel->atualizar($id, $_POST['nome_categoria'], $_POST['tipo'], $limite_mensal);
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