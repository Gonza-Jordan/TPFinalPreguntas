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

    public function verificarRespuesta($idUsuario, $respuesta, $idPartida) {
        $sql = "SELECT preguntas.respuesta_correcta 
            FROM preguntas 
            JOIN partidas ON preguntas.id_pregunta = partidas.id_pregunta
            WHERE partidas.id_usuario = :idUsuario AND partidas.id_partida = :idPartida";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':idPartida', $idPartida, PDO::PARAM_INT);
        $stmt->execute();
        $respuestaCorrecta = $stmt->fetch(PDO::FETCH_ASSOC);
        $resultado['esCorrecta'] = $respuestaCorrecta['respuesta_correcta'] == $respuesta;
        $resultado['opcionCorrecta'] = $respuestaCorrecta['respuesta_correcta'];
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

    public function sumarPuntos($idUsuario) {
        $sql = "UPDATE usuarios SET puntaje_total = puntaje_total + 10 WHERE id_usuario = :idUsuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
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


}