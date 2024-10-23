<?php

class PartidaModel
{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPregunta(){
        //Obtener pregunta random de la base
        //$pregunta = $this->conn->query("SELECT * FROM pregunta");
        $pregunta = null;
        return $pregunta;
    }

    public function sumarPuntos($idUsuario)
    {
        $query = "UPDATE usuarios SET puntaje = puntaje + 10 WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->execute();
    }

}