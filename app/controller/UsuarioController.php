<?php
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../helper/TemplateEngine.php'; // Cambia 'helper' si el archivo está en otro directorio

class UsuarioController {
    private $usuarioModel;

    public function __construct($db) {
        $this->usuarioModel = new UsuarioModel($db);
    }

public function mostrarPerfil($id_usuario) {
    // Obtener los datos del usuario
    $usuario = $this->usuarioModel->obtenerUsuarioPorId($id_usuario);

    if ($usuario) {
        // Lógica para manejar el sexo
        $usuario['is_masculino'] = ($usuario['sexo'] == 'Masculino');
        $usuario['is_femenino'] = ($usuario['sexo'] == 'Femenino');
        $usuario['is_otro'] = ($usuario['sexo'] == 'Otro');

        // Asegurarnos de que los valores booleanos están correctamente definidos
        $usuario['is_admin'] = ($usuario['tipo_usuario'] == 'admin') ? true : false;
        $usuario['is_editor'] = ($usuario['tipo_usuario'] == 'editor') ? true : false;

        // Formatear la fecha de nacimiento
        if (isset($usuario['anio_nacimiento']) && !empty($usuario['anio_nacimiento'])) {
            // Asumimos que la fecha en la base de datos está en formato yyyy-MM-dd
            $usuario['anio_nacimiento_formateado'] = date('Y-m-d', strtotime($usuario['anio_nacimiento']));
        } else {
            $usuario['anio_nacimiento_formateado'] = '';
        }

        // Renderizamos la vista del perfil con los datos del usuario
        echo TemplateEngine::render(__DIR__ . '/../view/perfil.mustache', $usuario);
    } else {
        echo "Usuario no encontrado.";
    }
}


}
