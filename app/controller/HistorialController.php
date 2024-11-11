<?php
class HistorialController {
    private $partidaModel;
    private $mustache;

    public function __construct($partidaModel, $mustache) {
        $this->partidaModel = $partidaModel;
        $this->mustache = $mustache;
    }

    // Método show que redirige a listar
    public function show($usuarioId) {
        $this->listar($usuarioId);
    }

    public function listar() {
        // Verifica si la sesión ya ha comenzado
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioId = $_SESSION['user_id'] ?? null;

        if (!$usuarioId) {
            echo "Usuario no autenticado. Inicie sesión para ver el historial.";
            return;
        }

        $partidas = $this->partidaModel->obtenerPartidasPorUsuario($usuarioId);

        echo $this->mustache->render('historial', ['partidas' => $partidas]);
    }


}
