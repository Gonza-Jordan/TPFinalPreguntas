<?php
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../model/RankingModel.php';
require_once __DIR__ . '/../helper/TemplateEngine.php';
require_once __DIR__ . '/../helper/SessionHelper.php';

class RankingController {
    private $presenter;
    private $model;
    
    public function __construct($presenter, $model) {
        $this->presenter = $presenter;
        $this->model = $model;
    }

    public function show() {
        $this->presenter->show('ranking');
    }
    
    public function mostrarRanking() {
        SessionHelper::verificarSesion();

        $usuarios = $this->model->obtenerRanking(10);
        $rankingPorPais = $this->model->obtenerRankingPorPais();
        $rankingPorCiudad = $this->model->obtenerRankingPorCiudad();

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

        $this->presenter->show('ranking', $data);
    }
}
