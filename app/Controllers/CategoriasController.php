<?php
require_once BASE_PATH . '/core/Controller.php';

class CategoriasController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }
    }

    public function index() {
        $categoriaModel = $this->model('Categoria');
        $id_usuario = $_SESSION['id_usuario'];

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $categorias = $categoriaModel->listarTodos($id_usuario);
        
        $this->view('categorias/index', [
            'titulo' => 'Minhas Categorias',
            'categorias' => $categorias,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $categoriaModel = $this->model('Categoria');
            $id_usuario = $_SESSION['id_usuario']; 
            
            $limite = $_POST['limite_mensal'] ?? null;
            if (!empty($limite)) {
                $limite = str_replace('.', '', $limite);
                $limite = str_replace(',', '.', $limite);
                $limite = (float) $limite;
            } else {
                $limite = null;
            }

            $categoriaModel->cadastrar($id_usuario, $_POST['nome_categoria'], $_POST['tipo'], $limite);
            header("Location: /financas/categorias");
        }
    }

    public function edit($id) {
        $categoriaModel = $this->model('Categoria');
        $id_usuario = $_SESSION['id_usuario'];
        
        $categoria = $categoriaModel->buscarPorId($id, $id_usuario);

        if ($categoria) {
            $this->view('categorias/edit', [
                'titulo' => 'Editar Categoria',
                'categoria' => $categoria,
                'csrf_token' => $_SESSION['csrf_token']
            ]);
        } else {
            header("Location: /financas/categorias");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF.");
            }

            $categoriaModel = $this->model('Categoria');
            $id_usuario = $_SESSION['id_usuario'];

            $limite = $_POST['limite_mensal'] ?? null;
            if (!empty($limite)) {
                $limite = str_replace('.', '', $limite);
                $limite = str_replace(',', '.', $limite);
                $limite = (float) $limite;
            } else {
                $limite = null;
            }

            $categoriaModel->atualizar($id, $id_usuario, $_POST['nome_categoria'], $_POST['tipo'], $limite);
            header("Location: /financas/categorias");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF.");
            }

            $categoriaModel = $this->model('Categoria');
            $id_usuario = $_SESSION['id_usuario'];
            $categoriaModel->deletar($id, $id_usuario);
            header("Location: /financas/categorias");
        }
    }
}
