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

    public function guardarPreguntaSugerida($categoria, $contenido, $opcionA, $opcionB, $opcionC, $opcionD, $respuestaCorrecta, $creadaPor) {
        $query = "INSERT INTO preguntas_sugeridas (contenido, categoria, nivel_dificultad, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, creada_por, estado) 
                  VALUES (:contenido, :categoria, 'facil', :opcionA, :opcionB, :opcionC, :opcionD, :respuestaCorrecta, :creadaPor, 'Pendiente')";

        $stmt = $this->conn->prepare($query);

        // Vincular parámetros
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':opcionA', $opcionA);
        $stmt->bindParam(':opcionB', $opcionB);
        $stmt->bindParam(':opcionC', $opcionC);
        $stmt->bindParam(':opcionD', $opcionD);
        $stmt->bindParam(':respuestaCorrecta', $respuestaCorrecta);
        $stmt->bindParam(':creadaPor', $creadaPor);

        return $stmt->execute();
    }

    public function obtenerPreguntasSugeridasPendientes() {
        $query = "SELECT * FROM preguntas_sugeridas WHERE estado = 'Pendiente'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aprobarPreguntaSugerida($idPregunta) {
        // Obtener los detalles de la pregunta sugerida
        $query = "SELECT * FROM preguntas_sugeridas WHERE id_pregunta = :idPregunta";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPregunta', $idPregunta);
        $stmt->execute();
        $pregunta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pregunta) {
            // Cambiar estado a "Aprobada"
            $updateQuery = "UPDATE preguntas_sugeridas SET estado = 'Aprobada' WHERE id_pregunta = :idPregunta";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':idPregunta', $idPregunta);
            $updateStmt->execute();

            // Insertar en la tabla principal de preguntas
            $insertQuery = "INSERT INTO preguntas (contenido, categoria, nivel_dificultad, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, creada_por, estado_aprobacion, fecha_creacion) 
                        VALUES (:contenido, :categoria, :nivel_dificultad, :opcionA, :opcionB, :opcionC, :opcionD, :respuestaCorrecta, :creadaPor, 'Aprobada', NOW())";
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->bindParam(':contenido', $pregunta['contenido']);
            $insertStmt->bindParam(':categoria', $pregunta['categoria']);
            $insertStmt->bindParam(':nivel_dificultad', $pregunta['nivel_dificultad']);
            $insertStmt->bindParam(':opcionA', $pregunta['opcion_a']);
            $insertStmt->bindParam(':opcionB', $pregunta['opcion_b']);
            $insertStmt->bindParam(':opcionC', $pregunta['opcion_c']);
            $insertStmt->bindParam(':opcionD', $pregunta['opcion_d']);
            $insertStmt->bindParam(':respuestaCorrecta', $pregunta['respuesta_correcta']);
            $insertStmt->bindParam(':creadaPor', $pregunta['creada_por']);
            return $insertStmt->execute();

        }

        return false;
    }

    public function rechazarPreguntaSugerida($id) {
        // Verificar la conexión
        if ($this->conn == null) {
            echo "Error: No hay conexión a la base de datos.";
            return false;
        }

        $query = "UPDATE preguntas_sugeridas SET estado = 'Rechazada' WHERE id_pregunta = :id";
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            echo "Consulta ejecutada correctamente.<br>";
            return true;
        } else {
            echo "Error al ejecutar la consulta SQL: ";
            print_r($stmt->errorInfo());
            return false;
        }
    }

    public function obtenerPreguntasSugeridasConUsuario() {
        $query = "SELECT ps.*, u.nombre_usuario 
              FROM preguntas_sugeridas ps 
              JOIN usuarios u ON ps.creada_por = u.id_usuario 
              WHERE ps.estado = 'Pendiente'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reportarPregunta($idPartida, $comentario) {
        //Obtener id pregunta
        $query = "SELECT id_pregunta FROM partidas WHERE id_partida = :idPartida";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPartida', $idPartida);
        $stmt->execute();
        $idPregunta = $stmt->fetchColumn();

        //Cambiar estado a 'Reportada'
        $query = "UPDATE preguntas SET estado_aprobacion = 'Reportada' WHERE id_pregunta = :idPregunta";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPregunta', $idPregunta);
        $stmt->execute();

        //Obtener pregunta
        $query = "SELECT * FROM preguntas WHERE id_pregunta = :idPregunta";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPregunta', $idPregunta);
        $stmt->execute();
        $pregunta = $stmt->fetch(PDO::FETCH_ASSOC);

        //Agregar pregunta a tabla de reportes
        $query = "INSERT INTO preguntas_reportadas (id_pregunta, estado, comentario) VALUES (:idPregunta, 'Reportada', :comentario)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPregunta', $idPregunta);
        $stmt->bindParam(':comentario', $comentario);
        $stmt->execute();
    }

    public function obtenerPreguntasReportadas() {
        $query = "SELECT * FROM preguntas_reportadas pr JOIN preguntas p ON pr.id_pregunta = p.id_pregunta WHERE pr.estado = 'Reportada'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function habilitarPreguntaReportada($idPregunta) {
        $query = "DELETE FROM preguntas_reportadas WHERE id_pregunta = :idPregunta";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPregunta', $idPregunta);
        $stmt->execute();

        $query = "UPDATE preguntas SET estado_aprobacion = 'Aprobada' WHERE id_pregunta = :idPregunta";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPregunta', $idPregunta);
        return $stmt->execute();
    }

    public function deshabilitarPreguntaReportada($idPregunta) {
        $query = "UPDATE preguntas_reportadas SET estado = 'Deshabilitada' WHERE id_pregunta = :idPregunta";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPregunta', $idPregunta);
        $stmt->execute();

        $query = "UPDATE preguntas SET estado_aprobacion = 'Deshabilitada' WHERE id_pregunta = :idPregunta";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPregunta', $idPregunta);
        return $stmt->execute();
    }

    public function contarTotalPreguntas() {
        $sql = "SELECT COUNT(*) as total FROM preguntas";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila['total'];
    }
    public function obtenerPreguntasPaginadas($limite, $offset) {
        $sql = "SELECT * FROM preguntas LIMIT :limite OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}