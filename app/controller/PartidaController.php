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

        $data['pregunta'] = $this->model->getPregunta($idUsuario);
        $_SESSION['pregunta_actual'] = $data['pregunta'];
        echo json_encode($data);
        echo json_encode($idUsuario);
        // Mostrar la vista con los datos
//        $this->presenter->show('crearPartida', $data);

        // Obtener la categoría con su color
        $categoriaActual = $data['pregunta']['categoria'];
        $categoriaJson = file_get_contents('public/data/categorias.json');
        $categorias = json_decode($categoriaJson, true);

        $categoriaDatos = $categorias[strtolower($categoriaActual)] ?? null;

        // Agregar los datos de la categoría al array $data
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
