<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper
{
    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        // Imprimir variables de entorno para confirmar carga
        var_dump($_ENV['SMTP_HOST'], $_ENV['SMTP_USER'], $_ENV['SMTP_PORT']);
    }

    public function enviarCorreoActivacion($email, $token)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom($_ENV['SMTP_USER'], $_ENV['SMTP_FROM_NAME']);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Activación de cuenta';
            $mail->Body = "Haz clic en el siguiente enlace para activar tu cuenta: ";
            $mail->Body .= "<a href='http://localhost/TPFinalPreguntas/activar.php?token=$token'>Activar Cuenta</a>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar el correo: " . $mail->ErrorInfo);
            return false;
        }
    }


}
