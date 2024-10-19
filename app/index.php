<?php
include_once("config/Configuration.php");

$configuration = new Configuration();
$router = $configuration->getRouter();

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'show';

$router->route($page, $action);