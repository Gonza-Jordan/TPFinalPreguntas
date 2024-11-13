<?php
class HistorialController {
    private $partidaModel;
    private $mustache;
    public function __construct($partidaModel, $mustache) {
        $this->partidaModel = $partidaModel;
        $this->mustache = $mustache;
    }
    public function show() {
        error_log("HistorialController::show called");

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $usuarioId = $_SESSION['user_id'] ?? null;
        if (!$usuarioId) {
            error_log("Usuario no autenticado.");
            echo "Usuario no autenticado. Inicie sesión para ver el historial.";
            return;
        }

        $limit = 5;
        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $offset = ($page - 1) * $limit;

        $partidas = $this->partidaModel->obtenerPartidasPaginadas($offset, $limit);
        $totalPartidas = $this->partidaModel->contarTotalPartidas();
        $totalPaginas = ceil($totalPartidas / $limit);

        $this->mustache->show('historial', [
            'partidas' => $partidas,
            'page' => $page,
            'totalPages' => $totalPaginas,
            'previous_page' => max(1, $page - 1),
            'next_page' => min($totalPaginas, $page + 1),
            'is_previous_disabled' => $page <= 1,
            'is_next_disabled' => $page >= $totalPaginas
        ]);
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
        $this->mustache->show('historial', ['partidas' => $partidas]);
    }

}