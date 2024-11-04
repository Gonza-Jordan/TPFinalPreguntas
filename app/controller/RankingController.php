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
        $this->mustache->show('ranking');
    }
    
    public function mostrarRanking() {
        SessionHelper::verificarSesion();

        $usuarios = $this->rankingModel->obtenerRanking(10);
        $rankingPorPais = $this->rankingModel->obtenerRankingPorPais();
        $rankingPorCiudad = $this->rankingModel->obtenerRankingPorCiudad();


        $data = [
            'usuarios' => $usuarios,
            'rankingPorPais' => $rankingPorPais,
            'rankingPorCiudad' => $rankingPorCiudad,
        ];

        $this->mustache->show('ranking', $data);
    }
}
