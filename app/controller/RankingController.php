<?php
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../helper/TemplateEngine.php';
require_once __DIR__ . '/../helper/SessionHelper.php';

class RankingController {
    private $usuarioModel;

    public function __construct($db) {
        $this->usuarioModel = new UsuarioModel($db);
    }

    public function mostrarRanking() {
        SessionHelper::verificarSesion();

        $usuarios = $this->usuarioModel->obtenerUsuariosPorRanking();

        if (!$usuarios) {
            echo TemplateEngine::render(__DIR__ . '/../view/ranking.mustache', ['usuarios' => [], 'mensaje' => 'No hay usuarios en el ranking.']);
            return;
        }

        foreach ($usuarios as $index => &$usuario) {
            $usuario['posicion'] = $index + 1;
        }

        echo TemplateEngine::render(__DIR__ . '/../view/ranking.mustache', ['usuarios' => $usuarios]);
    }
}

