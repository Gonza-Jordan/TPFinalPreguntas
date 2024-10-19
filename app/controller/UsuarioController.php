<?php
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../helper/TemplateEngine.php';

class UsuarioController {
    private $usuarioModel;

    public function __construct($db) {
        $this->usuarioModel = new UsuarioModel($db);
    }

    public function mostrarPerfil($id_usuario) {
        $usuario = $this->usuarioModel->obtenerUsuarioPorId($id_usuario);

        if ($usuario) {
            $usuario['is_masculino'] = ($usuario['sexo'] == 'Masculino');
            $usuario['is_femenino'] = ($usuario['sexo'] == 'Femenino');
            $usuario['is_otro'] = ($usuario['sexo'] == 'Otro');

            $usuario['is_admin'] = ($usuario['tipo_usuario'] == 'admin') ? true : false;
            $usuario['is_editor'] = ($usuario['tipo_usuario'] == 'editor') ? true : false;

            if (isset($usuario['anio_nacimiento']) && !empty($usuario['anio_nacimiento'])) {
                $usuario['anio_nacimiento_formateado'] = date('Y-m-d', strtotime($usuario['anio_nacimiento']));
            } else {
                $usuario['anio_nacimiento_formateado'] = '';
            }

            echo TemplateEngine::render(__DIR__ . '/../view/perfil.mustache', $usuario);
        } else {
            echo "Usuario no encontrado.";
        }
    }

public function actualizarFotoPerfil() {
    if (isset($_POST['id_usuario']) && isset($_FILES['nueva_foto'])) {
        $id_usuario = $_POST['id_usuario'];
        $foto = $_FILES['nueva_foto'];

        if ($foto['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = 'foto_' . $id_usuario . '.' . pathinfo($foto['name'], PATHINFO_EXTENSION);
            $rutaDestino = __DIR__ . '/../public/perfiles/' . $nombreArchivo;

            if (move_uploaded_file($foto['tmp_name'], $rutaDestino)) {
                if ($this->usuarioModel->actualizarFoto($id_usuario, $nombreArchivo)) {
                    echo "Foto de perfil actualizada exitosamente.";
                } else {
                    echo "Error al actualizar la base de datos.";
                }
            } else {
                echo "Error al mover el archivo subido.";
            }
        } else {
            echo "Error al subir la foto.";
        }
    } else {
        echo "ID de usuario o archivo no proporcionado.";
    }
}

}
