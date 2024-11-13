<?php
class HistorialController {
    private $partidaModel;
    private $mustache;

    public function __construct($partidaModel, $mustache) {
        $this->partidaModel = $partidaModel;
        $this->mustache = $mustache;
    }

    public function show($usuarioId) {
        $this->listar($usuarioId);
    }

    public function listar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioId = $_SESSION['user_id'] ?? null;
        if (!$usuarioId) {
            echo "Usuario no autenticado. Inicie sesión para ver el historial.";
            return;
        }

        $partidas = $this->partidaModel->obtenerHistorialPorUsuario($usuarioId);
        $this->mustache->show('historial', ['partidas' => $partidas]); // Usar show en lugar de render
    }
}