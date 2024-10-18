<?php
class RegistroController {
    private $usuarioModel;

    // Ver clase grabada y consultar esta parte
    public function __construct($db) {
        $this->usuarioModel = new UsuarioModel($db);
    }

    public function registrar($datos, $archivos) {
        $errores = $this->validarDatos($datos);
        
        if(empty($errores)) {
            $foto_perfil = $this->procesarFotoPerfil($archivos);
            if($foto_perfil === false) {
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

            if($this->usuarioModel->registrar()) {
                return ["success" => "Usuario registrado exitosamente"];
            } else {
                return ["error" => "Error al registrar usuario"];
            }
        }

        return ["errores" => $errores];
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
        if($this->usuarioModel->emailExiste()) {
            $errores[] = "Este email ya está registrado";
        }

        if(empty($datos['nombre_usuario']) || strlen($datos['nombre_usuario']) < 3) {
            $errores[] = "El nombre de usuario debe tener al menos 3 caracteres";
        }
        if($this->usuarioModel->nombre_usuarioExiste()) {
            $errores[] = "Este nombre de usuario ya está en uso";
        }

        if(strlen($datos['contrasenia']) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres";
        }
        if($datos['contrasenia'] !== $datos['contrasenia_confirm']) {
            $errores[] = "Las contraseñas no coinciden";
        }

        return $errores;
    }

    private function procesarFotoPerfil($archivos) {
        if(isset($archivos['foto_perfil'])) {
            $file = $archivos['foto_perfil'];
            $permitidos = ['jpg', 'jpeg', 'png'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if(in_array($extension, $permitidos)) {
                $nombre_archivo = uniqid() . "." . $extension;
                $ruta_destino = "uploads/" . $nombre_archivo;

                if(move_uploaded_file($file['tmp_name'], $ruta_destino)) {
                    return $nombre_archivo;
                }
            }
        }
        return false;
    }
}
