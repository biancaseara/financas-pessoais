<?php
require_once BASE_PATH . '/core/Controller.php';

class MetasController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['id_usuario'])) { header("Location: /financas/auth/login"); exit; }
        $this->exigirOnboarding();
    }

    public function index() {
        $metaModel = $this->model('Meta');
        if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }

        $this->view('metas/index', [
            'titulo' => 'Meus Objetivos',
            'metas' => $metaModel->listarTodos($_SESSION['id_usuario']),
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $metaModel = $this->model('Meta');
            
            // CORREÇÃO DO BUG: Lê o valor direto como o HTML envia (já no formato decimal)
            $valor_objetivo = (float) $_POST['valor_objetivo'];
            $valor_atual = (float) $_POST['valor_atual'];

            $metaModel->cadastrar(
                $_SESSION['id_usuario'], 
                strip_tags(trim($_POST['titulo_meta'])), 
                $valor_objetivo, 
                $valor_atual, 
                $_POST['data_limite']
            );

            // Se a meta já começar com dinheiro, grava o histórico inicial
            if ($valor_atual > 0) {
                $id_meta = $this->model('Meta')->pdo->lastInsertId();
                $this->model('Meta')->adicionarAporte($id_meta, $_SESSION['id_usuario'], $valor_atual, date('Y-m-d'), 'Aporte Inicial');
            }

            $this->setFlash('success', 'Novo objetivo financeiro traçado!');
            header("Location: /financas/metas");
        }
    }
    
    public function edit($id) {
        $metaModel = $this->model('Meta');
        $meta = $metaModel->buscarPorId($id, $_SESSION['id_usuario']);
        $historico = $metaModel->listarHistorico($id);

        if ($meta) {
            $this->view('metas/edit', [
                'titulo' => 'Painel do Objetivo',
                'meta' => $meta,
                'historico' => $historico,
                'csrf_token' => $_SESSION['csrf_token'] ?? ''
            ]);
        } else {
            header("Location: /financas/metas");
        }
    }

    public function storeAporte($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $metaModel = $this->model('Meta');
            $valor = (float) $_POST['valor'];
            
            $metaModel->adicionarAporte(
                $id, 
                $_SESSION['id_usuario'], 
                $valor, 
                $_POST['data_movimento'], 
                strip_tags(trim($_POST['observacao']))
            );
            
            $this->setFlash('success', 'Dinheiro guardado! Você está mais perto da sua meta.');
            header("Location: /financas/metas/edit/" . $id);
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $metaModel = $this->model('Meta');
            $valor_objetivo = (float) $_POST['valor_objetivo'];
            $valor_atual = (float) $_POST['valor_atual'];

            $metaModel->atualizar(
                $id, 
                $_SESSION['id_usuario'],
                strip_tags(trim($_POST['titulo_meta'])), 
                $valor_objetivo, 
                $valor_atual, 
                $_POST['data_limite']
            );

            $this->setFlash('success', 'Configurações da meta salvas.');
            header("Location: /financas/metas/edit/" . $id);
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model('Meta')->deletar($id, $_SESSION['id_usuario']);
            $this->setFlash('success', 'Meta excluída.');
            header("Location: /financas/metas");
        }
    }
}