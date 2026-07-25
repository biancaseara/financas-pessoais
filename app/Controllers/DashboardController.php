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

        $perfilIncompleto = empty($perfil['maior_problema']);

        $dados = [
            'titulo' => 'Resumo Financeiro',
            'resumo' => $resumo,
            'recentes' => $recentes,
            'orcamentos' => $orcamentos,
            'gastosPorCategoria' => $gastosPorCategoria,
            'conselho_ia' => $ultimoConselho,
            'perfilIncompleto' => $perfilIncompleto
        ];

        $this->view('dashboard', $dados);
    }
}