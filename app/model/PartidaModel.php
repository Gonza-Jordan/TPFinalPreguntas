<?php

class PartidaModel
{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPregunta($idUsuario) {
        $sql = "
            SELECT p.id_pregunta, p.pregunta
            FROM preguntas p
            WHERE p.id_pregunta NOT IN (
                SELECT up.id_pregunta
                FROM usuarios_preguntas up
                WHERE up.id_usuario = :idUsuario
            )
            ORDER BY RAND()
            LIMIT 1
        ";


        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();


        $pregunta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pregunta) {
            $this->registrarPreguntaMostrada($idUsuario, $pregunta['id_pregunta']);
        }

        return $pregunta;
    }

    // Metodo para registrar que una pregunta ha sido mostrada a un usuario
    private function registrarPreguntaMostrada($idUsuario, $idPregunta) {
        $sql = "INSERT INTO usuarios_preguntas (id_usuario, id_pregunta) VALUES (:idUsuario, :idPregunta)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':idPregunta', $idPregunta, PDO::PARAM_INT);
        $stmt->execute();
    }

}