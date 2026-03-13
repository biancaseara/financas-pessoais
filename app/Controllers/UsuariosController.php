<?php
require_once BASE_PATH . '/core/Controller.php';

class UsuariosController extends Controller {

    public function __construct() {
        // Proteção total: Se não for admin, bloqueia a página inteira na hora
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'admin') {
            die("<div style='text-align:center; margin-top:50px;'><h2 style='color:red;'>🛑 Acesso Negado</h2><p>Apenas administradores podem gerenciar usuários.</p><a href='/financas'>Voltar ao Dashboard</a></div>");
        }
    }

    public function index() {
        $usuarioModel = $this->model('Usuario');
        
        $this->view('usuarios/index', [
            'titulo' => 'Gestão de Usuários',
            'usuarios' => $usuarioModel->listarTodos()
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuarioModel = $this->model('Usuario');
            $senhaHash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
            
            $usuarioModel->cadastrar($_POST['nome'], $_POST['email'], $senhaHash, $_POST['perfil']);
            header("Location: /financas/usuarios");
        }
    }

    public function edit($id) {
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->buscarPorId($id);

        if ($usuario) {
            $this->view('usuarios/edit', [
                'titulo' => 'Editar Usuário',
                'usuario' => $usuario
            ]);
        } else {
            header("Location: /financas/usuarios");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuarioModel = $this->model('Usuario');
            $usuarioModel->atualizar($id, $_POST['nome'], $_POST['email'], $_POST['senha'], $_POST['perfil']);
            header("Location: /financas/usuarios");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($id == $_SESSION['id_usuario']) {
                die("Você não pode excluir a sua própria conta."); 
            }
            $usuarioModel = $this->model('Usuario');
            $usuarioModel->deletar($id);
            header("Location: /financas/usuarios");
        }
    }
}