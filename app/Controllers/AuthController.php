<?php
require_once BASE_PATH . '/core/Controller.php';

class AuthController extends Controller {

    public function login() {
        $erro = "";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuarioModel = $this->model('Usuario');
            $usuario = $usuarioModel->buscarPorEmail($_POST['email']);

            if ($usuario && password_verify($_POST['senha'], $usuario['senha'])) {
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['perfil'] = $usuario['perfil'];
                header("Location: /financas");
                exit;
            } else {
                $erro = "E-mail ou senha inválidos.";
            }
        }

        $this->view('auth/login', [
            'titulo' => 'Acessar Sistema',
            'erro' => $erro
        ]);
    }

    public function registro() {
        $erro = "";
        $sucesso = "";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nome = trim($_POST['nome']);
            $email = trim($_POST['email']);
            $senha = trim($_POST['senha']);

            if (!empty($nome) && !empty($email) && !empty($senha)) {
                $usuarioModel = $this->model('Usuario');
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                // O perfil padrão sempre será 'comum' no registro público
                if ($usuarioModel->cadastrar($nome, $email, $senhaHash, 'comum')) {
                    $sucesso = "Conta criada com sucesso! Você já pode fazer login.";
                } else {
                    $erro = "Este e-mail já está cadastrado.";
                }
            } else {
                $erro = "Preencha todos os campos.";
            }
        }

        $this->view('auth/registro', [
            'titulo' => 'Criar Conta',
            'erro' => $erro,
            'sucesso' => $sucesso
        ]);
    }

    public function logout() {
        session_destroy();
        header("Location: /financas/auth/login");
        exit;
    }
}