<?php

class PartidaController
{

    private $presenter;
    private $model;

    public function __construct($presenter, $model){
        $this->presenter = $presenter;
        $this->model = $model;
    }

    public function show() {
        SessionHelper::verificarSesion();
        $idUsuario = $_SESSION['user_id'];
        $pregunta = $this->model->getPregunta($idUsuario);
        $this->presenter->show('crearPartida', $pregunta);
    }



}