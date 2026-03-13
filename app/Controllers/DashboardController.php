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

        $id_usuario = $_SESSION['id_usuario'];

        // Instancia o Model do Dashboard
        $dashboardModel = $this->model('Dashboard');

        // Busca os dados no banco
        $resumo = $dashboardModel->getResumo($id_usuario);
        $recentes = $dashboardModel->getRecentes($id_usuario);

        // Prepara os dados para mandar para a tela
        $dados = [
            'titulo' => 'Resumo Financeiro',
            'resumo' => $resumo,
            'recentes' => $recentes
        ];

        $this->view('dashboard', $dados);
    }
}
