<?php
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../model/RankingModel.php';
require_once __DIR__ . '/../helper/TemplateEngine.php';
require_once __DIR__ . '/../helper/SessionHelper.php';
require_once __DIR__ . '/../helper/QRCodeHelper.php';

class RankingController {
    private $mustache;
    private $rankingModel;
    private $usuarioModel;
    private $partidaModel;

    public function __construct($mustache, $usuarioModel, $partidaModel,$rankingModel) {
        $this->mustache = $mustache;
        $this->usuarioModel = $usuarioModel;
        $this->partidaModel = $partidaModel;
        $this->rankingModel = $rankingModel;

        SessionHelper::verificarSesion();
    }

    public function show() {
        SessionHelper::verificarSesion();

        $this->rankingModel->actualizarRanking();

        $limite = 5;
        $paginaActual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;

        $usuarios = $this->rankingModel->obtenerRanking($limite, $paginaActual);
        $totalUsuarios = $this->rankingModel->contarUsuariosConPuntaje();
        $totalPaginas = ceil($totalUsuarios / $limite);

        $rankingPorPais = $this->rankingModel->obtenerRankingPorPais();
        $rankingPorCiudad = $this->rankingModel->obtenerRankingPorCiudad();

        $data = [
            'usuarios' => $usuarios,
            'rankingPorPais' => $rankingPorPais,
            'rankingPorCiudad' => $rankingPorCiudad,
            'pagina_actual' => $paginaActual,
            'total_paginas' => $totalPaginas,
            'prev_page' => ($paginaActual > 1) ? $paginaActual - 1 : null,
            'next_page' => ($paginaActual < $totalPaginas) ? $paginaActual + 1 : null,
            'pages' => array_map(function ($numero) use ($paginaActual) {
                return [
                    'numero' => $numero,
                    'active' => ($numero == $paginaActual) ? 'active' : '',
                ];
            }, range(1, $totalPaginas)),
            'nombre_usuario' => $_SESSION['nombre_usuario'] ?? 'Invitado',
            'foto_perfil' => $_SESSION['foto_perfil'] ?? 'default.png',
        ];


        $this->mustache->show('ranking', $data);
    }

    public function verPerfilJugador() {
        SessionHelper::verificarSesion();

        $idUsuario = $_GET['id'];
        $usuario = $this->usuarioModel->obtenerUsuarioPorId($idUsuario);
        $partidas = $this->partidaModel->obtenerPartidasPorUsuario($idUsuario);

        if ($usuario) {

            $qrCodeUrl = "http://localhost:8080/perfil/id/$idUsuario";
            $qrCode = QRCodeHelper::generateQRCode($qrCodeUrl);

            $this->mustache->show('verPerfilJugador', [
                'usuario' => $usuario,
                'partidas' => $partidas,
                'qrCode' => $qrCode,
            ]);
        } else {
            echo "Usuario no encontrado";
        }
    }
}