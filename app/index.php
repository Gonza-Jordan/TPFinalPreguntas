<?php
include_once("config/Configuration.php");

$configuration = new Configuration();
$router = $configuration->getRouter();

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'show';

if ($page == 'perfil') {
    $id_usuario = $_GET['id'] ?? null;

    if ($id_usuario) {
        require_once 'controller/UsuarioController.php';
        require_once 'config/Database.php';

        $database = new Database();
        $db = $database->getConnection();

        $usuarioController = new UsuarioController($db);
        $usuarioController->mostrarPerfil($id_usuario);
    } else {
        echo "ID de usuario no especificado.";
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $page == 'actualizarFoto') {
    require_once 'controller/UsuarioController.php';
    require_once 'config/Database.php';

    $database = new Database();
    $db = $database->getConnection();

    $usuarioController = new UsuarioController($db);
    $usuarioController->actualizarFotoPerfil();

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $page == 'actualizarPerfil') {
    require_once 'controller/UsuarioController.php';
    require_once 'config/Database.php';

    $database = new Database();
    $db = $database->getConnection();

    $usuarioController = new UsuarioController($db);
    $usuarioController->actualizarPerfilUsuario();
} else {
    $router->route($page, $action);
}
