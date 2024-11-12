<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/model/UsuarioModel.php';

// Obtener el token de la URL y sanitizarlo
$token = htmlspecialchars($_GET['token'] ?? '');

// Verificar si el token está vacío
if (empty($token)) {
    header('Location: /TPFinalPreguntas/app/index.php?page=auth&action=show&mensaje=token_faltante');
    exit();
}

$db = Database::getConnection();
$usuarioModel = new UsuarioModel($db);

// Verificar y activar la cuenta
if ($usuarioModel->activarCuenta($token)) {
    // Redirigir al login con un mensaje de éxito
    header('Location: /TPFinalPreguntas/app/index.php?page=auth&action=show&mensaje=activacion_exito');
    exit();
} else {
    // Redirigir al login con un mensaje de error
    header('Location: /TPFinalPreguntas/app/index.php?page=auth&action=show&mensaje=token_invalido');
    exit();
}
