<?php

class PartidaModel
{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function buscarPartidaEnCurso($idUsuario)
    {
        $sql = "SELECT * FROM partidas 
            WHERE id_usuario = :idUsuario AND estado = 'enCurso' 
            ORDER BY horario_inicio DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearPartida($idUsuario) {

        $partida['pregunta'] = $this->getPregunta($idUsuario);
        $idPregunta = $partida['pregunta']['id_pregunta'];
        $horarioInicio = date('Y-m-d H:i:s');
        $estado = 'enCurso';

        $sql = "INSERT INTO partidas (id_usuario, id_pregunta, horario_inicio, estado) VALUES (:idUsuario, :idPregunta, :horarioInicio, :estado)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':idPregunta', $idPregunta, PDO::PARAM_INT);
        $stmt->bindParam(':horarioInicio', $horarioInicio, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->execute();

        $partida['id_partida'] = $this->conn->lastInsertId();
        return $partida;
    }

    public function verificarRespuesta($idUsuario, $respuestaSeleccionada, $idPartida) {
        $sql = "SELECT preguntas.id_pregunta, preguntas.respuesta_correcta 
                FROM preguntas 
                JOIN partidas ON preguntas.id_pregunta = partidas.id_pregunta
                WHERE partidas.id_usuario = :idUsuario AND partidas.id_partida = :idPartida";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':idPartida', $idPartida, PDO::PARAM_INT);
        $stmt->execute();
        $respuestaData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($respuestaData) {
            $idPregunta = $respuestaData['id_pregunta'];
            $esCorrecta = ($respuestaData['respuesta_correcta'] == $respuestaSeleccionada);

            $this->actualizarEstadisticasPregunta($idPregunta, $esCorrecta);

            $resultado['esCorrecta'] = $esCorrecta;
            $resultado['opcionCorrecta'] = $respuestaData['respuesta_correcta'];
        } else {
            $resultado['esCorrecta'] = false;
            $resultado['opcionCorrecta'] = null;
        }

        return $resultado;
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
        SELECT id_pregunta, contenido, categoria, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta
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
    private function registrarPreguntaMostrada($idUsuario, $idPregunta) {
        $sql = "INSERT INTO usuarios_preguntas (id_usuario, id_pregunta) VALUES (:idUsuario, :idPregunta)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':idPregunta', $idPregunta, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function sumarPuntos($idUsuario, $idPartida) {
        $sql = "UPDATE usuarios SET puntaje_total = puntaje_total + 10 WHERE id_usuario = :idUsuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "UPDATE partidas SET puntos_sumados = puntos_sumados + 10 WHERE id_partida = :idPartida";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idPartida', $idPartida, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function siguientePregunta($idUsuario, $idPartida) {

        $partida['pregunta'] = $this->getPregunta($idUsuario);
        $idPregunta = $partida['pregunta']['id_pregunta'];
        $horarioInicio = date('Y-m-d H:i:s');

        $sql = "UPDATE partidas 
            SET id_pregunta = :idPregunta, horario_inicio = :horarioInicio
            WHERE id_usuario = :idUsuario AND id_partida = :idPartida";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':idPregunta', $idPregunta, PDO::PARAM_INT);
        $stmt->bindParam(':horarioInicio', $horarioInicio, PDO::PARAM_STR);
        $stmt->bindParam(':idPartida', $idPartida, PDO::PARAM_INT);
        $stmt->execute();

        $partida['id_partida'] = $idPartida;
        return $partida;
    }

    public function finalizarPartida($idPartida, $opcionCorrecta = '') {
        $sqlFinalizar = "UPDATE partidas SET estado = 'finalizada' WHERE id_partida = :idPartida";
        $stmt = $this->conn->prepare($sqlFinalizar);
        $stmt->bindParam(':idPartida', $idPartida, PDO::PARAM_INT);
        $stmt->execute();

        if($opcionCorrecta != '') {

        $sqlPregunta = "SELECT id_pregunta FROM partidas WHERE id_partida = :idPartida";
        $stmt = $this->conn->prepare($sqlPregunta);
        $stmt->bindParam(':idPartida', $idPartida, PDO::PARAM_INT);
        $stmt->execute();
        $idPregunta = $stmt->fetchColumn();

        $columnaOpcion = 'opcion_' . strtolower($opcionCorrecta);

        $sqlRespuesta = "SELECT $columnaOpcion FROM preguntas WHERE id_pregunta = :idPregunta";
        $stmt = $this->conn->prepare($sqlRespuesta);
        $stmt->bindParam(':idPregunta', $idPregunta, PDO::PARAM_INT);
        $stmt->execute();
        $respuestaCorrecta = $stmt->fetchColumn();

        return $respuestaCorrecta;
        }
    }


    public function entregarUltimaPregunta($idUsuario, $idPartida) {
        $sql = "SELECT preguntas.*
            FROM preguntas
            JOIN partidas ON preguntas.id_pregunta = partidas.id_pregunta
            WHERE partidas.id_usuario = :idUsuario AND partidas.id_partida = :idPartida AND partidas.estado = 'enCurso'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':idPartida', $idPartida, PDO::PARAM_INT);
        $stmt->execute();
        $pregunta = $stmt->fetch(PDO::FETCH_ASSOC);
        return $pregunta;
    }
    private function actualizarEstadisticasPregunta($idPregunta, $esCorrecta) {
        $sqlUpdate = "UPDATE preguntas SET veces_respondida = veces_respondida + 1 WHERE id_pregunta = :idPregunta";
        $stmt = $this->conn->prepare($sqlUpdate);
        $stmt->bindParam(':idPregunta', $idPregunta, PDO::PARAM_INT);
        $stmt->execute();

        if ($esCorrecta) {
            $sqlUpdateCorrectas = "UPDATE preguntas SET veces_respondida_correctamente = veces_respondida_correctamente + 1 WHERE id_pregunta = :idPregunta";
            $stmt = $this->conn->prepare($sqlUpdateCorrectas);
            $stmt->bindParam(':idPregunta', $idPregunta, PDO::PARAM_INT);
            $stmt->execute();
        }

        $this->actualizarDificultad($idPregunta);
    }

    private function actualizarDificultad($idPregunta) {
        $sql = "SELECT veces_respondida, veces_respondida_correctamente FROM preguntas WHERE id_pregunta = :idPregunta";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idPregunta', $idPregunta, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data && $data['veces_respondida'] > 0) {
            $vecesRespondida = $data['veces_respondida'];
            $vecesRespondidaCorrectamente = $data['veces_respondida_correctamente'];

            $porcentajeCorrectas = ($vecesRespondidaCorrectamente / $vecesRespondida) * 100;

            $nivelDificultad = 'Medio';
            if ($porcentajeCorrectas > 70) {
                $nivelDificultad = 'Facil';
            } elseif ($porcentajeCorrectas < 30) {
                $nivelDificultad = 'Dificil';
            }

            $sqlUpdateDificultad = "UPDATE preguntas SET nivel_dificultad = :nivelDificultad WHERE id_pregunta = :idPregunta";
            $stmt = $this->conn->prepare($sqlUpdateDificultad);
            $stmt->bindParam(':nivelDificultad', $nivelDificultad, PDO::PARAM_STR);
            $stmt->bindParam(':idPregunta', $idPregunta, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    public function obtenerRatioRespuestasCorrectas($idUsuario) {
        $sql = "SELECT COUNT(*) as total_respuestas,
                   SUM(CASE WHEN es_correcta = 1 THEN 1 ELSE 0 END) as respuestas_correctas
            FROM respuestas_usuarios
            WHERE id_usuario = :idUsuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data['total_respuestas'] > 0) {
            return ($data['respuestas_correctas'] / $data['total_respuestas']) * 100;
        } else {
            return null;
        }
    }
    public function getPreguntaAcordeDificultad($idUsuario) {
        $ratio = $this->obtenerRatioRespuestasCorrectas($idUsuario);

        if ($ratio === null) {
            $dificultad = null;
        } elseif ($ratio > 70) {
            $dificultad = 'Dificil';
        } elseif ($ratio < 30) {
            $dificultad = 'Facil';
        } else {
            $dificultad = 'Medio';
        }

        $sql = "SELECT * FROM preguntas WHERE nivel_dificultad = :dificultad ORDER BY RAND() LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dificultad', $dificultad, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}