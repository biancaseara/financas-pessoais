<?php
require_once BASE_PATH . '/core/Controller.php';

class RecorrentesController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }
    }

    public function index() {
        $recorrenteModel = $this->model('DespesaRecorrente');
        $contaModel = $this->model('Conta');
        $categoriaModel = $this->model('Categoria');
        $id_usuario = $_SESSION['id_usuario'];

        $this->view('recorrentes/index', [
            'titulo' => 'Despesas Fixas e Assinaturas',
            'recorrentes' => $recorrenteModel->listarTodos($id_usuario),
            'contas' => $contaModel->listarTodos($id_usuario),
            'categorias' => $categoriaModel->listarTodos($id_usuario)
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recorrenteModel = $this->model('DespesaRecorrente');
            $recorrenteModel->cadastrar(
                $_SESSION['id_usuario'],
                $_POST['id_conta'],
                $_POST['id_categoria'],
                $_POST['descricao'],
                $_POST['valor'],
                $_POST['dia_vencimento']
            );
            header("Location: /financas/recorrentes");
        }
    }

    public function edit($id) {
        $recorrenteModel = $this->model('DespesaRecorrente');
        $id_usuario = $_SESSION['id_usuario'];
        $recorrente = $recorrenteModel->buscarPorId($id, $id_usuario);

        if ($recorrente) {
            $contaModel = $this->model('Conta');
            $categoriaModel = $this->model('Categoria');

            $this->view('recorrentes/edit', [
                'titulo' => 'Editar Despesa Fixa',
                'recorrente' => $recorrente,
                'contas' => $contaModel->listarTodos($id_usuario),
                'categorias' => $categoriaModel->listarTodos($id_usuario)
            ]);
        } else {
            header("Location: /financas/recorrentes");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recorrenteModel = $this->model('DespesaRecorrente');
            $recorrenteModel->atualizar(
                $id,
                $_SESSION['id_usuario'],
                $_POST['id_conta'],
                $_POST['id_categoria'],
                $_POST['descricao'],
                $_POST['valor'],
                $_POST['dia_vencimento'],
                $_POST['status']
            );
            header("Location: /financas/recorrentes");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recorrenteModel = $this->model('DespesaRecorrente');
            $recorrenteModel->deletar($id, $_SESSION['id_usuario']);
            header("Location: /financas/recorrentes");
        }
    }

    public function lancarMes() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recorrenteModel = $this->model('DespesaRecorrente');
            $transacaoModel = $this->model('Transacao');
            
            $id_usuario = $_SESSION['id_usuario'];
            $despesas = $recorrenteModel->listarTodos($id_usuario);
            
            $mesAno = date('Y-m'); 
            $mesAnoDisplay = date('m/Y'); 
            $ultimoDiaMes = date('t'); 
            
            foreach ($despesas as $d) {
                if ($d['status'] == 'Ativo') {
                    $diaVencimento = $d['dia_vencimento'];
                    if ($diaVencimento > $ultimoDiaMes) {
                        $diaVencimento = $ultimoDiaMes;
                    }
                    
                    $dataVencimento = $mesAno . '-' . str_pad($diaVencimento, 2, '0', STR_PAD_LEFT);
                    $descricaoFormatada = "🔄 " . $d['descricao'] . " (" . $mesAnoDisplay . ")";
                    
                    $transacaoModel->cadastrar( $id_usuario, $d['id_conta'], $d['id_categoria'], $descricaoFormatada, $d['valor'], $dataVencimento, 'Saida', null);
                }
            }
            header("Location: /financas/transacoes");
        }
    }
}