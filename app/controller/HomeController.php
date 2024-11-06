<?php
require_once __DIR__ . '/../helper/SessionHelper.php';

class HomeController {
    private $presenter;
    private $model;

    public function __construct($presenter, $model) {
        $this->presenter = $presenter;
        $this->model = $model;
    }

    public function show() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /TPFinalPreguntas/app/index.php?page=auth&action=show');
            exit();
        }

        $id_usuario = $_SESSION['user_id'];
        $usuario = $this->model->obtenerUsuarioPorId($id_usuario);

        if (!isset($usuario['puntaje_total'])) {
            $usuario['puntaje_total'] = 0;
        }

        $usuario['esEditor'] = ($usuario['tipo_usuario'] === 'editor');

        $this->presenter->show('home', $usuario);
    }
}
