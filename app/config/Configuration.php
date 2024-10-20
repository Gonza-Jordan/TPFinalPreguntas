<?php

include_once('Presenter.php');
include_once('MustachePresenter.php');
include_once('vendor/mustache/src/Mustache/Autoloader.php');
include_once('Router.php');
include_once ('Database.php');

include_once ('controller/AuthController.php');
include_once ('controller/HomeController.php');

include_once ('model/UsuarioModel.php');
include_once ('model/HomeModel.php');

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
        return new Router($this, "getAuthController", "show");
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
        return new HomeModel();
    }

    public function getPerfilController()
    {
        return new PerfilController($this->getPresenter(), $this->getPerfilModel());
    }

    public function getPerfilModel()
    {
        return new PerfilModel();
    }

}