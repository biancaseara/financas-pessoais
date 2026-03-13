<?php
require_once BASE_PATH . '/core/Controller.php';

class MetasController extends Controller {

    public function index() {
        $metaModel = $this->model('Meta');
        $metas = $metaModel->listarTodos();
        
        $this->view('metas/index', [
            'titulo' => 'Meus Objetivos',
            'metas' => $metas
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $metaModel = $this->model('Meta');
            $id_usuario = $_SESSION['id_usuario'] ?? 7; 
            
            $metaModel->cadastrar(
                $id_usuario, 
                $_POST['titulo_meta'], 
                $_POST['valor_objetivo'], 
                $_POST['valor_atual'], 
                $_POST['data_limite']
            );
            header("Location: /financas/metas");
        }
    }

    public function edit($id) {
        $metaModel = $this->model('Meta');
        $meta = $metaModel->buscarPorId($id);

        if ($meta) {
            $this->view('metas/edit', [
                'titulo' => 'Atualizar Meta',
                'meta' => $meta
            ]);
        } else {
            header("Location: /financas/metas");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $metaModel = $this->model('Meta');
            $metaModel->atualizar(
                $id, 
                $_POST['titulo_meta'], 
                $_POST['valor_objetivo'], 
                $_POST['valor_atual'], 
                $_POST['data_limite']
            );
            header("Location: /financas/metas");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $metaModel = $this->model('Meta');
            $metaModel->deletar($id);
            header("Location: /financas/metas");
        }
    }
}