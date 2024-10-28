<?php

class PartidaModel
{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPregunta($idUsuario) {

        $sqlTotalPreguntas = "SELECT COUNT(*) FROM preguntas";
        $stmt = $this->conn->prepare($sqlTotalPreguntas);
        $stmt->execute();
        $totalPreguntas = $stmt->fetchColumn();

        $sqlRespondidas = "
        SELECT COUNT(*)
        FROM usuarios_preguntas
        WHERE id_usuario = :idUsuario
    ";
        $stmt = $this->conn->prepare($sqlRespondidas);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        $preguntasRespondidas = $stmt->fetchColumn();


        if ($preguntasRespondidas >= $totalPreguntas) {
            $sqlReset = "DELETE FROM usuarios_preguntas WHERE id_usuario = :idUsuario";
            $stmt = $this->conn->prepare($sqlReset);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
        }

        $sql = "
        SELECT id_pregunta, contenido, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta
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


    public function sumarPuntos($idUsuario) {
        $sql = "UPDATE usuarios SET puntaje_total = puntaje_total + 10 WHERE id_usuario = :idUsuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
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