<?php
require_once '../vendor/autoload.php';
require_once '../app/config/database.php';
require_once '../app/models/UserModel.php';
require_once '../app/controllers/AuthController.php';

session_start();

$db = getDBConnection();
$mustache = new Mustache_Engine([
    'loader' => new Mustache_Loader_FilesystemLoader('../app/views')
]);

$userModel = new UserModel($db);
$authController = new AuthController($userModel, $mustache);

if ($_SERVER['REQUEST_URI'] === '/login') {
    $authController->showLogin();
} elseif ($_SERVER['REQUEST_URI'] === '/login/auth' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->login();
} else {
    echo "Página no encontrada";
}
