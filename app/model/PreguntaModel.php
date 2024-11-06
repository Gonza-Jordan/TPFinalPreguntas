<?php

class PreguntaModel {
    private $conn;
    private $table_name = "preguntas";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function guardarPregunta($categoria, $contenido, $opcionA, $opcionB, $opcionC, $opcionD, $respuestaCorrecta) {
        $query = "INSERT INTO preguntas (contenido, categoria, nivel_dificultad, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, estado_aprobacion, fecha_creacion) 
              VALUES (:contenido, :categoria, 'facil', :opcionA, :opcionB, :opcionC, :opcionD, :respuestaCorrecta, 'En Revisión', NOW())";

        $stmt = $this->conn->prepare($query);

        // Vincular parámetros
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':opcionA', $opcionA);
        $stmt->bindParam(':opcionB', $opcionB);
        $stmt->bindParam(':opcionC', $opcionC);
        $stmt->bindParam(':opcionD', $opcionD);
        $stmt->bindParam(':respuestaCorrecta', $respuestaCorrecta);

        return $stmt->execute();
    }

    public function obtenerTodas() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarPregunta($id, $categoria, $pregunta, $opcionA, $opcionB, $opcionC, $opcionD, $respuesta_correcta) {
        $query = "UPDATE " . $this->table_name . "
            SET
                categoria = :categoria,
                pregunta = :pregunta,
                opcionA = :opcionA,
                opcionB = :opcionB,
                opcionC = :opcionC,
                opcionD = :opcionD,
                respuesta_correcta = :respuesta_correcta
            WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":categoria", $categoria);
        $stmt->bindParam(":pregunta", $pregunta);
        $stmt->bindParam(":opcionA", $opcionA);
        $stmt->bindParam(":opcionB", $opcionB);
        $stmt->bindParam(":opcionC", $opcionC);
        $stmt->bindParam(":opcionD", $opcionD);
        $stmt->bindParam(":respuesta_correcta", $respuesta_correcta);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function eliminarPregunta($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
