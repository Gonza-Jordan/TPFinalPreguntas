<?php

class EditorController
{
    private $presenter;
    private $model;
    public function __construct($presenter, $model) {
        $this->presenter = $presenter;
        $this->model = $model;

        SessionHelper::verificarSesion();
    }
    public function mostrarPanelEditor() {
        $preguntasReportadas = $this->model->obtenerPreguntasReportadas();
        $preguntasSugeridas = $this->model->obtenerPreguntasSugeridas();

        $this->presenter->render('panelEditor', [
            'preguntasReportadas' => $preguntasReportadas,
            'preguntasSugeridas' => $preguntasSugeridas
        ]);
    }

    public function aprobarReportada($id) {
        $this->model->aprobarPreguntaReportada($id);
        header('Location: /TPFinalPreguntas/editor');
    }

    public function deshabilitarReportada($id) {
        $this->model->deshabilitarPreguntaReportada($id);
        header('Location: /TPFinalPreguntas/editor');
    }

    public function aprobarSugerida($id) {
        $this->model->aprobarPreguntaSugerida($id);
        header('Location: /TPFinalPreguntas/editor');
    }

    public function rechazarSugerida($id) {
        $this->model->rechazarPreguntaSugerida($id);
        header('Location: /TPFinalPreguntas/editor');
    }
}

