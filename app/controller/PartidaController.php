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
        var_dump($partida);
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

        $partida = $this->model->crearPartida($idUsuario);
        if ($partida) {
            $_SESSION['id_partida'] = $partida['id_partida'];
//            $categoriaActual = $partida['pregunta']['categoria'];
//            $categoriaJson = file_get_contents('public/data/categorias.json');
//            $categorias = json_decode($categoriaJson, true);
//            $categoriaDatos = $categorias[strtolower($categoriaActual)] ?? null;
        } else {
            $partida['pregunta'] = null;
            $partida['mensaje'] = "No hay más preguntas disponibles para este usuario.";
        }

//        if ($categoriaDatos) {
//            $partida['categoria'] = $categoriaActual;
//            $partida['categoriaColor'] = $categoriaDatos['color'];
//            $partida['categoriaImagen'] = $categoriaDatos['imagen'];
//        }

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
                //$data['resultado'] = "incorrecta";
                $this->finalizarPartida($idPartida, $opcionCorrecta);
            }
        } else {
            $this->model->entregarUltimaPregunta();
        }

    }


    public function siguientePregunta($idUsuario, $idPartida){
        $partida = $this->model->siguientePregunta($idUsuario, $idPartida);
        $this->show($partida);
    }

    public function finalizarPartida($idPartida, $opcionCorrecta){
        $respuestaCorrecta = $this->model->finalizarPartida($idPartida, $opcionCorrecta);
        $data['respuesta_correcta'] = $respuestaCorrecta;

        $this->show($data);
    }

}