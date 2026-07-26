<?php
require_once BASE_PATH . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        try {
            $host = $_ENV['MAIL_HOST'] ?? getenv('MAIL_HOST');
            $user = $_ENV['MAIL_USERNAME'] ?? getenv('MAIL_USERNAME');
            $pass = $_ENV['MAIL_PASSWORD'] ?? getenv('MAIL_PASSWORD');
            $port = $_ENV['MAIL_PORT'] ?? getenv('MAIL_PORT');
            $name = $_ENV['MAIL_FROM_NAME'] ?? getenv('MAIL_FROM_NAME');

            $this->mail->isSMTP();
            $this->mail->Host       = $host;
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $user;
            $this->mail->Password   = $pass;
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = $port;
            
            $this->mail->CharSet    = 'UTF-8';

            $this->mail->setFrom($user, $name);
            
        } catch (Exception $e) {
            error_log("Erro crítico ao instanciar o servidor de e-mail: {$this->mail->ErrorInfo}");
        }
    }

    public function enviar($paraEmail, $paraNome, $assunto, $corpoHTML) {
        try {
            $this->mail->clearAddresses();
            
            $this->mail->addAddress($paraEmail, $paraNome);

            $this->mail->isHTML(true);
            $this->mail->Subject = $assunto;
            $this->mail->Body    = $corpoHTML;
            
            $this->mail->AltBody = strip_tags($corpoHTML);

            $this->mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Falha ao enviar e-mail para {$paraEmail}. Erro: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}