<?php
require_once BASE_PATH . '/core/Controller.php';

class UsuariosController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'admin') {
            die("<div style='text-align:center; margin-top:50px;'><h2 style='color:red;'>🛑 Acesso Negado</h2><p>Apenas administradores podem gerenciar usuários.</p><a href='/financas'>Voltar ao Dashboard</a></div>");
        }

        $this->exigirOnboarding();
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
            
            $senhaTemporaria = bin2hex(random_bytes(16));
            $senhaHash = password_hash($senhaTemporaria, PASSWORD_DEFAULT);
            
            $nome = $_POST['nome'];
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            $perfil = $_POST['perfil'];

            if ($email) {
                if ($usuarioModel->cadastrar($nome, $email, $senhaHash, 0, null, $perfil)) {
                    
                    $token = bin2hex(random_bytes(32));
                    $expiracao = date('Y-m-d H:i:s', strtotime('+7 days'));
                    
                    if ($usuarioModel->salvarTokenRecuperacao($email, $token, $expiracao)) {
                        
                        require_once BASE_PATH . '/core/Email.php';
                        $emailService = new Email();

                        $linkConvite = "http://" . $_SERVER['HTTP_HOST'] . "/financas/auth/redefinirSenha?token=" . $token;

                        $assunto = "Bem-vindo ao PREDITIV.IA!";
                        $corpoHTML = "
                            <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px;'>
                                <h2 style='color: #8b5cf6;'>Olá, " . htmlspecialchars($nome) . "!</h2>
                                <p>Sua conta no <strong>PREDITIV.IA</strong> foi criada com sucesso.</p>
                                <p>Para começar a usar o sistema, clique no botão abaixo para criar a sua senha de acesso. Este convite é válido por 7 dias.</p>
                                <div style='text-align: center; margin: 30px 0;'>
                                    <a href='{$linkConvite}' style='background: #8b5cf6; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Criar Minha Senha</a>
                                </div>
                            </div>
                        ";
                        
                        $emailService->enviar($email, $nome, $assunto, $corpoHTML);
                    }
                }
            }
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
            
            $perfil = $_POST['perfil'];
            if ($id == $_SESSION['id_usuario']) {
                $perfil = 'admin'; 
            }
            
            $usuarioModel->atualizar($id, $_POST['nome'], $_POST['email'], '', $perfil);
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

    public function reativar($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            if ($id == $_SESSION['id_usuario']) {
                die("Ação não permitida na própria conta."); 
            }
            
            $usuarioModel = $this->model('Usuario');
            $usuarioModel->reativar($id);
            header("Location: /financas/usuarios");
            exit;
        }
    }
}