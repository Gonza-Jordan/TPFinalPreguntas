<?php

class PreguntaController {
    private $preguntaModel;
    private $presenter;

    public function __construct($preguntaModel, $presenter) {
        $this->preguntaModel = $preguntaModel;
        $this->presenter = $presenter;
    }

    public function listar() {
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $preguntasPorPagina = 5;
        $totalPreguntas = $this->preguntaModel->contarTotalPreguntas();
        $totalPaginas = ceil($totalPreguntas / $preguntasPorPagina);

        $offset = ($pagina - 1) * $preguntasPorPagina;
        $preguntas = $this->preguntaModel->obtenerPreguntasPaginadas($preguntasPorPagina, $offset);

        $prevPage = $pagina > 1 ? $pagina - 1 : null;
        $nextPage = $pagina < $totalPaginas ? $pagina + 1 : null;

        $this->presenter->show('listarPreguntas', [
            'preguntas' => $preguntas,
            'paginaActual' => $pagina,
            'totalPaginas' => $totalPaginas,
            'prevPage' => $prevPage,
            'nextPage' => $nextPage
        ]);
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

            header("Location: /TPFinalPreguntas/pregunta/listar");
            exit();
        } else {
            $data = [
                'esEditor' => ($_SESSION['tipo_usuario'] === 'editor')
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
            header('Location: /TPFinalPreguntas/pregunta/listar');
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

            header("Location: /TPFinalPreguntas/home/show&mensaje=sugerencia_enviada");
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
            header("Location: /TPFinalPreguntas/pregunta/revisarSugerencias&mensaje=aprobada");
        } else {
            header("Location: /TPFinalPreguntas/app/pregunta/revisarSugerencias&mensaje=error");
        }
        exit();
    }

    public function rechazarPregunta($idPregunta) {
        if (is_null($idPregunta)) {
            echo "Error: ID de la pregunta es nulo";
            exit;
        }

        if ($this->preguntaModel->rechazarPreguntaSugerida($idPregunta)) {
            echo "<script>alert('Pregunta rechazada correctamente'); window.location.href = '/TPFinalPreguntas/pregunta/revisarSugerencias&mensaje=rechazada';</script>";
        } else {
            echo "<script>alert('Error al rechazar la pregunta'); window.location.href = '/TPFinalPreguntas/index.php/pregunta/revisarSugerencias&mensaje=error';</script>";
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

        header("Location: /TPFinalPreguntas/home/show&mensaje=reporte_enviado");
        exit();
    }

    public function revisarReportes() {
        $preguntasReportadas = $this->preguntaModel->obtenerPreguntasReportadas();
        $this->presenter->show('revisarReportes', ['preguntasReportadas' => $preguntasReportadas]);
    }

    public function habilitarPreguntaReportada($idPregunta) {
        $idPregunta = $_GET['id'];
        if ($this->preguntaModel->habilitarPreguntaReportada($idPregunta)) {
            $_SESSION['mensaje'] = "Pregunta habilitada correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al habilitar la pregunta.";
        }

        header("Location: /TPFinalPreguntas/pregunta/revisarReportes");
        exit();
    }

    public function deshabilitarPreguntaReportada($idPregunta) {
        $idPregunta = $_GET['id'];
        if ($this->preguntaModel->deshabilitarPreguntaReportada($idPregunta)) {
            $_SESSION['mensaje'] = "Pregunta deshabilitada correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al deshabilitar la pregunta.";
        }

        header("Location: /TPFinalPreguntas/pregunta/revisarReportes");
        exit();
    }
}
