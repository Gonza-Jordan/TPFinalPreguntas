<?php

class PerfilController
{

    private $presenter;
    private $model;

    public function __construct($presenter, $model){
        $this->presenter = $presenter;
        $this->model = $model;
    }

    public function show(){
        $this->presenter->show('perfil');
    }

}