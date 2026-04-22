<?php
require_once BASE_PATH . '/core/Controller.php';

class ContasController extends Controller
{

    public function __construct()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }
    }

    public function index()
    {
        $contaModel = $this->model('Conta');
        $id_usuario = $_SESSION['id_usuario'];
        
        if (empty($_SESSION{'csrf_token'})) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $contas = $contaModel->listarTodos($id_usuario);

        $this->view('contas/index', [
            'titulo' => 'Minhas Contas',
            'contas' => $contas,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Acesso negado: Falha de segurança CSRF.");
            }

            $contaModel = $this->model('Conta');
            $id_usuario = $_SESSION['id_usuario'];

            $nome_banco = strip_tags(trim($_POST['nome_banco']));
            $cor = $_POST['cor_identificacao'];

            if(!preg_match('/^#[a-fA-F0-9]{6}$/', $cor)) {
                $cor = '#CCC';
            }

            // Conversão de BRL para Decimal (Float) para o Banco de Dados
            $saldo_inicial = $_POST['saldo_inicial'];
            $saldo_inicial = str_replace('.', '', $saldo_inicial); // Tira os pontos de milhar
            $saldo_inicial = str_replace(',', '.', $saldo_inicial); // Troca vírgula por ponto
            $saldo_inicial = (float) $saldo_inicial; // Garante que seja número

            $contaModel->cadastrar($id_usuario, $nome_banco, $saldo_inicial, $cor);
            header("Location: /financas/contas");
        }
    }

    public function edit($id)
    {
        $contaModel = $this->model('Conta');
        $id_usuario = $_SESSION['id_usuario'];
        
        $conta = $contaModel->buscarPorId($id, $id_usuario);

        if ($conta) {
            if (empty($_SESSION{'csrf_token'})) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            
            $this->view('contas/edit', [
                'titulo' => 'Editar Conta',
                'conta' => $conta,
                'csrf_token' => $_SESSION['csrf_token']
            ]);
        } else {
            header("Location: /financas/contas");
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Acesso negado: Falha de segurança CSRF.");
            }
            
            $contaModel = $this->model('Conta');
            $id_usuario = $_SESSION['id_usuario'];

            $nome_banco = strip_tags(trim($_POST['nome_banco']));
            $cor = $_POST['cor_identificacao'];
            
            if (!preg_match('/^#[a-fA-F0-9]{6}$/', $cor)) {
                $cor = '#CCCCCC'; 
            }

            $saldo_inicial = $_POST['saldo_inicial'];
            $saldo_inicial = str_replace('.', '', $saldo_inicial);
            $saldo_inicial = str_replace(',', '.', $saldo_inicial);
            $saldo_inicial = (float) $saldo_inicial;

            $contaModel->atualizar($id, $id_usuario, $nome_banco, $saldo_inicial, $cor);
            header("Location: /financas/contas");
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Acesso negado: Falha de segurança CSRF.");
            }

            $contaModel = $this->model('Conta');
            $id_usuario = $_SESSION['id_usuario'];

            $contaModel->deletar($id, $id_usuario);
            header("Location: /financas/contas");
        }
    }
}
