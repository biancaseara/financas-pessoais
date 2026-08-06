<?php
require_once BASE_PATH . '/core/Controller.php';

class OnboardingController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }
    }

    public function index() {
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->buscarPorId($_SESSION['id_usuario']);

        if ($usuario['fez_onboarding'] == 1) {
            header("Location: /financas/dashboard");
            exit;
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->view('onboarding/index', [
            'titulo' => 'Configuração Inicial',
            'csrf_token' => $_SESSION['csrf_token'],
            'is_refazendo' => $_SESSION['is_refazendo'] ?? false
        ], false);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $this->setFlash('error', 'Falha de segurança CSRF detectada.');
                header("Location: /financas/auth/login");
                exit;
            }

            $id_usuario = $_SESSION['id_usuario'];

            $perfilModel = $this->model('PerfilFinanceiro');
            $perfilModel->salvarOnboardingInicial(
                $id_usuario,
                $_POST['sentimento_dinheiro'] ?? null,
                $_POST['conhecimento_financeiro'] ?? null,
                $_POST['renda_exata'] ?? null,
                $_POST['tipo_renda'] ?? null,
                $_POST['tem_dividas'] ?? null,
                $_POST['objetivo_principal'] ?? null
            );

            $contaModel = $this->model('Conta');
            $saldo_inicial = str_replace(['.', ','], ['', '.'], $_POST['saldo_inicial'] ?? '0');
            $contaModel->cadastrar(
                $id_usuario, 
                strip_tags(trim($_POST['nome_banco'])), 
                (float) $saldo_inicial, 
                $_POST['cor_conta'] ?? '#8b5cf6'
            );

            $cartaoModel = $this->model('Cartao');
            $limite_total = str_replace(['.', ','], ['', '.'], $_POST['limite_total'] ?? '0');
            $cartaoModel->cadastrar(
                $id_usuario, 
                strip_tags(trim($_POST['nome_cartao'])), 
                (float) $limite_total, 
                $_POST['dia_fechamento'], 
                $_POST['dia_vencimento'], 
                '#8b5cf6'
            );

            $usuarioModel = $this->model('Usuario');
            $usuarioModel->marcarOnboardingConcluido($id_usuario);
            
            if (isset($_SESSION['is_refazendo'])) {
                unset($_SESSION['is_refazendo']);
            }

            $this->setFlash('success', 'Tudo pronto! O Preditiv.ia foi configurado para você.');
            header("Location: /financas/dashboard");
            exit;
        }
    }

    public function refazer() {
        $db = new Database();
        $pdo = $db->getConnection();
        $pdo->prepare("UPDATE usuarios SET fez_onboarding = 0 WHERE id_usuario = ?")->execute([$_SESSION['id_usuario']]);
        
        $_SESSION['is_refazendo'] = true;
        
        header("Location: /financas/onboarding");
        exit;
    }

    public function cancelar() {
        if (isset($_SESSION['is_refazendo']) && $_SESSION['is_refazendo'] === true) {
            $usuarioModel = $this->model('Usuario');
            $usuarioModel->marcarOnboardingConcluido($_SESSION['id_usuario']);
            unset($_SESSION['is_refazendo']);
            
            $this->setFlash('info', 'Alteração cancelada. Seu perfil foi mantido.');
            header("Location: /financas/perfil");
            exit;
        }
        
        header("Location: /financas/onboarding");
        exit;
    }
}