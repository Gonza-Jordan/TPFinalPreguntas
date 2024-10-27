<?php
class RegistroController {
    private $usuarioModel;

    private $mustache;

    public function __construct($mustache, $db) {
        $this->usuarioModel = new UsuarioModel($db);
        $this->mustache = $mustache;
    }

    public function show() {
        $this->mustache->show('SignUp');
    }

    public function registrar() {
        $datos = $_POST;
        $archivos = $_FILES;

        $errores = $this->validarDatos($datos);
        
        if (empty($errores)) {
            // Primero registramos el usuario sin la foto
            $nombre_completo = $datos['nombre_completo'];
            $anio_nacimiento = $datos['anio_nacimiento'];
            $sexo = $datos['sexo'];
            $pais = $datos['pais'];
            $ciudad = $datos['ciudad'];
            $latitud = $datos['latitud'];
            $longitud = $datos['longitud'];
            $email = $datos['email'];
            $contrasenia = $datos['contrasenia'];
            $nombre_usuario = $datos['nombre_usuario'];

            $registro = $this->usuarioModel->registrar($nombre_completo, $anio_nacimiento, $nombre_usuario, $email, $contrasenia, $sexo, $ciudad, $pais, $latitud, $longitud, $foto_perfil);

            // Primero registrar el usuario
            if($registro) {
                // Obtener el ID del usuario registrado
                $id_usuario = $this->usuarioModel->getLastInsertedId();

                // Procesar la foto de perfil usando el ID del usuario
                $foto_perfil = $this->procesarFotoPerfil($archivos, $id_usuario);
                if($foto_perfil === false) {
                    return ["error" => "Error al procesar la imagen"];
                }

                // Actualizamos el usuario con la ruta de la foto de perfil
                $this->usuarioModel->actualizarFoto($id_usuario, $foto_perfil);

                // Redirigimos con el mensaje de éxito
                header('Location: /TPFinalPreguntas/app/index.php?page=registro&status=success');
                exit();
            } else {
                header('Location: /TPFinalPreguntas/app/index.php?page=registro&status=error_registro');
                exit();
            }
        } else {
            $errores_texto = urlencode(implode(', ', $errores));
            header("Location: /TPFinalPreguntas/app/index.php?page=registro&status=error_validacion&errors=$errores_texto");
            exit();
        }
    }


    private function validarDatos($datos) {
        $errores = [];

        if(empty($datos['nombre_completo']) || strlen($datos['nombre_completo']) < 3) {
            $errores[] = "El nombre completo es requerido y debe tener al menos 3 caracteres";
        }

        if(!is_numeric($datos['anio_nacimiento']) ||
           $datos['anio_nacimiento'] < 1900 ||
           $datos['anio_nacimiento'] > date('Y')) {
            $errores[] = "Año de nacimiento inválido";
        }

        $sexos_validos = ['Masculino', 'Femenino', 'Prefiero no cargarlo'];
        if(!in_array($datos['sexo'], $sexos_validos)) {
            $errores[] = "Sexo inválido";
        }

        if(!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "Email inválido";
        }
        if($this->usuarioModel->emailExiste($datos['email'])) {
            $errores[] = "Este email ya está registrado";
        }

        if(empty($datos['nombre_usuario']) || strlen($datos['nombre_usuario']) < 3) {
            $errores[] = "El nombre de usuario debe tener al menos 3 caracteres";
        }
        if($this->usuarioModel->nombreUsuarioExiste($datos['nombre_usuario'])) {
            $errores[] = "Este nombre de usuario ya está en uso";
        }

        if(strlen($datos['contrasenia']) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres";
        }
        if($datos['contrasenia'] !== $datos['comparar_contrasenia']) {
            $errores[] = "Las contraseñas no coinciden";
       }

        return $errores;
    }

    private function procesarFotoPerfil($archivos, $id_usuario) {
        if (isset($archivos['foto_perfil'])) {
            $file = $archivos['foto_perfil'];
            $permitidos = ['jpg', 'jpeg', 'png'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($extension, $permitidos)) {
                // Usar el ID del usuario para el nombre de la imagen
                $nombre_archivo = 'foto_' . $id_usuario . '.' . $extension;
                $ruta_destino = __DIR__ . '/../public/perfiles/' . $nombre_archivo;

                if (move_uploaded_file($file['tmp_name'], $ruta_destino)) {
                    return $nombre_archivo;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }
}