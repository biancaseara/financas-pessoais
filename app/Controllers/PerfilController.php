<?php
require_once BASE_PATH . '/core/Controller.php';

class PerfilController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }
        $this->exigirOnboarding();
    }

    public function index() {
        $usuarioModel = $this->model('Usuario');
        $meusDados = $usuarioModel->buscarPorId($_SESSION['id_usuario']);

        $perfilModel = $this->model('PerfilFinanceiro');
        $perfilIA = $perfilModel->buscarPorIdUsuario($_SESSION['id_usuario']);

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->view('perfil/index', [
            'titulo' => 'Meu Perfil',
            'usuario' => $meusDados,
            'perfil' => $perfilIA,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $this->setFlash('error', 'Falha de segurança CSRF detectada no perfil.');
                header("Location: /financas/perfil");
                exit;
            }

            $usuarioModel = $this->model('Usuario');
            $id_logado = $_SESSION['id_usuario'];
            $perfil_atual = $_SESSION['perfil'];

            $usuario_bd = $usuarioModel->buscarPorId($id_logado);
            $dados_completos = $usuarioModel->buscarPorEmail($usuario_bd['email']);

            $senhaHash = '';
            $nova_senha = $_POST['nova_senha'] ?? '';

            if (!empty($nova_senha)) {
                $senha_atual = $_POST['senha_atual'] ?? '';
                $confirmacao = $_POST['nova_senha_confirmacao'] ?? '';

                if (!password_verify($senha_atual, $dados_completos['senha'])) {
                    $this->setFlash('error', 'Sua senha atual está incorreta.');
                    header("Location: /financas/perfil");
                    exit;
                }
                if ($nova_senha !== $confirmacao) {
                    $this->setFlash('error', 'As novas senhas digitadas não coincidem.');
                    header("Location: /financas/perfil");
                    exit;
                }
                if (strlen($nova_senha) < 8 || !preg_match('/[A-Za-z]/', $nova_senha) || !preg_match('/[0-9]/', $nova_senha)) {
                    $this->setFlash('error', 'A nova senha deve ter no mínimo 8 caracteres, com letras e números.');
                    header("Location: /financas/perfil");
                    exit;
                }
                
                $senhaHash = password_hash($nova_senha, PASSWORD_DEFAULT);
            }

            $usuarioModel->atualizar($id_logado, $_POST['nome'], $_POST['email'], $senhaHash, $perfil_atual);
            $_SESSION['nome'] = $_POST['nome'];

            $this->setFlash('success', 'Seus dados foram atualizados com sucesso!');
            header("Location: /financas/perfil");
            exit;
        }
    }

    public function atualizarIa() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $this->setFlash('error', 'Falha de segurança CSRF detectada na atualização da IA.');
                header("Location: /financas/perfil");
                exit;
            }

            $perfilModel = $this->model('PerfilFinanceiro');
            $id_usuario = $_SESSION['id_usuario'];

            $perfilModel->atualizarPerfilCompleto(
                $id_usuario,
                $_POST['maior_problema'] ?? null,
                $_POST['situacao_fim_mes'] ?? null,
                $_POST['tipos_divida'] ?? null,
                $_POST['status_divida'] ?? null,
                $_POST['valor_divida_exata'] ?? null,
                $_POST['controle_gastos'] ?? null,
                $_POST['gatilho_gastos'] ?? null,
                $_POST['tentou_organizar'] ?? null,
                $_POST['tentou_nao_funcionou'] ?? null,
                $_POST['reserva_emergencia'] ?? null,
                $_POST['meses_reserva'] ?? null,
                $_POST['local_reserva'] ?? null,
                $_POST['conhece_conceitos'] ?? null,
                $_POST['ja_investiu'] ?? null,
                $_POST['tipos_investimento'] ?? null,
                $_POST['quer_renda_extra'] ?? null,
                $_POST['pode_aumentar_renda'] ?? null,
                $_POST['habilidades'] ?? null,
                $_POST['horas_disponiveis'] ?? null,
                $_POST['acesso_tecnologia'] ?? null,
                $_POST['dependentes'] ?? null,
                $_POST['tempo_melhoria'] ?? null,
                $_POST['sentimento_dinheiro'] ?? null,
                $_POST['conhecimento_financeiro'] ?? null,
                $_POST['renda_exata'] ?? null,
                $_POST['tipo_renda'] ?? null,
                $_POST['tem_dividas'] ?? null,
                $_POST['objetivo_principal'] ?? null
            );

            $this->setFlash('success', 'Perfil comportamental atualizado para a IA!');
            header("Location: /financas/perfil");
            exit;
        }
    }
}