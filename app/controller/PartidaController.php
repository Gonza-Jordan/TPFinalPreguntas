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

        $data['pregunta'] = $this->model->getPregunta();
        $_SESSION['pregunta_actual'] = $data['pregunta'];

        // Mostrar la vista con los datos
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