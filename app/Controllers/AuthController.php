<?php
require_once BASE_PATH . '/core/Controller.php';

class AuthController extends Controller
{

    private function initCsrfToken()
    {
        // Gera um token criptograficamente seguro para proteger contra ataques CSRF
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function validateCsrfToken()
    {
        // Verifica se o token CSRF enviado no formulário corresponde ao token armazenado na sessão
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            return false;
        }
        return true;
    }

    public function login()
    {
        $erro = "";
        $csrf_token = $this->initCsrfToken(); // Inicializa o token para mandar para a View

        if (isset($_SESSION['id_usuario'])) {
            header("Location: /financas");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!$this->validateCsrfToken()) {
                $erro = "Token de segurança inválido.";
            } else {
                $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

                if (!$email) {
                    $erro = "E-mail inválido.";
                } else {
                    $usuarioModel = $this->model('Usuario');
                    $usuario = $usuarioModel->buscarPorEmail($_POST['email']);

                    if ($usuario && password_verify($_POST['senha'], $usuario['senha'])) {
                        
                        session_regenerate_id(true); // Regenera o ID da sessão para evitar fixação de sessão

                        $_SESSION['id_usuario'] = $usuario['id_usuario'];
                        $_SESSION['perfil'] = $usuario['perfil'];
                        $_SESSION['nome'] = $usuario['nome'];
                        header("Location: /financas");
                        exit;
                    } else {
                        $erro = "E-mail ou senha inválidos.";
                    }
                }
            }
        }

        $this->view('auth/login', [
            'titulo' => 'Acessar Sistema',
            'erro' => $erro,
            'csrf_token' => $csrf_token // Passa o token para a View
        ]);
    }

    public function registro()
    {
        $erro = "";
        $sucesso = "";
        $csrf_token = $this->initCsrfToken(); // Inicializa o token para mandar para a View

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!$this->validateCsrfToken()) {
                $erro = "Token de segurança inválido.";
            } else {
                $nome = trim($_POST['nome']);

                $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                $senha = trim($_POST['senha']);
    
                $aceitou_termos = isset($_POST['termos']) ? 1 : 0;
                $data_aceite_termos = date('Y-m-d H:i:s');
    
                if (!$email) {
                    $erro = "E-mail inválido.";
                } elseif (!empty($nome) && !empty($senha)) {
                    $usuarioModel = $this->model('Usuario');
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    
                    // O perfil padrão sempre será 'comum' no cadastro público
                    if ($usuarioModel->cadastrar($nome, $email, $senhaHash, $aceitou_termos, $data_aceite_termos, 'comum')) {
                        $novo_usuario = $usuarioModel->buscarPorEmail($email);

                        session_regenerate_id(true); // Regenera o ID da sessão para evitar fixação de sessão
    
                        $_SESSION['id_usuario'] = $novo_usuario['id_usuario'];
                        $_SESSION['perfil'] = $novo_usuario['perfil'];
                        $_SESSION['nome'] = $novo_usuario['nome'];
    
                        header("Location: /financas");
                        exit;
                    } else {
                        $erro = "Este e-mail já está cadastrado.";
                    }
                } else {
                    $erro = "Preencha todos os campos.";
                }

            }

        }

        $this->view('auth/registro', [
            'titulo' => 'Criar Conta',
            'erro' => $erro,
            'sucesso' => $sucesso,
            'csrf_token' => $csrf_token // Passa o token para a View
        ]);
    }

    public function logout()
    {
        session_destroy();
        header("Location: /financas/auth/login");
        exit;
    }
}
