<?php
require_once BASE_PATH . '/core/Controller.php';

class TelegramController extends Controller {
    
    public function __construct() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }
        $this->exigirOnboarding();
    }

    public function index() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->view('telegram/index', [
            'titulo' => 'Conectar Bot do Telegram',
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function vincular() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $this->setFlash('error', 'Falha de segurança CSRF detectada.');
                header("Location: /financas/telegram");
                exit;
            }

            $chat_id = strip_tags(trim($_POST['chat_id']));

            if (empty($chat_id)) {
                $this->setFlash('error', 'Por favor, informe o seu código do Telegram.');
                header("Location: /financas/telegram");
                exit;
            }

            try {
                $db = new Database();
                $pdo = $db->getConnection();
                
                $stmt = $pdo->prepare("UPDATE usuarios SET chat_id_telegram = ? WHERE id_usuario = ?");
                $stmt->execute([$chat_id, $_SESSION['id_usuario']]);

                $this->setFlash('success', 'Bot vinculado com sucesso! Você já pode enviar suas despesas por lá.');
            } catch (Exception $e) {
                $this->setFlash('error', 'Erro ao vincular conta. Tente novamente.');
            }

            header("Location: /financas/telegram");
            exit;
        }
    }
}