<?php
require_once BASE_PATH . '/core/Controller.php';

class AuthController extends Controller
{

    private function initCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function validateCsrfToken()
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            return false;
        }
        return true;
    }

    public function login()
    {
        $erro = "";
        $csrf_token = $this->initCsrfToken(); 
        
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
                        
                        session_regenerate_id(true);

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
            'csrf_token' => $csrf_token
        ]);
    }

    public function registro()
    {
        $erro = "";
        $sucesso = "";
        $csrf_token = $this->initCsrfToken();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!$this->validateCsrfToken()) {
                $erro = "Token de segurança inválido.";
            } else {
                $nome = trim($_POST['nome']);
                $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                $senha = trim($_POST['senha']);
                $senha_confirmacao = trim($_POST['senha_confirmacao'] ?? '');
    
                $aceitou_termos = isset($_POST['termos']) ? 1 : 0;
                $data_aceite_termos = date('Y-m-d H:i:s');
    
                if (!$email) {
                    $erro = "E-mail inválido.";
                } elseif (empty($nome) || empty($senha)) {
                    $erro = "Preencha todos os campos.";
                } elseif ($senha !== $senha_confirmacao) {
                    $erro = "As senhas digitadas não coincidem.";
                } elseif (strlen($senha) < 8 || !preg_match('/[A-Za-z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
                    $erro = "A senha deve ter pelo menos 8 caracteres, incluindo letras e números.";
                } else {
                    $usuarioModel = $this->model('Usuario');
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    
                    if ($usuarioModel->cadastrar($nome, $email, $senhaHash, $aceitou_termos, $data_aceite_termos, 'comum')) {
                        $novo_usuario = $usuarioModel->buscarPorEmail($email);
                        $id_novo_usuario = $novo_usuario['id_usuario'];

                        $db = new Database();
                        $pdo = $db->getConnection();

                        $sqlConta = "INSERT INTO contas (id_usuario, nome_banco, saldo_inicial, cor_identificacao) VALUES (?, 'Carteira Principal', 0.00, '#8b5cf6')";
                        $pdo->prepare($sqlConta)->execute([$id_novo_usuario]);

                        $categoriasPadrao = [
                            ['Alimentação', 'D'],
                            ['Moradia', 'D'],
                            ['Transporte', 'D'],
                            ['Saúde', 'D'],
                            ['Renda Principal', 'R'],
                            ['Reserva e Investimentos', 'D'],
                            ['Assinaturas e Streaming', 'D'],
                            ['Internet e Telefonia', 'D'],
                        ];

                        $sqlCategoria = "INSERT INTO categorias (id_usuario, nome_categoria, tipo) VALUES (?, ?, ?)";
                        $stmtCat = $pdo->prepare($sqlCategoria);

                        foreach ($categoriasPadrao as $cat) {
                            $stmtCat->execute([$id_novo_usuario, $cat[0], $cat[1]]);
                        }

                        session_regenerate_id(true);
    
                        $_SESSION['id_usuario'] = $id_novo_usuario;
                        $_SESSION['perfil'] = $novo_usuario['perfil'];
                        $_SESSION['nome'] = $novo_usuario['nome'];
    
                        header("Location: /financas");
                        exit;
                    } else {
                        $erro = "Este e-mail já está cadastrado.";
                    }
                }
            }
        }

        $this->view('auth/registro', [
            'titulo' => 'Criar Conta',
            'erro' => $erro,
            'sucesso' => $sucesso,
            'csrf_token' => $csrf_token
        ]);
    }

    public function logout()
    {
        session_destroy();
        header("Location: /financas/auth/login");
        exit;
    }
}