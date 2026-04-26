<?php
require_once BASE_PATH . '/core/Controller.php';

class UsuariosController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'admin') {
            die("<div style='text-align:center; margin-top:50px;'><h2 style='color:red;'>🛑 Acesso Negado</h2><p>Apenas administradores podem gerenciar usuários.</p><a href='/financas'>Voltar ao Dashboard</a></div>");
        }
    }

    public function index() {
        $usuarioModel = $this->model('Usuario');
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->view('usuarios/index', [
            'titulo' => 'Gestão de Usuários',
            'usuarios' => $usuarioModel->listarTodos(),
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $usuarioModel = $this->model('Usuario');
            $senhaHash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
            
            $usuarioModel->cadastrar($_POST['nome'], $_POST['email'], $senhaHash, 1, date('Y-m-d H:i:s'), $_POST['perfil']);
            header("Location: /financas/usuarios");
        }
    }

    public function edit($id) {
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->buscarPorId($id);

        if ($usuario) {
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            $this->view('usuarios/edit', [
                'titulo' => 'Editar Usuário',
                'usuario' => $usuario,
                'csrf_token' => $_SESSION['csrf_token']
            ]);
        } else {
            header("Location: /financas/usuarios");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $usuarioModel = $this->model('Usuario');
            
            $senhaHash = !empty($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : '';
            
            $usuarioModel->atualizar($id, $_POST['nome'], $_POST['email'], $senhaHash, $_POST['perfil']);
            header("Location: /financas/usuarios");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            if ($id == $_SESSION['id_usuario']) {
                die("Você não pode excluir a sua própria conta."); 
            }
            $usuarioModel = $this->model('Usuario');
            $usuarioModel->deletar($id);
            header("Location: /financas/usuarios");
        }
    }
}