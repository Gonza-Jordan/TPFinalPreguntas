<?php

class PartidaController
{

    private $presenter;
    private $model;

    public function __construct($presenter, $model){
        $this->presenter = $presenter;
        $this->model = $model;
    }

    public function show($partida)
    {
//        var_dump($partida);
        SessionHelper::verificarSesion();

        $categoriaActual = $partida['pregunta']['categoria'];
        $categoriaJson = file_get_contents('public/data/categorias.json');
        $categorias = json_decode($categoriaJson, true);
        $categoriaDatos = $categorias[strtolower($categoriaActual)] ?? null;
        if ($categoriaDatos) {
            $partida['categoria'] = $categoriaActual;
            $partida['categoriaColor'] = $categoriaDatos['color'];
            $partida['categoriaImagen'] = $categoriaDatos['imagen'];
        }

        $this->presenter->show('crearPartida', $partida);
    }

    public function crearPartida()
    {
        SessionHelper::verificarSesion();
        $idUsuario = $_SESSION['user_id'];

        // Verificar si hay una partida en curso para el usuario
        $partidaEnCurso = $this->model->buscarPartidaEnCurso($idUsuario);
        if ($partidaEnCurso) {
            // Calcular la diferencia en segundos entre el horario de inicio de la partida y el tiempo actual
            $horarioInicio = new DateTime($partidaEnCurso['horario_inicio']);
            $ahora = new DateTime();
            $diferenciaSegundos = $horarioInicio->diff($ahora)->s; // Calcula la diferencia en segundos

            if ($diferenciaSegundos < 15) {
                // Si la partida es reciente (menos de 15 segundos), retornar la partida existente
                $_SESSION['id_partida'] = $partidaEnCurso['id_partida'];
                $ultimaPregunta = $this->model->entregarUltimaPregunta($idUsuario, $partidaEnCurso['id_partida']);
                $data['pregunta'] = $ultimaPregunta;
                $this->show($data);
                return;
            } else {
                // Si la partida tiene más de 15 segundos, finalizarla
                $this->model->finalizarPartida($partidaEnCurso['id_partida']);
            }
        }

        // Si no hay partida en curso reciente o la anterior fue finalizada, crear una nueva
        $partida = $this->model->crearPartida($idUsuario);
        if ($partida) {
            $_SESSION['id_partida'] = $partida['id_partida'];
        } else {
            $partida['pregunta'] = null;
            $partida['mensaje'] = "No hay más preguntas disponibles para este usuario.";
        }

        $this->show($partida);
    }

    public function verificarRespuesta()
    {
        SessionHelper::verificarSesion();
        $idUsuario = $_SESSION['user_id'];

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['respuesta'])) {
            $respuestaSeleccionada = $_POST['respuesta'];
            $idPartida = $_SESSION['id_partida'];
            $resultado = $this->model->verificarRespuesta($idUsuario, $respuestaSeleccionada, $idPartida);

            if ($resultado['esCorrecta']) {
                //$data['resultado'] = "correcta";
                $this->model->sumarPuntos($idUsuario);
                $this->siguientePregunta($idUsuario, $idPartida);
            } else {
                $opcionCorrecta = $resultado['opcionCorrecta'];
                $this->finalizarPartida($idUsuario, $idPartida, $opcionCorrecta);
            }
        } else {
            $this->model->entregarUltimaPregunta();
        }

    }


    public function siguientePregunta($idUsuario, $idPartida){
        $partida = $this->model->siguientePregunta($idUsuario, $idPartida);
        $this->show($partida);
    }

    public function finalizarPartida($idUsuario, $idPartida, $opcionCorrecta){
        $ultimaPregunta = $this->model->entregarUltimaPregunta($idUsuario, $idPartida);
        $respuestaCorrecta = $this->model->finalizarPartida($idPartida, $opcionCorrecta);

        $data['pregunta'] = $ultimaPregunta;
        $data['respuesta_correcta'] = $respuestaCorrecta;
        $data['resultado'] = "incorrecta";

        $this->show($data);
    }

}