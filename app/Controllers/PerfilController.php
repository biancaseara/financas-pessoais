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
                throw new Exception("Falha de segurança CSRF detectada no perfil.");
            }

            $usuarioModel = $this->model('Usuario');
            
            $id_logado = $_SESSION['id_usuario'];
            $perfil_atual = $_SESSION['perfil'];
            
            $usuarioModel->atualizar($id_logado, $_POST['nome'], $_POST['email'], $_POST['senha'], $perfil_atual);
            
            $_SESSION['nome'] = $_POST['nome'];

            header("Location: /financas/perfil?sucesso=1");
        }
    }

    public function atualizarIa() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada na atualização da IA.");
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
                
                // AS 6 PERGUNTAS DO ONBOARDING INICIAL
                $_POST['sentimento_dinheiro'] ?? null,
                $_POST['conhecimento_financeiro'] ?? null,
                $_POST['renda_exata'] ?? null,
                $_POST['tipo_renda'] ?? null,
                $_POST['tem_dividas'] ?? null,
                $_POST['objetivo_principal'] ?? null
            );

            header("Location: /financas/perfil?sucesso=1");
            exit;
        }
    }
}