<?php

class PartidaModel
{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPregunta($idUsuario){
        //Obtener pregunta random de la base
        return null;
    }

}