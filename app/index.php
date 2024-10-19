<?php
include_once("config/Configuration.php");

$configuration = new Configuration();
$router = $configuration->getRouter();

$page = $_GET['page'] ?? '';
$action = $_GET['action'] ?? '';

$router->route($page, $action);