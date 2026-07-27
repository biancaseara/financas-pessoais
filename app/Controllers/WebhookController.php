<?php
require_once BASE_PATH . '/core/Controller.php';

class WebhookController extends Controller {

    public function telegram() {
        $conteudoJson = file_get_contents("php://input");
        $update = json_decode($conteudoJson, true);

        if (!isset($update['message'])) {
            http_response_code(200);
            exit;
        }

        $mensagem = $update['message'];
        $chatId = $mensagem['chat']['id'];
        $textoMensagem = $mensagem['text'] ?? '';

        $logData = date('Y-m-d H:i:s') . " - Chat ID: {$chatId} - Mensagem: {$textoMensagem}\n";
        file_put_contents(BASE_PATH . '/logs_telegram.txt', $logData, FILE_APPEND);

        http_response_code(200);
        echo json_encode(["status" => "sucesso"]);
        exit;
    }
}