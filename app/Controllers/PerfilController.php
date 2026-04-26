<?php
require_once BASE_PATH . '/core/Controller.php';

class PerfilController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }
    }

    public function index() {
        $usuarioModel = $this->model('Usuario');
        $meusDados = $usuarioModel->buscarPorId($_SESSION['id_usuario']);

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->view('perfil/index', [
            'titulo' => 'Meu Perfil',
            'usuario' => $meusDados,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada no perfil.");
            }

            $usuarioModel = $this->model('Usuario');
            
            $id_logado = $_SESSION['id_usuario'];
            $perfil_atual = $_SESSION['perfil'];
            
            $usuarioModel->atualizar($id_logado, $_POST['nome'], $_POST['email'], $_POST['senha'], $perfil_atual);
            
            $_SESSION['nome'] = $_POST['nome'];

            header("Location: /financas/perfil?sucesso=1");
        }
    }
}