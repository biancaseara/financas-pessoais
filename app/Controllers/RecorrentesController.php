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

        $this->view('recorrentes/index', [
            'titulo' => 'Despesas Fixas e Assinaturas',
            'recorrentes' => $recorrenteModel->listarTodos($_SESSION['id_usuario']),
            'contas' => $contaModel->listarTodos(),
            'categorias' => $categoriaModel->listarTodos()
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
        $recorrente = $recorrenteModel->buscarPorId($id, $_SESSION['id_usuario']);

        if ($recorrente) {
            $contaModel = $this->model('Conta');
            $categoriaModel = $this->model('Categoria');

            $this->view('recorrentes/edit', [
                'titulo' => 'Editar Despesa Fixa',
                'recorrente' => $recorrente,
                'contas' => $contaModel->listarTodos(),
                'categorias' => $categoriaModel->listarTodos()
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
            $transacaoModel = $this->model('Transacao'); // Puxa o model de transações para podermos salvar
            
            $id_usuario = $_SESSION['id_usuario'];
            $despesas = $recorrenteModel->listarTodos($id_usuario);
            
            $mesAno = date('Y-m'); // Ex: 2026-03
            $mesAnoDisplay = date('m/Y'); // Ex: 03/2026
            $ultimoDiaMes = date('t'); // Retorna 28, 30 ou 31 dependendo do mês
            
            foreach ($despesas as $d) {
                if ($d['status'] == 'Ativo') {
                    // Proteção: Se a conta vence dia 31, mas estamos em fevereiro (que vai até 28)
                    $diaVencimento = $d['dia_vencimento'];
                    if ($diaVencimento > $ultimoDiaMes) {
                        $diaVencimento = $ultimoDiaMes;
                    }
                    
                    // Monta a data no formato do banco (YYYY-MM-DD)
                    $dataVencimento = $mesAno . '-' . str_pad($diaVencimento, 2, '0', STR_PAD_LEFT);
                    
                    // Coloca uma tag na descrição para saber que foi o sistema que lançou
                    $descricaoFormatada = "🔄 " . $d['descricao'] . " (" . $mesAnoDisplay . ")";
                    
                    // Usa a função de cadastrar transação que já criamos lá na Fase 1
                    $transacaoModel->cadastrar(
                        $d['id_conta'],
                        $d['id_categoria'],
                        $descricaoFormatada,
                        $d['valor'],
                        $dataVencimento,
                        'Saida',
                        null
                    );
                }
            }
            
            // Depois de lançar tudo, redireciona o usuário para a página de transações para ver tudo lançado lá
            header("Location: /financas/transacoes");
        }
    }
}