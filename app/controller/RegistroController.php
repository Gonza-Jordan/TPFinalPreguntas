<?php
require_once '../model/UsuarioModel.php';
require_once '../helper/EmailHelper.php';
require_once '../config/Database.php';

class RegistroController {
    private $usuarioModel;

    public function __construct($db) {
        $this->usuarioModel = new UsuarioModel($db); //agregarle
    }

    public function registrar($datos, $archivos) {
        $errores = $this->validarDatos($datos);

        if (empty($errores)) {
            $foto_perfil = $this->procesarFotoPerfil($archivos);
            if ($foto_perfil === false) {
                return ["error" => "Error al procesar la imagen"];
            }

            $this->usuarioModel->nombre_completo = $datos['nombre_completo'];
            $this->usuarioModel->anio_nacimiento = $datos['anio_nacimiento'];
            $this->usuarioModel->sexo = $datos['sexo'];
            $this->usuarioModel->pais = $datos['pais'];
            $this->usuarioModel->ciudad = $datos['ciudad'];
            $this->usuarioModel->email = $datos['email'];
            $this->usuarioModel->contrasenia = $datos['contrasenia'];
            $this->usuarioModel->nombre_usuario = $datos['nombre_usuario'];
            $this->usuarioModel->foto_perfil = $foto_perfil;

            if ($this->usuarioModel->registrar()) {
                $token = bin2hex(random_bytes(16)); // Generar un token de activación
                $this->usuarioModel->guardarTokenActivacion($token);

                // Enviar correo de activación
                if (EmailHelper::enviarCorreoActivacion($datos['email'], $token)) {
                    return ["success" => "Usuario registrado exitosamente. Revisa tu correo para activar tu cuenta."];
                } else {
                    return ["error" => "Error al enviar el correo de activación."];
                }
            } else {
                return ["error" => "Error al registrar usuario"];
            }
        }

        return ["errores" => $errores];
    }

    private function validarDatos($datos) {
        $errores = [];

        if (empty($datos['nombre_completo']) || strlen($datos['nombre_completo']) < 3) {
            $errores[] = "El nombre completo es requerido y debe tener al menos 3 caracteres";
        }

        if (!is_numeric($datos['anio_nacimiento']) ||
            $datos['anio_nacimiento'] < 1900 ||
            $datos['anio_nacimiento'] > date('Y')) {
            $errores[] = "Año de nacimiento inválido";
        }

        $sexos_validos = ['Masculino', 'Femenino', 'Prefiero no cargarlo'];
        if (!in_array($datos['sexo'], $sexos_validos)) {
            $errores[] = "Sexo inválido";
        }

        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "Email inválido";
        }
        if ($this->usuarioModel->emailExiste($datos['email'])) {
            $errores[] = "Este email ya está registrado";
        }

        if (empty($datos['nombre_usuario']) || strlen($datos['nombre_usuario']) < 3) {
            $errores[] = "El nombre de usuario debe tener al menos 3 caracteres";
        }
        if ($this->usuarioModel->nombre_usuarioExiste($datos['nombre_usuario'])) {
            $errores[] = "Este nombre de usuario ya está en uso";
        }

        if (strlen($datos['contrasenia']) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres";
        }
        if ($datos['contrasenia'] !== $datos['contrasenia_confirm']) {
            $errores[] = "Las contraseñas no coinciden";
        }

        return $errores;
    }

    private function procesarFotoPerfil($archivos) {
        if (isset($archivos['foto_perfil']) && $archivos['foto_perfil']['error'] == 0) {
            $file = $archivos['foto_perfil'];
            $permitidos = ['jpg', 'jpeg', 'png'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($extension, $permitidos)) {
                $nombre_archivo = uniqid() . "." . $extension;
                $ruta_destino = "../uploads/" . $nombre_archivo;

                if (move_uploaded_file($file['tmp_name'], $ruta_destino)) {
                    return $nombre_archivo;
                }
            }
        }
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = Database::getConnection();
    $registroController = new RegistroController($db);

    $datos = [
        'nombre_completo' => $_POST['nombre_completo'],
        'anio_nacimiento' => $_POST['anio_nacimiento'],
        'sexo' => $_POST['sexo'],
        'pais' => $_POST['pais'],
        'ciudad' => $_POST['ciudad'],
        'email' => $_POST['email'],
        'contrasenia' => $_POST['contrasenia'],
        'contrasenia_confirm' => $_POST['contrasenia_confirm'],
        'nombre_usuario' => $_POST['nombre_usuario'],
    ];

    $archivos = [
        'foto_perfil' => $_FILES['foto_perfil']
    ];

    $resultado = $registroController->registrar($datos, $archivos);

    if (isset($resultado['success'])) {
        echo "<div class='alert alert-success'>{$resultado['success']}</div>";
    } elseif (isset($resultado['error'])) {
        echo "<div class='alert alert-danger'>{$resultado['error']}</div>";
    } elseif (isset($resultado['errores'])) {
        foreach ($resultado['errores'] as $error) {
            echo "<div class='alert alert-danger'>{$error}</div>";
        }
    }
}
?>
