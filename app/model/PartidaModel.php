<?php

class PartidaModel
{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPregunta(){
        $pregunta = $this->conn->query("SELECT contenido, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta FROM preguntas ORDER BY RAND() LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        return $pregunta;
    }

    public function sumarPuntos($idUsuario)
    {
        $query = "UPDATE usuarios SET puntaje_total = puntaje_total + 10 WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->execute();
    }

}