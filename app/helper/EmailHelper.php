<?php

require_once __DIR__ . '/../../vendor/autoload.php';  // Usar el autoload de Composer
use Dotenv\Dotenv;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper
{
    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }
    public function enviarCorreoActivacion($email, $token)
    {

        $mail = new PHPMailer(true);  // 'true' para excepciones

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = 'tls';
            $mail->Port = $_ENV['SMTP_PORT'];

            // Remitente y destinatario
            $mail->setFrom($_ENV['SMTP_USER'], $_ENV['SMTP_FROM_NAME']);
            $mail->addAddress($email);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Activación de cuenta';
            $mail->Body = "Haz clic en el siguiente enlace para activar tu cuenta: ";
            $mail->Body .= "<a href='http://localhost/TPFinalPreguntas/activar.php?token=$token'>Activar Cuenta</a>";

            // Enviar el correo
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar el correo: " . $mail->ErrorInfo);
            return false;
        }
    }
}
