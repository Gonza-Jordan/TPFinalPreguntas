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
        $idUsuario = $_SESSION['user_id']; //Esto es para poder mandarle el usuario al modelo y que le dé una pregunta customizada

        $data['pregunta'] = $this->model->getPregunta();

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['respuesta'])) {
            $data = $this->validarRespuesta($data, $idUsuario);
        } else {
            // Si no se envió ninguna respuesta, recargar solo con la pregunta
            $data['resultado'] = "";
        }

        // Mostrar la vista con los datos
        $this->presenter->show('crearPartida', $data);
    }

    public function validarRespuesta($data, $idUsuario)
    {
        $respuestaCorrecta = $data['pregunta']['respuesta_correcta'];
        $respuestaSeleccionada = $_POST['respuesta'];

        echo "Respuesta seleccionada: " . $respuestaSeleccionada . "<br>";
        echo "Respuesta correcta: " . $respuestaCorrecta . "<br>";

        if ($respuestaSeleccionada == $respuestaCorrecta) {
            $this->model->sumarPuntos($idUsuario);
            $data['resultado'] = "correcta";
        } else {
            $data['resultado'] = "incorrecta";
//            header("Location: home");
//            exit();

        }

        $data['respuestaSeleccionada'] = $respuestaSeleccionada;

        return $data;
    }


}