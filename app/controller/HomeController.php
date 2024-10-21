<?php
require_once __DIR__ . '/../helper/SessionHelper.php';

class HomeController {
    private $presenter;
    private $model;

    public function __construct($presenter, $model){
        $this->presenter = $presenter;
        $this->model = $model;
    }

    public function show() {
        // Verifica si el usuario está autenticado
        if (!isset($_SESSION['user_id'])) {
            header('Location: /TPFinalPreguntas/app/index.php?page=auth&action=show');
            exit();
        }

        // Obtener la información del usuario desde el modelo
        $id_usuario = $_SESSION['user_id'];
        $usuario = $this->model->obtenerUsuarioPorId($id_usuario);

        // Asegúrate de que 'puntaje_total' esté en el array de usuario
        if (!isset($usuario['puntaje_total'])) {
            $usuario['puntaje_total'] = 0;
        }

        $this->presenter->show('home', $usuario);
    }
}
