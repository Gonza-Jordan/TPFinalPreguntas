<?php

class EditorModel
{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerPreguntasReportadas() {
        $query = "SELECT pr.id, p.texto, pr.estado 
                  FROM preguntas_reportadas pr
                  JOIN preguntas p ON pr.id_pregunta = p.id
                  WHERE pr.estado = 'Reportada'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPreguntasSugeridas() {
        $query = "SELECT ps.id, ps.texto, ps.estado 
                  FROM preguntas_sugeridas ps
                  WHERE ps.estado = 'Pendiente'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aprobarPreguntaReportada($idPregunta) {
        $query = "UPDATE preguntas_reportadas SET estado = 'Aprobada' WHERE id_pregunta = :id;
                  UPDATE preguntas SET estado_de_aprobacion = 'Aprobada' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $idPregunta);
        return $stmt->execute();
    }

    public function deshabilitarPreguntaReportada($idPregunta) {
        $query = "UPDATE preguntas_reportadas SET estado = 'Deshabiltada' WHERE id_pregunta = :id;
                  UPDATE preguntas SET estado_de_aprobacion = 'Rechazada' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $idPregunta);
        return $stmt->execute();
    }

    public function aprobarPreguntaSugerida($idPregunta) {
        $query = "UPDATE preguntas_sugeridas SET estado = 'Aprobada' WHERE id = :id;
                  INSERT INTO preguntas (texto, estado_de_aprobacion) 
                  SELECT texto, 'Aprobada' FROM preguntas_sugeridas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $idPregunta);
        return $stmt->execute();
    }

    public function rechazarPreguntaSugerida($idPregunta) {
        $query = "UPDATE preguntas_sugeridas SET estado = 'Rechazada' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $idPregunta);
        return $stmt->execute();
    }
}