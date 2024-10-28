
<?php
require_once __DIR__ . '/vendor/autoload.php';  // Usar el autoload de Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'brian67.bk@gmail.com';
    $mail->Password = 'rzws dyww xbyr xzuf';  // La contraseña de aplicación generada
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('brian67.bk@gmail.com', 'Tu Proyecto');
    $mail->addAddress('destino@example.com');

    $mail->isHTML(true);
    $mail->Subject = 'Prueba de PHPMailer';
    $mail->Body = 'Este es un correo de prueba enviado con PHPMailer.';

    $mail->send();
    echo 'Correo enviado con éxito.';
} catch (Exception $e) {
    echo 'Error al enviar el correo: ' . $mail->ErrorInfo;
}
?>
