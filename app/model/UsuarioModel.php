<?php
class UsuarioModel {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nombre_completo;
    public $anio_nacimiento;
    public $sexo;
    public $pais;
    public $ciudad;
    public $email;
    public $contrasenia;
    public $nombre_usuario;
    public $foto_perfil;
    public $token_activacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrar nuevo usuario
    public function registrar() {
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
        $this->nombre_completo = htmlspecialchars(strip_tags($this->nombre_completo));
        $this->anio_nacimiento = htmlspecialchars(strip_tags($this->anio_nacimiento));
        $this->sexo = htmlspecialchars(strip_tags($this->sexo));
        $this->pais = htmlspecialchars(strip_tags($this->pais));
        $this->ciudad = htmlspecialchars(strip_tags($this->ciudad));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->nombre_usuario = htmlspecialchars(strip_tags($this->nombre_usuario));

        // Encriptar la contraseña
        $contrasenia_hash = password_hash($this->contrasenia, PASSWORD_BCRYPT);  // Cambiar aquí

        // Vincular parámetros
        $stmt->bindParam(":nombre_completo", $this->nombre_completo);
        $stmt->bindParam(":anio_nacimiento", $this->anio_nacimiento);
        $stmt->bindParam(":sexo", $this->sexo);
        $stmt->bindParam(":pais", $this->pais);
        $stmt->bindParam(":ciudad", $this->ciudad);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":contrasenia", $contrasenia_hash);  // Cambiar aquí a 'contrasenia'
        $stmt->bindParam(":nombre_usuario", $this->nombre_usuario);
        $stmt->bindParam(":foto_perfil", $this->foto_perfil);
        $stmt->bindParam(":token_activacion", $this->token_activacion);

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
    public function obtenerRankingUsuarios() {
        $query = "SELECT id_usuario, nombre_usuario, puntaje_total 
              FROM usuarios 
              ORDER BY puntaje_total DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
