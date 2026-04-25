<?php
require_once BASE_PATH . '/core/Controller.php';

class CartoesController extends Controller
{
    public function __construct() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }
    }

    public function index() {
        $cartaoModel = $this->model('Cartao');
        $id_usuario = $_SESSION['id_usuario'];

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->view('cartoes/index', [
            'titulo' => 'Meus Cartões de Crédito',
            'cartoes' => $cartaoModel->listarTodos($id_usuario),
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $cartaoModel = $this->model('Cartao');
            $id_usuario = $_SESSION['id_usuario'];
            
            $limite = $_POST['limite_total'];
            $limite = str_replace('.', '', $limite);
            $limite = str_replace(',', '.', $limite);
            $limite = (float) $limite;

            $cartaoModel->cadastrar($id_usuario, strip_tags(trim($_POST['nome_cartao'])), $limite, $_POST['dia_fechamento'], $_POST['dia_vencimento'], $_POST['cor_identificacao'] ?? '#000000'
            );
            
            header("Location: /financas/cartoes");
        }
    }

    public function edit($id) {
        $cartaoModel = $this->model('Cartao');
        $id_usuario = $_SESSION['id_usuario'];
        
        $cartao = $cartaoModel->buscarPorId($id, $id_usuario);

        if ($cartao) {
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            
            $this->view('cartoes/edit', [
                'titulo' => 'Editar Cartão',
                'cartao' => $cartao,
                'csrf_token' => $_SESSION['csrf_token']
            ]);
        } else {
            header("Location: /financas/cartoes");
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $cartaoModel = $this->model('Cartao');
            $id_usuario = $_SESSION['id_usuario'];
            
            $limite = $_POST['limite_total'];
            $limite = str_replace('.', '', $limite);
            $limite = str_replace(',', '.', $limite);
            $limite = (float) $limite;

            $cartaoModel->atualizar($id, $id_usuario, strip_tags(trim($_POST['nome_cartao'])), $limite, $_POST['dia_fechamento'], $_POST['dia_vencimento'], $_POST['cor_identificacao']);

            header("Location: /financas/cartoes");
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception("Falha de segurança CSRF detectada.");
            }

            $cartaoModel = $this->model('Cartao');
            $id_usuario = $_SESSION['id_usuario'];
            
            $cartaoModel->deletar($id, $id_usuario);
            header("Location: /financas/cartoes");
        }
    }

}

?>