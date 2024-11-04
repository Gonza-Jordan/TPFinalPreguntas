<?php
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../model/RankingModel.php';
require_once __DIR__ . '/../helper/TemplateEngine.php';
require_once __DIR__ . '/../helper/SessionHelper.php';

class RankingController {
    private $mustache;
    private $usuarioModel;
    private $rankingModel;
    
    public function __construct($mustache, $usuarioModel, $rankingModel) {
        $this->mustache = $mustache;
        $this->usuarioModel = $usuarioModel;
        $this->rankingModel = $rankingModel;
    }

    public function show() {
        $this->mustache->show('logIn');
    }
    

    public function mostrarRanking() {
        SessionHelper::verificarSesion();

        $usuarios = $this->usuarioModel->obtenerRankingUsuarios();
        $rankingPorPais = $this->rankingModel->obtenerRankingPorPais();
        $rankingPorCiudad = $this->rankingModel->obtenerRankingPorCiudad();

        if (!$usuarios) {
            echo TemplateEngine::render(__DIR__ . '/../view/ranking.mustache', ['usuarios' => [], 'mensaje' => 'No hay usuarios en el ranking.']);
            return;
        }

        foreach ($usuarios as $index => &$usuario) {
            $usuario['posicion'] = $index + 1;
        }

        echo TemplateEngine::render(__DIR__ . '/../view/ranking.mustache', [
            'usuarios' => $usuarios,
            'rankingPorPais' => $rankingPorPais,
            'rankingPorCiudad' => $rankingPorCiudad
        ]);
    }
}
