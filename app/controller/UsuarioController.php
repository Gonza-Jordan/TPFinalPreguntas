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

            // Asegúrate de que tipo_usuario esté definido
            $usuario['is_admin'] = (isset($usuario['tipo_usuario']) && $usuario['tipo_usuario'] == 'admin') ? true : false;
            $usuario['is_editor'] = (isset($usuario['tipo_usuario']) && $usuario['tipo_usuario'] == 'editor') ? true : false;

            if (isset($usuario['fecha_nacimiento']) && !empty($usuario['fecha_nacimiento'])) {
                $usuario['anio_nacimiento_formateado'] = date('Y-m-d', strtotime($usuario['fecha_nacimiento']));
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

                // Mueve el archivo a la carpeta de perfiles
                if (move_uploaded_file($foto['tmp_name'], $rutaDestino)) {
                    if ($this->usuarioModel->actualizarFoto($id_usuario, $nombreArchivo)) {
                        echo json_encode(['status' => 'success', 'message' => 'Foto de perfil actualizada exitosamente.']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la base de datos.']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al mover el archivo subido.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al subir la foto.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID de usuario o archivo no proporcionado.']);
        }
    }


    public function actualizarPerfilUsuario() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_POST['id_usuario'];
            $fechaNacimiento = $_POST['fechaNacimiento'];
            $sexo = $_POST['sexo'];
            $pais = $_POST['pais'];
            $ciudad = $_POST['ciudad'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Obtener la contraseña anterior
            $usuario = $this->usuarioModel->obtenerUsuarioPorId($id_usuario);

            if (empty($password)) {
                $password = $usuario['contraseña'];
            } else {
                $password = password_hash($password, PASSWORD_BCRYPT);
            }

            $resultado = $this->usuarioModel->actualizarPerfil($id_usuario, $fechaNacimiento, $sexo, $pais, $ciudad, $email, $password);

            if ($resultado) {
                echo "Perfil actualizado exitosamente.";
            } else {
                echo "Error al actualizar el perfil.";
            }
        }
    }

}
