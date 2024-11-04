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

        $usuarios = $this->usuarioModel->obtenerRankingUsuarios();
        $rankingPorPais = $this->rankingModel->obtenerRankingPorPais();
        $rankingPorCiudad = $this->rankingModel->obtenerRankingPorCiudad();

        if ($usuarios) {
            foreach ($usuarios as $index => &$usuario) {
                $usuario['posicion'] = $index + 1;
            }
        }

        // Preparar los datos para el presenter
        $data = [
            'usuarios' => $usuarios,
            'rankingPorPais' => $rankingPorPais,
            'rankingPorCiudad' => $rankingPorCiudad,
        ];

        // Llamar al presenter con la vista y los datos
        $this->mustache->show('ranking', $data);
    }
}
