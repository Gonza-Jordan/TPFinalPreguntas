<?php
require_once __DIR__ . '/../helper/SessionHelper.php';

class HomeController {
    private $presenter;
    private $model;
    private $preguntaModel;

    public function __construct($presenter, $model, $preguntaModel){
        $this->presenter = $presenter;
        $this->model = $model;
        $this->preguntaModel = $preguntaModel;

    }

    public function show() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /TPFinalPreguntas/auth/show');
            exit();
        }

        $id_usuario = $_SESSION['user_id'];
        $usuario = $this->model->obtenerUsuarioPorId($id_usuario);

        if (!isset($usuario['puntaje_total'])) {
            $usuario['puntaje_total'] = 0;
        }

        $esEditor = ($usuario['tipo_usuario'] === 'editor');
        $esJugador = ($usuario['tipo_usuario'] === 'jugador');

        $data = [
            'nombre_usuario' => $usuario['nombre_usuario'],
            'foto_perfil' => $usuario['foto_perfil'],
            'puntaje_total' => $usuario['puntaje_total'],
            'user_id' => $usuario['id_usuario'],
            'esEditor' => $esEditor,
            'esJugador' => $esJugador
        ];

        if ($esEditor) {
            $preguntasSugeridas = $this->preguntaModel->obtenerPreguntasSugeridasPendientes();
            $data['preguntasSugeridas'] = $preguntasSugeridas;
        }

        $this->presenter->show('home', $data);
    }
}
