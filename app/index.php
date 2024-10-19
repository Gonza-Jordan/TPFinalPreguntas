<?php
include_once("config/Configuration.php");

$configuration = new Configuration();
$router = $configuration->getRouter();

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'show';

if ($page == 'perfil') {
    $id_usuario = $_GET['id'] ?? null;

    if ($id_usuario) {
        // Ajustamos la ruta para el controlador correcto
        require_once 'controller/UsuarioController.php';
        require_once 'config/Database.php';

        // Crear la conexión a la base de datos
        $database = new Database();
        $db = $database->getConnection();

        // Crear la instancia del controlador y mostrar el perfil
        $usuarioController = new UsuarioController($db);
        $usuarioController->mostrarPerfil($id_usuario);
    } else {
        echo "ID de usuario no especificado.";
    }
} else {
    // Enviar a otras rutas usando el router
    $router->route($page, $action);
}
