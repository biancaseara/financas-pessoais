<?php
require_once BASE_PATH . '/core/Controller.php';

class InvestimentosController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['id_usuario'])) { header("Location: /financas/auth/login"); exit; }
        $this->exigirOnboarding();
    }

    public function index() {
        $investimentoModel = $this->model('Investimento');
        $investimentos = $investimentoModel->listarTodos($_SESSION['id_usuario']);
        $tipos = $investimentoModel->listarTipos($_SESSION['id_usuario']);
        
        if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }

        $this->view('investimentos/index', [
            'titulo' => 'Meus Investimentos',
            'investimentos' => $investimentos,
            'tipos' => $tipos,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $investimentoModel = $this->model('Investimento');
            $vencimento = !empty($_POST['vencimento']) ? $_POST['vencimento'] : null;

            $investimentoModel->cadastrar(
                $_SESSION['id_usuario'], 
                strip_tags(trim($_POST['nome_investimento'])), 
                $_POST['tipo'], 
                strip_tags(trim($_POST['corretora'])),
                $_POST['valor_aplicado'],
                $_POST['data_aplicacao'],
                $vencimento
            );
            $this->setFlash('success', 'Investimento adicionado e aporte registrado no histórico!');
            header("Location: /financas/investimentos");
        }
    }

    public function storeTipo() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $investimentoModel = $this->model('Investimento');
            $investimentoModel->cadastrarTipo($_SESSION['id_usuario'], strip_tags(trim($_POST['nome_tipo'])), $_POST['grupo']);
            
            $this->setFlash('success', 'Nova categoria de investimento criada!');
            header("Location: /financas/investimentos");
        }
    }

    public function edit($id) {
        $investimentoModel = $this->model('Investimento');
        $investimento = $investimentoModel->buscarPorId($id, $_SESSION['id_usuario']);
        $tipos = $investimentoModel->listarTipos($_SESSION['id_usuario']);
        $historico = $investimentoModel->listarHistorico($id);

        if ($investimento) {
            $this->view('investimentos/edit', [
                'titulo' => 'Painel do Ativo',
                'investimento' => $investimento,
                'tipos' => $tipos,
                'historico' => $historico,
                'csrf_token' => $_SESSION['csrf_token'] ?? ''
            ]);
        } else {
            header("Location: /financas/investimentos");
        }
    }

    public function storeMovimentacao($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $investimentoModel = $this->model('Investimento');
            
            $tipo_movimento = $_POST['tipo_movimento'];
            $valor = (float) $_POST['valor'];
            $data_movimento = $_POST['data_movimento'];
            $observacao = strip_tags(trim($_POST['observacao']));

            $investimentoModel->adicionarMovimentacao($id, $_SESSION['id_usuario'], $tipo_movimento, $valor, $data_movimento, $observacao);
            
            $this->setFlash('success', 'Movimentação registrada e saldo atualizado!');
            header("Location: /financas/investimentos/edit/" . $id);
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $investimentoModel = $this->model('Investimento');
            $vencimento = !empty($_POST['vencimento']) ? $_POST['vencimento'] : null;

            $investimentoModel->atualizar(
                $id,
                $_SESSION['id_usuario'], 
                strip_tags(trim($_POST['nome_investimento'])), 
                $_POST['tipo'], 
                strip_tags(trim($_POST['corretora'])),
                $_POST['valor_aplicado'],
                $_POST['data_aplicacao'],
                $vencimento
            );
            $this->setFlash('success', 'Investimento atualizado.');
            header("Location: /financas/investimentos");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $investimentoModel = $this->model('Investimento');
            $investimentoModel->deletar($id, $_SESSION['id_usuario']);
            $this->setFlash('success', 'Investimento removido.');
            header("Location: /financas/investimentos");
        }
    }
}
?>