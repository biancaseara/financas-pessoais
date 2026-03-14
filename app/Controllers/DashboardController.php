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

        $dashboardModel = $this->model('Dashboard');

        // Busca os dados no banco
        $resumo = $dashboardModel->getResumo($id_usuario);
        $recentes = $dashboardModel->getRecentes($id_usuario);
        
        // NOVA LINHA: Busca os orçamentos do mês atual
        $orcamentos = $dashboardModel->getOrcamentos($id_usuario); 

        $dados = [
            'titulo' => 'Resumo Financeiro',
            'resumo' => $resumo,
            'recentes' => $recentes,
            'orcamentos' => $orcamentos // <- NOVA LINHA
        ];

        $this->view('dashboard', $dados);
    }
}
