<?php
require_once BASE_PATH . '/core/Controller.php';

class DashboardController extends Controller
{
    public function index()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }

        $this->exigirOnboarding();

        $id_usuario = $_SESSION['id_usuario'];

        $dashboardModel = $this->model('Dashboard');

        $resumo = $dashboardModel->getResumo($id_usuario);
        $recentes = $dashboardModel->getRecentes($id_usuario);
        $orcamentos = $dashboardModel->getOrcamentos($id_usuario); 
        $gastosPorCategoria = $dashboardModel->getGastosPorCategoria($id_usuario);

        $conselhoModel = $this->model('ConselhoIa');
        $ultimoConselho = $conselhoModel->buscarUltimoConselho($id_usuario);

        $perfilModel = $this->model('PerfilFinanceiro');
        $perfil = $perfilModel->buscarPorIdUsuario($id_usuario);

        $perfilIncompleto = empty($perfil['maior_problema']) || empty($perfil['tempo_melhoria']) || empty($perfil['horas_disponiveis']) || empty($perfil['sentimento_dinheiro']) || empty($perfil['conhecimento_financeiro']) || empty($perfil['renda_exata']) || empty($perfil['tipo_renda']) || empty($perfil['tem_dividas']) || empty($perfil['objetivo_principal']) || empty($perfil['situacao_fim_mes']) || empty($perfil['tipos_divida']) || empty($perfil['status_divida']) || !isset($perfil['valor_divida_exata']) || empty($perfil['controle_gastos']) || empty($perfil['gatilho_gastos']) || empty($perfil['tentou_organizar']) || empty($perfil['tentou_nao_funcionou']) || empty($perfil['reserva_emergencia']) || empty($perfil['meses_reserva']) || empty($perfil['local_reserva']) || empty($perfil['conhece_conceitos']) || empty($perfil['ja_investiu']) || empty($perfil['tipos_investimento']) || empty($perfil['quer_renda_extra']) || empty($perfil['pode_aumentar_renda']) || empty($perfil['habilidades']) || empty($perfil['acesso_tecnologia']) || empty($perfil['dependentes']);

        $despesaRecorrenteModel = $this->model('DespesaRecorrente');
        $despesasFixas = $despesaRecorrenteModel->listarTodos($id_usuario);
        $semDespesasFixas = empty($despesasFixas);

        $motor = $this->model('MotorPreditivo');        
        $motor->processarReservaAutomatica($id_usuario);
        
        $projecao = $motor->calcularProjecaoMensal($id_usuario);
        $raloDinheiro = $motor->encontrarRaloDinheiro($id_usuario);
        $contasEsquecidas = $motor->analisarContasEsquecidas($id_usuario);

        $dados = [
            'titulo' => 'Resumo Financeiro',
            'resumo' => $resumo,
            'recentes' => $recentes,
            'orcamentos' => $orcamentos,
            'gastosPorCategoria' => $gastosPorCategoria,
            'conselho_ia' => $ultimoConselho,
            'perfilIncompleto' => $perfilIncompleto,
            'semDespesasFixas' => $semDespesasFixas,
            'projecao' => $projecao,
            'raloDinheiro' => $raloDinheiro,
            'contasEsquecidas' => $contasEsquecidas
        ];

        $this->view('dashboard', $dados);
    }
}