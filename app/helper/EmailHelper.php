<?php

require_once __DIR__ . '/../../vendor/autoload.php';  // Usar el autoload de Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper
{
    public function enviarCorreoActivacion($email, $token)
    {
        $mail = new PHPMailer(true);  // 'true' para excepciones

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'brian67.bk@gmail.com'; // Tu correo de Gmail
            $mail->Password = 'kgop fdbi besg hcka'; // La contraseña de aplicación
            $mail->SMTPSecure = 'tls';  // Usar 'tls'
            $mail->Port = 587;

            // Remitente y destinatario
            $mail->setFrom('brian67.bk@gmail.com', 'Tu Proyecto');
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
