<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/model/UsuarioModel.php';

$token = htmlspecialchars($_GET['token'] ?? '');

if (empty($token)) {
    header('Location: /TPFinalPreguntas/auth/show&mensaje=token_faltante');
    exit();
}

$db = Database::getConnection();
$usuarioModel = new UsuarioModel($db);

if ($usuarioModel->activarCuenta($token)) {
    header('Location: /TPFinalPreguntas/auth/show&mensaje=activacion_exito');
    exit();
} else {
    header('Location: /TPFinalPreguntas/auth/show&mensaje=token_invalido');
    exit();
}
