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
            'titulo' => 'Personalize sua Experiência',
            'csrf_token' => $_SESSION['csrf_token']
        ], false);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $perfilModel = $this->model('PerfilFinanceiro');
            
            $perfilModel->salvarOnboardingInicial(
                $_SESSION['id_usuario'],
                $_POST['sentimento_dinheiro'] ?? null,
                $_POST['conhecimento_financeiro'] ?? null,
                $_POST['renda_mensal'] ?? null,
                $_POST['tipo_renda'] ?? null,
                $_POST['tem_dividas'] ?? null,
                $_POST['objetivo_principal'] ?? null
            );

            $usuarioModel = $this->model('Usuario');
            $usuarioModel->marcarOnboardingConcluido($_SESSION['id_usuario']);

            header("Location: /financas/dashboard");
            exit;
        }
    }
}