<?php

class PreguntaController {
    private $preguntaModel;
    private $presenter;

    public function __construct($preguntaModel, $presenter) {
        $this->preguntaModel = $preguntaModel;
        $this->presenter = $presenter;
    }

    public function listar() {
        $preguntas = $this->preguntaModel->obtenerTodas(); // Suponiendo que este método existe y obtiene todas las preguntas
        $this->presenter->show('listarPreguntas', ['preguntas' => $preguntas]);
    }

    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria = $_POST['category'];
            $pregunta = $_POST['newQuestion'];
            $opcionA = $_POST['optionA'];
            $opcionB = $_POST['optionB'];
            $opcionC = $_POST['optionC'];
            $opcionD = $_POST['optionD'];
            $respuesta_correcta = $_POST['correctOption'];
            $this->preguntaModel->guardarPregunta($categoria, $pregunta, $opcionA, $opcionB, $opcionC, $opcionD, $respuesta_correcta);

            header("Location: /TPFinalPreguntas/app/index.php?page=pregunta&action=listar");
            exit();
        } else {
            $data = [
                'esEditor' => ($_SESSION['tipo_usuario'] === 'editor') // Verifica si el usuario es editor
            ];
            $this->presenter->show('crearPregunta', $data);
        }
    }

    public function editar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pregunta = $_POST['pregunta'];
            $opciones = $_POST['opciones'];
            $respuesta_correcta = $_POST['respuesta_correcta'];
            $this->preguntaModel->actualizarPregunta($id, $pregunta, $opciones, $respuesta_correcta);
            header('Location: /TPFinalPreguntas/app/index.php?page=pregunta&action=listar');
            exit();
        } else {
            $pregunta = $this->preguntaModel->obtenerPorId($id);
            $this->presenter->show('editarPregunta', ['pregunta' => $pregunta]);
        }
    }

}
