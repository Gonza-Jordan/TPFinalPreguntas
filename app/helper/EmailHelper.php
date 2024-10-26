
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

class EmailHelper
{
    public function enviarCorreoActivacion($email, $token)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'brian67.bk@gmail.com';
            $mail->Password = 'btkk btrq slqb jnbp';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Remitente y destinatario
            $mail->setFrom('brian67.bk@gmail.com', 'Tu Proyecto');
            $mail->addAddress($email);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Activación de cuenta';
            $mail->Body = "Haz clic en el siguiente enlace para activar tu cuenta: ";
            $mail->Body .= "<a href='http://localhost/Pregunta2/TPFinalPreguntas/activar.php?token=$token'>Activar Cuenta</a>";
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar el correo: " . $mail->ErrorInfo);
            return false;
        }
    }
}


