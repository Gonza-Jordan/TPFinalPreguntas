<?php
session_start();
include_once 'config/Configuration.php';
include_once 'controller/UsuarioController.php';
include_once 'config/Database.php';

$configuration = new Configuration();
$router = $configuration->getRouter();

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'show';
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $usuarioController = new UsuarioController($db);

    switch ($page) {
        case 'actualizarFoto':
            $usuarioController->actualizarFotoPerfil();
            exit();
        case 'actualizarPerfil':
            $usuarioController->actualizarPerfilUsuario();
            exit();
    }
}

$router->route($page, $action, $id);
