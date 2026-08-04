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
                        
                        if (isset($usuario['status']) && $usuario['status'] === 'inativo') {
                            $erro = "Sua conta foi desativada. Entre em contato com o suporte.";
                        } else {
                            session_regenerate_id(true);

                            $_SESSION['id_usuario'] = $usuario['id_usuario'];
                            $_SESSION['perfil'] = $usuario['perfil'];
                            $_SESSION['nome'] = $usuario['nome'];
                            header("Location: /financas");
                            exit;
                        }
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

    public function esqueciSenha()
    {
        $csrf_token = $this->initCsrfToken();
        $this->view('auth/esqueci-senha', [
            'titulo' => 'Recuperar Senha',
            'erro' => '',
            'sucesso' => '',
            'csrf_token' => $csrf_token
        ]);
    }

    public function recuperarSenha()
    {
        $erro = "";
        $sucesso = "";
        $csrf_token = $this->initCsrfToken();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!$this->validateCsrfToken()) {
                $erro = "Token de segurança inválido.";
            } else {
                $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

                if (!$email) {
                    $erro = "E-mail inválido.";
                } else {
                    $usuarioModel = $this->model('Usuario');
                    $usuario = $usuarioModel->buscarPorEmail($email);

                    if ($usuario) {
                        $token = bin2hex(random_bytes(32));
                        $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

                        if ($usuarioModel->salvarTokenRecuperacao($email, $token, $expiracao)) {
                            require_once BASE_PATH . '/core/Email.php';
                            $emailService = new Email();

                            $linkRecuperacao = "http://" . $_SERVER['HTTP_HOST'] . "/financas/auth/redefinirSenha?token=" . $token;

                            $assunto = "Recuperação de Senha - PREDITIV.IA";
                            $corpoHTML = "
                                <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px;'>
                                    <h2 style='color: #8b5cf6;'>Olá, " . htmlspecialchars($usuario['nome']) . "!</h2>
                                    <p>Recebemos uma solicitação para redefinir a senha da sua conta no <strong>PREDITIV.IA</strong>.</p>
                                    <p>Clique no botão abaixo para criar uma nova senha. Por segurança, este link é válido por apenas 1 hora.</p>
                                    <div style='text-align: center; margin: 30px 0;'>
                                        <a href='{$linkRecuperacao}' style='background: #8b5cf6; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Redefinir Minha Senha</a>
                                    </div>
                                    <p style='font-size: 12px; color: #6b7280;'>Se você não solicitou essa alteração, apenas ignore este e-mail. Nenhuma mudança será feita na sua conta.</p>
                                </div>
                            ";

                            if ($emailService->enviar($email, $usuario['nome'], $assunto, $corpoHTML)) {
                                $sucesso = "Se o e-mail estiver cadastrado, você receberá um link de recuperação em instantes.";
                            } else {
                                $erro = "Erro ao tentar enviar o e-mail. Verifique suas configurações de SMTP.";
                            }
                        } else {
                            $erro = "Erro interno ao processar a solicitação.";
                        }
                    } else {
                        $sucesso = "Se o e-mail estiver cadastrado, você receberá um link de recuperação em instantes.";
                    }
                }
            }
        }

        $this->view('auth/esqueci-senha', [
            'titulo' => 'Recuperar Senha',
            'erro' => $erro,
            'sucesso' => $sucesso,
            'csrf_token' => $csrf_token
        ]);
    }

    public function redefinirSenha()
    {
        $erro = "";
        $sucesso = "";
        $csrf_token = $this->initCsrfToken();
        
        $token = $_GET['token'] ?? $_POST['token'] ?? '';

        if (empty($token)) {
            header("Location: /financas/auth/login");
            exit;
        }

        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->buscarPorTokenReset($token);
        $token_valido = true;

        if (!$usuario) {
            $erro = "O link de redefinição é inválido ou expirou. Por favor, solicite um novo.";
            $token_valido = false;
        } else {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (!$this->validateCsrfToken()) {
                    $erro = "Token de segurança inválido.";
                } else {
                    $senha = trim($_POST['senha']);
                    $senha_confirmacao = trim($_POST['senha_confirmacao'] ?? '');

                    if (empty($senha) || $senha !== $senha_confirmacao) {
                        $erro = "As senhas digitadas não coincidem.";
                    } elseif (strlen($senha) < 8 || !preg_match('/[A-Za-z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
                        $erro = "A senha deve ter pelo menos 8 caracteres, incluindo letras e números.";
                    } else {
                        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                        
                        if ($usuarioModel->atualizarSenhaEToken($usuario['id_usuario'], $senhaHash)) {
                            $sucesso = "Sua senha foi redefinida com sucesso! Você já pode acessar o sistema.";
                            $token_valido = false;
                        } else {
                            $erro = "Erro interno ao atualizar a senha.";
                        }
                    }
                }
            }
        }

        $this->view('auth/redefinir-senha', [
            'titulo' => 'Criar Nova Senha',
            'erro' => $erro,
            'sucesso' => $sucesso,
            'csrf_token' => $csrf_token,
            'token' => $token,
            'token_valido' => $token_valido
        ]);
    }

    public function logout()
    {
        session_destroy();

        $this->setFlash('info', 'Você saiu do sistema com segurança.');
        header("Location: /financas/auth/login");
        exit;
    }
}