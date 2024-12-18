<?php

class PartidaController
{
    private $presenter;
    private $model;

    public function __construct($presenter, $model) {
        $this->presenter = $presenter;
        $this->model = $model;

        SessionHelper::verificarSesion();
    }

    public function show($partida = null) {
        SessionHelper::verificarSesion();

        if ($partida === null || !isset($partida['pregunta'])) {
            header("Location: /TPFinalPreguntas/index.php");
            exit();
        }

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

    public function crearPartida() {
        SessionHelper::verificarSesion();
        $idUsuario = $_SESSION['user_id'];

        $partidaEnCurso = $this->model->buscarPartidaEnCurso($idUsuario);
        if ($partidaEnCurso) {
            $horarioInicio = new DateTime($partidaEnCurso['horario_inicio']);
            $ahora = new DateTime();
            $diferenciaSegundos = $horarioInicio->diff($ahora)->s;

            if ($diferenciaSegundos < 15) {
                $_SESSION['id_partida'] = $partidaEnCurso['id_partida'];
                $ultimaPregunta = $this->model->entregarUltimaPregunta($idUsuario, $partidaEnCurso['id_partida']);
                $data['pregunta'] = $ultimaPregunta;
                $this->show($data);
                return;
            } else {
                $this->model->finalizarPartida($partidaEnCurso['id_partida']);
            }
        }

        $partida = $this->model->crearPartida($idUsuario);
        if ($partida) {
            $_SESSION['id_partida'] = $partida['id_partida'];
        } else {
            $partida['pregunta'] = null;
            $partida['mensaje'] = "No hay más preguntas disponibles para este usuario.";
        }

        $this->show($partida);
    }

    public function verificarRespuesta($idUsuario = null, $respuestaSeleccionada = null, $idPartida = null) {
        SessionHelper::verificarSesion();

        $idUsuario = $idUsuario ?? $_SESSION['user_id'];
        $idPartida = $idPartida ?? $_SESSION['id_partida'];

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['respuesta'])) {
            $respuestaSeleccionada = $_POST['respuesta'];

            // Llama a la función del modelo para verificar y actualizar estadísticas
            $resultado = $this->model->verificarRespuesta($idUsuario, $respuestaSeleccionada, $idPartida);

            if ($resultado['esCorrecta']) {
                $this->model->sumarPuntos($idUsuario, $idPartida);
                $this->siguientePregunta($idUsuario, $idPartida);
            } else {
                $opcionCorrecta = $resultado['opcionCorrecta'];
                $this->finalizarPartida($idUsuario, $idPartida, $opcionCorrecta);
            }
        } else {
            $this->model->entregarUltimaPregunta($idUsuario, $idPartida);
        }
    }

    public function siguientePregunta($idUsuario, $idPartida) {
        $partida = $this->model->siguientePregunta($idUsuario, $idPartida);
        $this->show($partida);
    }

    public function finalizarPartida($idUsuario, $idPartida, $opcionCorrecta) {
        $ultimaPregunta = $this->model->entregarUltimaPregunta($idUsuario, $idPartida);
        $respuestaCorrecta = $this->model->finalizarPartida($idPartida, $opcionCorrecta);

        $data['pregunta'] = $ultimaPregunta;
        $data['respuesta_correcta'] = $respuestaCorrecta;
        $data['resultado'] = "incorrecta";

        $this->show($data);
    }

}
