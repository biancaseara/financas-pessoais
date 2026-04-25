<?php
require_once BASE_PATH . '/core/Controller.php';

class MetasController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }
    }

    public function index() {
        $metaModel = $this->model('Meta');
        $id_usuario = $_SESSION['id_usuario'];

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $metas = $metaModel->listarTodos($id_usuario);
        
        $this->view('metas/index', [
            'titulo' => 'Meus Objetivos',
            'metas' => $metas,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $metaModel = $this->model('Meta');
            $id_usuario = $_SESSION['id_usuario']; 
            
            $metaModel->cadastrar(
                $id_usuario, 
                strip_tags(trim($_POST['titulo_meta'])), 
                $_POST['valor_objetivo'], 
                $_POST['valor_atual'], 
                $_POST['data_limite']
            );
            header("Location: /financas/metas");
        }
    }

    public function edit($id) {
        $metaModel = $this->model('Meta');
        $id_usuario = $_SESSION['id_usuario'];
        $meta = $metaModel->buscarPorId($id, $id_usuario);

        if ($meta) {
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            $this->view('metas/edit', [
                'titulo' => 'Atualizar Meta',
                'meta' => $meta,
                'csrf_token' => $_SESSION['csrf_token']
            ]);
        } else {
            header("Location: /financas/metas");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $metaModel = $this->model('Meta');
            $id_usuario = $_SESSION['id_usuario'];

            $metaModel->atualizar(
                $id, 
                $id_usuario,
                strip_tags(trim($_POST['titulo_meta'])), 
                $_POST['valor_objetivo'], 
                $_POST['valor_atual'], 
                $_POST['data_limite']
            );
            header("Location: /financas/metas");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $metaModel = $this->model('Meta');
            $id_usuario = $_SESSION['id_usuario'];

            $metaModel->deletar($id, $id_usuario);
            header("Location: /financas/metas");
        }
    }
}