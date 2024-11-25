<?php
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../helper/TemplateEngine.php';
require_once __DIR__ . '/../helper/SessionHelper.php';

class PerfilController {
    private $usuarioModel;

    public function __construct($db) {
        $this->usuarioModel = new UsuarioModel($db);

        SessionHelper::verificarSesion();
    }

    public function show($id_usuario) {
        SessionHelper::verificarSesion();

        $usuario = $this->usuarioModel->obtenerUsuarioPorId($id_usuario);

        if ($usuario) {
            $usuario['is_masculino'] = ($usuario['sexo'] == 'Masculino');
            $usuario['is_femenino'] = ($usuario['sexo'] == 'Femenino');
            $usuario['is_otro'] = ($usuario['sexo'] == 'Otro');

            $usuario['is_admin'] = ($usuario['tipo_usuario'] == 'admin');
            $usuario['is_editor'] = ($usuario['tipo_usuario'] == 'editor');

            echo TemplateEngine::render(__DIR__ . '/../view/perfil.mustache', $usuario);
        } else {
            echo "Usuario no encontrado.";
        }
    }
}