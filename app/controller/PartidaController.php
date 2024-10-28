<?php

class PartidaController
{

    private $presenter;
    private $model;

    public function __construct($presenter, $model){
        $this->presenter = $presenter;
        $this->model = $model;
    }

    public function show()
    {
        SessionHelper::verificarSesion();
        $idUsuario = $_SESSION['user_id'];

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['respuesta'])) {
            $data = $this->validarRespuesta($idUsuario);
        } else {
            $data['resultado'] = "";
        }

        if(isset($_SESSION['pregunta_actual'])){
            $data['pregunta'] = $_SESSION['pregunta_actual'];
        } else {
            $pregunta = $this->model->getPregunta($idUsuario);
            if ($pregunta) {
                $_SESSION['pregunta_actual'] = $pregunta;
                $data['pregunta'] = $pregunta;
            } else {

                $data['pregunta'] = null;
                $data['mensaje'] = "No hay más preguntas disponibles para este usuario.";
            }
        }

        $categoriaActual = $data['pregunta']['categoria'];
        $categoriaJson = file_get_contents('public/data/categorias.json');
        $categorias = json_decode($categoriaJson, true);
        $categoriaDatos = $categorias[strtolower($categoriaActual)] ?? null;

        if ($categoriaDatos) {
            $data['categoria'] = $categoriaActual;
            $data['categoriaColor'] = $categoriaDatos['color'];
            $data['categoriaImagen'] = $categoriaDatos['imagen'];
        }


        $this->presenter->show('crearPartida', $data);
    }

    public function validarRespuesta($idUsuario)
    {
        $respuestaCorrecta = $_SESSION['pregunta_actual']['respuesta_correcta'];
        $respuestaSeleccionada = $_POST['respuesta'];

        if ($respuestaSeleccionada === $respuestaCorrecta) {
            if ($this->model->sumarPuntos($idUsuario)) {
                $data['resultado'] = "correcta";
            } else {
                $data['resultado'] = "error";
                $data['mensaje'] = "No se pudieron sumar los puntos.";
            }
        } else {
            // Obtener el texto de la respuesta correcta en base a la letra
            $opcionCorrecta = $_SESSION['pregunta_actual']['opcion_' . strtolower($respuestaCorrecta)];
            $data['respuesta_correcta'] = $opcionCorrecta; // Guarda el texto de la respuesta correcta
            $data['resultado'] = "incorrecta";
        }

        $_SESSION['pregunta_actual'] = null;
        return $data;
    }

}