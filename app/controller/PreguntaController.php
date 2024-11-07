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

    public function sugerir() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria = $_POST['category'];
            $contenido = $_POST['suggestedQuestion'];
            $opcionA = $_POST['optionA'];
            $opcionB = $_POST['optionB'];
            $opcionC = $_POST['optionC'];
            $opcionD = $_POST['optionD'];
            $respuestaCorrecta = $_POST['correctOption'];
            $creadaPor = $_SESSION['user_id'];

            $this->preguntaModel->guardarPreguntaSugerida($categoria, $contenido, $opcionA, $opcionB, $opcionC, $opcionD, $respuestaCorrecta, $creadaPor);

            // Redirigir a una página de confirmación o a la página principal
            header("Location: /TPFinalPreguntas/app/index.php?page=home&action=show&mensaje=sugerencia_enviada");
            exit();
        } else {
            $this->presenter->show('sugerirPregunta');
        }
    }
    public function listarPreguntas() {
        $preguntas = $this->preguntaModel->obtenerPreguntasSugeridasPendientes();
        $this->presenter->show('preguntasSugeridas', ['preguntas' => $preguntas]);
    }

    public function aprobarPregunta($idPregunta) {
        if ($this->preguntaModel->aprobarPreguntaSugerida($idPregunta)) {
            header("Location: /TPFinalPreguntas/app/index.php?page=pregunta&action=revisarSugerencias&mensaje=aprobada");
        } else {
            header("Location: /TPFinalPreguntas/app/index.php?page=pregunta&action=revisarSugerencias&mensaje=error");
        }
        exit();
    }

    public function rechazarPregunta($idPregunta) {
        if (is_null($idPregunta)) {
            echo "Error: ID de la pregunta es nulo";
            exit;
        }

        if ($this->preguntaModel->rechazarPreguntaSugerida($idPregunta)) {
            echo "<script>alert('Pregunta rechazada correctamente'); window.location.href = '/TPFinalPreguntas/app/index.php?page=pregunta&action=revisarSugerencias&mensaje=rechazada';</script>";
        } else {
            echo "<script>alert('Error al rechazar la pregunta'); window.location.href = '/TPFinalPreguntas/app/index.php?page=pregunta&action=revisarSugerencias&mensaje=error';</script>";
        }
        exit();
    }

    public function revisarSugerencias() {
        $preguntasSugeridas = $this->preguntaModel->obtenerPreguntasSugeridasConUsuario();
        $this->presenter->show('revisarSugerencias', ['preguntasSugeridas' => $preguntasSugeridas]);
    }

    public function reportarPregunta(){
        $idPartida = $_SESSION['id_partida'];
        $comentario = $_POST['comentario'];
        $this->preguntaModel->reportarPregunta($idPartida, $comentario);

        // Redirigir a una página de confirmación o a la página principal
        header("Location: /TPFinalPreguntas/app/index.php?page=home&action=show&mensaje=reporte_enviado");
        exit();
    }

    public function revisarReportes(){
        $preguntasReportadas = $this->preguntaModel->obtenerPreguntasReportadas();
        $this->presenter->show('revisarReportes', ['preguntasReportadas' => $preguntasReportadas]);
    }

    public function habilitarPreguntaReportada($idPregunta) {
        $idPregunta = $_GET['id'];
        $this->preguntaModel->habilitarPreguntaReportada($idPregunta);
    }

    public function deshabilitarPreguntaReportada($idPregunta) {
        $idPregunta = $_GET['id'];
        $this->preguntaModel->deshabilitarPreguntaReportada($idPregunta);
    }



}
