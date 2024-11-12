<?php

include_once('Presenter.php');
include_once('MustachePresenter.php');
include_once('vendor/mustache/src/Mustache/Autoloader.php');
include_once('Router.php');
include_once('Database.php');

// Controladores
include_once('controller/AuthController.php');
include_once('controller/HomeController.php');
include_once('controller/PerfilController.php');
include_once('controller/UsuarioController.php');
include_once('controller/PreguntaController.php');
include_once ('controller/RegistroController.php');
include_once ('controller/PartidaController.php');
include_once ('controller/RankingController.php');
include_once ('controller/EditorController.php');

// Modelos
include_once('model/UsuarioModel.php');
include_once('model/HomeModel.php');
include_once ('model/PartidaModel.php');
include_once ('model/PreguntaModel.php');
include_once ('model/RankingModel.php');
include_once ('model/EditorModel.php');

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

    public function getHomeController() {
        $presenter = $this->getPresenter();
        $usuarioModel = $this->getUserModel();
        $preguntaModel = $this->getPreguntaModel(); // Asegúrate de que este método existe y devuelve una instancia de PreguntaModel
        return new HomeController($presenter, $usuarioModel, $preguntaModel);
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

    public function getRegistroController() {
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
        return new RankingController($this->getPresenter(),$this->getUserModel(), $this->getPartidaModel(), $this->getRankingModel());
    }

    public function getRankingModel()
    {
        return new RankingModel($this->getDatabase());
    }

    public function getEditorController()
    {
        return new EditorController($this->getPresenter(), $this->getEditorModel());
    }

    public function getEditorModel()
    {
        return new EditorModel($this->getDatabase());
    }
    public function getPreguntaController() {
        $db = $this->getDatabase();
        $preguntaModel = new PreguntaModel($db);
        return new PreguntaController($preguntaModel, $this->getPresenter()); // Pasa el presenter también
    }
    public function getPreguntaModel() {
        return new PreguntaModel($this->getDatabase());
    }
}