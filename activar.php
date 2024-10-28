<?php
require_once 'app/config/Database.php';
require_once 'app/model/UsuarioModel.php';

// Obtener el token de la URL y sanitizarlo
$token = htmlspecialchars($_GET['token'] ?? '');

// Verificar si el token está vacío
if (empty($token)) {
    header('Location: error.php?mensaje=token_faltante');
    exit();
}

// Instanciar la clase Database y obtener la conexión
$db = (new Database())->getConnection();
$usuarioModel = new UsuarioModel($db);

// Verificar y activar la cuenta
if ($usuarioModel->activarCuenta($token)) {
    header('Location: login.php?mensaje=activacion_exito');
    exit();
} else {
    header('Location: error.php?mensaje=token_invalido');
    exit();
}
?>
