<?php
session_start();
include_once 'config/Configuration.php';

$configuration = new Configuration();
$router = $configuration->getRouter();

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'show';

// Verifica si hay un método POST para la actualización de perfil o foto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($page == 'actualizarFoto') {
        require_once 'controller/UsuarioController.php';
        require_once 'config/Database.php';

        $database = new Database();
        $db = $database->getConnection();

        $usuarioController = new UsuarioController($db);
        $usuarioController->actualizarFotoPerfil();
        exit();
    } elseif ($page == 'actualizarPerfil') {
        require_once 'controller/UsuarioController.php';
        require_once 'config/Database.php';

        $database = new Database();
        $db = $database->getConnection();

        $usuarioController = new UsuarioController($db);
        $usuarioController->actualizarPerfilUsuario();
        exit();
    }
}

$router->route($page, $action);
