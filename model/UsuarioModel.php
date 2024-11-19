<?php
class UsuarioModel {
    private $conn;
    private $table_name = "usuarios";
    
    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrar nuevo usuario
    public function registrar($nombre_completo, $anio_nacimiento, $sexo, $pais, $ciudad, $email, $contrasenia, $nombre_usuario, $foto_perfil, $token_activacion) {
        $query = "INSERT INTO " . $this->table_name . "
            SET
                nombre_completo = :nombre_completo,
                anio_nacimiento = :anio_nacimiento,
                sexo = :sexo,
                pais = :pais,
                ciudad = :ciudad,
                email = :email,
                contraseña = :contrasenia, 
                nombre_usuario = :nombre_usuario,
                foto_perfil = :foto_perfil,
                token_activacion = :token_activacion,
                validado = 0"; // Usuario inicialmente no validado

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $nombre_completo = htmlspecialchars(strip_tags($nombre_completo));
        $anio_nacimiento = htmlspecialchars(strip_tags($anio_nacimiento));
        $sexo = htmlspecialchars(strip_tags($sexo));
        $pais = htmlspecialchars(strip_tags($pais));
        $ciudad = htmlspecialchars(strip_tags($ciudad));
        $email = htmlspecialchars(strip_tags($email));
        $nombre_usuario = htmlspecialchars(strip_tags($nombre_usuario));

        // Encriptar la contraseña
        $contrasenia_hash = password_hash($contrasenia, PASSWORD_BCRYPT);

        // Vincular parámetros
        $stmt->bindParam(":nombre_completo", $nombre_completo);
        $stmt->bindParam(":anio_nacimiento", $anio_nacimiento);
        $stmt->bindParam(":sexo", $sexo);
        $stmt->bindParam(":pais", $pais);
        $stmt->bindParam(":ciudad", $ciudad);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":contrasenia", $contrasenia_hash);
        $stmt->bindParam(":nombre_usuario", $nombre_usuario);
        $stmt->bindParam(":foto_perfil", $foto_perfil);
        $stmt->bindParam(":token_activacion", $token_activacion);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Verificar si el email ya está registrado
    public function emailExiste() {
        $query = "SELECT id_usuario FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Verificar si el nombre de usuario ya está registrado
    public function nombre_usuarioExiste() {
        $query = "SELECT id_usuario FROM " . $this->table_name . " WHERE nombre_usuario = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->nombre_usuario);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Obtener el último ID insertado
    public function getLastInsertedId() {
        return $this->conn->lastInsertId();
    }

    // Activar la cuenta del usuario
    public function activarCuenta($token_activacion) {
        $query = "UPDATE " . $this->table_name . " 
                  SET validado = 1, token_activacion = NULL 
                  WHERE token_activacion = :token_activacion";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token_activacion', $token_activacion);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0; // Devuelve true si se actualizó alguna fila
        }
        return false;
    }

    // Obtener el usuario por ID
    public function obtenerUsuarioPorId($id_usuario) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar perfil del usuario
    public function actualizarPerfil($id_usuario, $anioNacimiento, $sexo, $pais, $ciudad, $email, $password) {
        $query = "UPDATE " . $this->table_name . " 
                  SET anio_nacimiento = :anio_nacimiento, 
                      sexo = :sexo, 
                      pais = :pais, 
                      ciudad = :ciudad, 
                      email = :email, 
                      contrasenia = :password 
                  WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        // Vincular parámetros
        $stmt->bindParam(':anio_nacimiento', $anioNacimiento);
        $stmt->bindParam(':sexo', $sexo);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id_usuario', $id_usuario);

        return $stmt->execute();
    }

    // Actualizar foto de perfil
    public function actualizarFoto($id_usuario, $nombreArchivo) {
        $query = "UPDATE " . $this->table_name . " 
                  SET foto_perfil = :foto_perfil 
                  WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':foto_perfil', $nombreArchivo);
        $stmt->bindParam(':id_usuario', $id_usuario);

        return $stmt->execute();
    }
    public function findUserByUsername($username) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE nombre_usuario = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    private function generarCodigoQR($idUsuario) {
        $urlPerfil = "http://tusitio.com/perfil.php?id=" . $idUsuario;
        $filePath = "path_donde_guardar_qr/qr_$idUsuario.png";
        QRcode::png($urlPerfil, $filePath);
        return $filePath;
    }

    public function esEditor($id_usuario) {
        $query = "SELECT tipo_usuario FROM usuarios WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id_usuario' => $id_usuario]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['tipo_usuario'] === 'editor';
    }

    public function esAdministrador($id_usuario) {
        $query = "SELECT tipo_usuario FROM usuarios WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id_usuario' => $id_usuario]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['tipo_usuario'] === 'administrador';
    }

    public function actualizarEstadisticasUsuario($idUsuario, $esCorrecta) {
        $query = "UPDATE usuarios SET preguntas_respondidas_total = preguntas_respondidas_total + 1 WHERE id_usuario = :idUsuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        if ($esCorrecta) {
            $query = "UPDATE usuarios SET preguntas_respondidas_correctas = preguntas_respondidas_correctas + 1 WHERE id_usuario = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
        }

        $this->actualizarNivelJugador($idUsuario);
    }

    private function actualizarNivelJugador($idUsuario) {
        $query = "SELECT preguntas_respondidas_total, preguntas_respondidas_correctas FROM usuarios WHERE id_usuario = :idUsuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario['preguntas_respondidas_total'] > 0) {
            $porcentajeCorrectas = ($usuario['preguntas_respondidas_correctas'] / $usuario['preguntas_respondidas_total']) * 100;

            $nivel = 'Medio';
            if ($porcentajeCorrectas > 70) {
                $nivel = 'Dificil';
            } elseif ($porcentajeCorrectas < 30) {
                $nivel = 'Facil';
            }

            $query = "UPDATE usuarios SET nivel_jugador = :nivel WHERE id_usuario = :idUsuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nivel', $nivel);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    public function getCantidadUsuariosPorRango($rangoFechas)
    {
        $inicio = $rangoFechas['inicio'];
        $fin = $rangoFechas['fin'];

        $query = $this->conn->prepare("SELECT COUNT(*) AS cantidad FROM usuarios WHERE fecha_creacion BETWEEN :inicio AND :fin");
        $query->bindValue(':inicio', $inicio);
        $query->bindValue(':fin', $fin);
        $query->execute();

        return $query->fetchColumn();
    }

    public function getCantidadUsuarios()
    {
        $query = "SELECT COUNT(*) FROM usuarios";
        return $this->conn->query($query)->fetchColumn();
    }



    public function getUsuariosPorPais()
    {
        $query = "SELECT pais, COUNT(*) as cantidad FROM usuarios GROUP BY pais";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsuariosPorSexo()
    {
        $query = "SELECT sexo, COUNT(*) as cantidad FROM usuarios GROUP BY sexo";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsuariosPorGrupoEdad()
    {
        $query = "SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, anio_nacimiento, CURDATE()) < 18 THEN 'menores'
                    WHEN TIMESTAMPDIFF(YEAR, anio_nacimiento, CURDATE()) >= 65 THEN 'jubilados'
                    ELSE 'medio'
                END as grupo, COUNT(*) as cantidad 
              FROM usuarios 
              GROUP BY grupo";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

}
