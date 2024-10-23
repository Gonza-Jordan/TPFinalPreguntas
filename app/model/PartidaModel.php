<?php

class PartidaModel
{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPregunta(){
        $pregunta = $this->conn->query("SELECT * FROM preguntas ORDER BY RAND() LIMIT 1")->fetch(PDO::FETCH_ASSOC);
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