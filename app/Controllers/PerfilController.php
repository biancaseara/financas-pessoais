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
        // Busca APENAS os dados do usuário que está logado
        $meusDados = $usuarioModel->buscarPorId($_SESSION['id_usuario']);

        $this->view('perfil/index', [
            'titulo' => 'Meu Perfil',
            'usuario' => $meusDados
        ]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuarioModel = $this->model('Usuario');
            
            // Força a atualização APENAS no ID da sessão atual
            $id_logado = $_SESSION['id_usuario'];
            $perfil_atual = $_SESSION['perfil']; // Impede que a pessoa mude o próprio nível de acesso
            
            $usuarioModel->atualizar($id_logado, $_POST['nome'], $_POST['email'], $_POST['senha'], $perfil_atual);
            
            // Atualiza o nome na sessão caso a pessoa tenha mudado de nome
            $_SESSION['nome'] = $_POST['nome'];

            header("Location: /financas/perfil?sucesso=1");
        }
    }
}