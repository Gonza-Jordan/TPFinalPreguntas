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
include_once ('controller/RegistroController.php');
include_once ('controller/PartidaController.php');
include_once ('controller/RankingController.php');

// Modelos
include_once('model/UsuarioModel.php');
include_once('model/HomeModel.php');
include_once ('model/PartidaModel.php');
include_once ('model/RankingModel.php');

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

    public function getRegistroController()
    {
        return new RegistroController($this->getPresenter(), $this->getDatabase());
    }

    public function getPartidaController()
    {
        return new PartidaController($this->getPresenter(), $this->getPartidaModel());
    }

    public function getPartidaModel()
    {
        return new PartidaModel($this->getDatabase());
    }

    public function getRankingController()
    {
        return new RankingController($this->getPresenter(), $this->getRankingModel());
    }

    public function getRankingModel()
    {
        return new RankingModel($this->getDatabase());
    }

}
