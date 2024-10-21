<?php

include_once('Presenter.php');
include_once('MustachePresenter.php');
include_once('vendor/mustache/src/Mustache/Autoloader.php');
include_once('Router.php');
include_once('Database.php');

// Controladores
include_once('controller/AuthController.php');
include_once('controller/HomeController.php');
include_once('controller/PerfilController.php');  // Agregado el controlador de perfil
include_once('controller/UsuarioController.php'); // Agregado el controlador de usuario

// Modelos
include_once('model/UsuarioModel.php');
include_once('model/HomeModel.php');

class Configuration
{
    public function __construct()
    {
    }

    public function getPresenter()
    {
        return new MustachePresenter("view");
    }

    public function getRouter()
    {
        return new Router($this, "getHomeController", "show");
    }

    public function getAuthController()
    {
        return new AuthController($this->getPresenter(), $this->getUserModel());
    }

    public function getUserModel()
    {
        return new UsuarioModel($this->getDatabase());
    }

    public function getDatabase()
    {
        $db = new Database();
        return $db->getConnection();
    }

    public function getHomeController()
    {
        return new HomeController($this->getPresenter(), $this->getHomeModel());
    }

    public function getHomeModel()
    {
        return new HomeModel($this->getDatabase());
    }

    public function getUsuarioController()
    {
        return new UsuarioController($this->getDatabase());
    }
    public function getPerfilController()
    {
        return new PerfilController($this->getDatabase());
    }

}
