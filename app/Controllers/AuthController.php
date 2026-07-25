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
    
                $aceitou_termos = isset($_POST['termos']) ? 1 : 0;
                $data_aceite_termos = date('Y-m-d H:i:s');
    
                if (!$email) {
                    $erro = "E-mail inválido.";
                } elseif (!empty($nome) && !empty($senha)) {
                    $usuarioModel = $this->model('Usuario');
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    
                    if ($usuarioModel->cadastrar($nome, $email, $senhaHash, $aceitou_termos, $data_aceite_termos, 'comum')) {
                        $novo_usuario = $usuarioModel->buscarPorEmail($email);
                        $id_novo_usuario = $novo_usuario['id_usuario'];

                        $db = new Database();
                        $pdo = $db->getConnection();

                        $sqlConta = "INSERT INTO contas (id_usuario, nome_conta, saldo, tipo_conta) VALUES (?, 'Carteira Principal', 0, 'Conta Corrente')";
                        $pdo->prepare($sqlConta)->execute([$id_novo_usuario]);

                        $categoriasPadrao = [
                            ['Alimentação', 'Saida', '#ef4444'],
                            ['Moradia', 'Saida', '#f59e0b'],
                            ['Transporte', 'Saida', '#3b82f6'],
                            ['Saúde', 'Saida', '#ec4899'],
                            ['Renda Principal', 'Entrada', '#10b981'],
                            ['Reserva e Investimentos', 'Saida', '#8b5cf6'],
                            ['Assinaturas e Streaming', 'Saida', '#6366f1'],
                            ['Internet e Telefonia', 'Saida', '#14b8a6'],
                        ];

                        $sqlCategoria = "INSERT INTO categorias (id_usuario, nome_categoria, tipo_categoria, cor) VALUES (?, ?, ?, ?)";
                        $stmtCat = $pdo->prepare($sqlCategoria);

                        foreach ($categoriasPadrao as $cat) {
                            $stmtCat->execute([$id_novo_usuario, $cat[0], $cat[1], $cat[2]]);
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
                } else {
                    $erro = "Preencha todos os campos.";
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