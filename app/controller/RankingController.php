<?php
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../model/RankingModel.php';
require_once __DIR__ . '/../helper/TemplateEngine.php';
require_once __DIR__ . '/../helper/SessionHelper.php';

class RankingController {
    private $mustache;
    private $RankingModel;
    
    public function __construct($mustache, $RankingModel) {
        $this->mustache = $mustache;
        $this->RankingModel = $RankingModel;
    }

    public function show() {
        $this->mustache->show('ranking');
    }
    
    public function mostrarRanking() {
        SessionHelper::verificarSesion();

        $usuarios = $this->RankingModel->obtenerRanking(10);
        $rankingPorPais = $this->RankingModel->obtenerRankingPorPais();
        $rankingPorCiudad = $this->RankingModel->obtenerRankingPorCiudad();

        if ($usuarios) {
            foreach ($usuarios as $index => &$usuario) {
                $usuario['posicion'] = $index + 1;
            }
        }
        if ($rankingPorPais) {
            foreach ($rankingPorPais as $index => &$usuario) {
                $usuario['posicion'] = $index + 1;
            }
        }

        if ($rankingPorCiudad) {
            foreach ($rankingPorCiudad as $index => &$usuario) {
                $usuario['posicion'] = $index + 1;
            }
        }


        $data = [
            'usuarios' => $usuarios,
            'rankingPorPais' => $rankingPorPais,
            'rankingPorCiudad' => $rankingPorCiudad,
        ];

        $this->mustache->show('ranking', $data);
    }
}
