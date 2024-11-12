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

        $usuarios = $this->rankingModel->obtenerRanking(10);
        $rankingPorPais = $this->rankingModel->obtenerRankingPorPais();
        $rankingPorCiudad = $this->rankingModel->obtenerRankingPorCiudad();

        // Asegúrate de pasar nombre_usuario y foto_perfil a la vista
        $data = [
            'usuarios' => $usuarios,
            'rankingPorPais' => $rankingPorPais,
            'rankingPorCiudad' => $rankingPorCiudad,
            'nombre_usuario' => $_SESSION['nombre_usuario'] ?? 'Invitado', // Valor por defecto si no existe en la sesión
            'foto_perfil' => $_SESSION['foto_perfil'] ?? 'default.png' // Imagen por defecto si no existe en la sesión
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