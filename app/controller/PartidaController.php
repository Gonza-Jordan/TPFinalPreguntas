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
            $data['resultado'] = ""; // Inicializar el resultado vacío
        }

        $pregunta = $this->model->getPregunta($idUsuario);
        if ($pregunta) {
            $_SESSION['pregunta_actual'] = $pregunta;
            $data['pregunta'] = $pregunta;
        } else {

            $data['pregunta'] = null;
            $data['mensaje'] = "No hay más preguntas disponibles para este usuario.";
        }

        echo json_encode($data);

        $this->presenter->show('crearPartida', $data);
    }

    public function validarRespuesta($idUsuario)
    {
        $respuestaCorrecta = $_SESSION['pregunta_actual']['respuesta_correcta'];
        $respuestaSeleccionada = $_POST['respuesta'];

        if ($respuestaSeleccionada == $respuestaCorrecta) {
            $this->model->sumarPuntos($idUsuario);
            $data['resultado'] = "correcta";
        } else {
            // Guardar la respuesta correcta en los datos para mostrarla en el modal
            $data['resultado'] = "incorrecta";
            $data['respuesta_correcta'] = $respuestaCorrecta;
        }

        return $data;
    }



}