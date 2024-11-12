
<?php
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tuchanguitocompras@gmail.com';
    $mail->Password = 'kiya oeqf rtvz dbnp';
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
