<?php
session_start();
include_once 'config/Configuration.php';
include_once 'config/Database.php';
include_once 'controller/UsuarioController.php';

$configuration = new Configuration();
$router = $configuration->getRouter();

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'show';
$id = $_GET['id'] ?? null;

// Controlador específico de acciones POST
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

// Lógica de enrutamiento principal para otras páginas
$router->route($page, $action, $id);
