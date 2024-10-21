<?php
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../helper/TemplateEngine.php';
require_once __DIR__ . '/../helper/SessionHelper.php';

class UsuarioController {
    private $usuarioModel;

    public function __construct($db) {
        $this->usuarioModel = new UsuarioModel($db);
    }

    public function mostrarPerfil($id_usuario) {
        SessionHelper::verificarSesion();

        if ($_SESSION['user_id'] != $id_usuario) {
            echo "Acceso denegado. No tienes permiso para ver este perfil.";
            exit();
        }

        $usuario = $this->usuarioModel->obtenerUsuarioPorId($id_usuario);

        if ($usuario) {
            // Cargar variables extra necesarias para la vista
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
    public function actualizarFotoPerfil() {
        if (isset($_POST['id_usuario']) && isset($_FILES['nueva_foto'])) {
            $id_usuario = $_POST['id_usuario'];
            $foto = $_FILES['nueva_foto'];

            if ($foto['error'] === UPLOAD_ERR_OK) {
                $nombreArchivo = 'foto_' . $id_usuario . '.' . pathinfo($foto['name'], PATHINFO_EXTENSION);
                $rutaDestino = __DIR__ . '/../public/perfiles/' . $nombreArchivo;

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
            $anio_nacimiento = $_POST['anio_nacimiento'];
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

            $resultado = $this->usuarioModel->actualizarPerfil($id_usuario, $anio_nacimiento, $sexo, $pais, $ciudad, $email, $password);

            if ($resultado) {
                echo json_encode(['status' => 'success', 'message' => 'Perfil actualizado exitosamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el perfil.']);
            }
        }
    }

}
