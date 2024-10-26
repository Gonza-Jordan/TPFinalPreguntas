<?php
class UsuarioModel {
    private $conn;
    private $table_name = "usuarios";

    private $id;
    private $nombre_completo;
    private $anio_nacimiento;
    private $sexo;
    private $pais;
    private $ciudad;
    private $latitud;
    private $longitud;
    private $email;
    private $contrasenia;
    private $nombre_usuario;
    private $foto_perfil;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Verificar que el nombre de usuario existe
    public function nombreUsuarioExiste($nombre_usuario)
    {
        $sql = "SELECT 1 
                FROM $this.table_name 
                WHERE nombre_usuario = '" . $nombre_usuario. "'";

        $usuario_encontrado = $this->conn->query($sql);

        return sizeof($usuario_encontrado) > 0;
    }

    // Verificar que el email existe
    public function emailExiste($email)
    {
        $sql = "SELECT 1 
                FROM $this.table_name
                WHERE email = '" . $email. "'";

        $email_encontrado = $this->conn->query($sql);

        return sizeof($email_encontrado) > 0;
    }

    public function registrar($nombre_completo, $anio_nacimiento, $nombre_usuario, $email, $contrasenia, $sexo, $ciudad, $pais, $latitud, $longitud, $foto_perfil) {
        try {
            // Primero verificar si el nombre de usuario ya existe
            if ($this->nombreUsuarioExiste($nombre_usuario)) {
                return ['success' => false, 'message' => 'El nombre de usuario ya está en uso'];
            }
            
            $hashed_password = password_hash($contrasenia, PASSWORD_DEFAULT);
                        
            $sql = "INSERT INTO " . $this->table_name . " (nombre_completo, anio_nacimiento, sexo, pais, ciudad, latitud, longitud, email, contraseña, nombre_usuario, foto_perfil)
                    VALUES (:nombre_completo, :anio_nacimiento, :sexo, :pais, :ciudad, :latitud, :longitud, :email, :contrasenia, :nombre_usuario, :foto_perfil)";
            
            return ['success' => true, 'message' => 'Usuario registrado exitosamente'];

        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Error al registrar usuario: ' . $e->getMessage()];
    }
}

    public function obtenerUsuarioPorId($id_usuario) {
        $query = "SELECT id_usuario, nombre_usuario, contraseña, nombre_completo, email, anio_nacimiento, sexo, pais, ciudad, foto_perfil, tipo_usuario FROM " . $this->table_name . " WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarPerfil($id_usuario, $anioNacimiento, $sexo, $pais, $ciudad, $email, $password) {
        $query = "UPDATE " . $this->table_name . " SET anio_nacimiento = :anio_nacimiento, sexo = :sexo, pais = :pais, ciudad = :ciudad, email = :email, contraseña = :password WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        // Vincular los parámetros
        $stmt->bindParam(':anio_nacimiento', $anioNacimiento);
        $stmt->bindParam(':sexo', $sexo);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id_usuario', $id_usuario);

        return $stmt->execute();
    }

    public function actualizarFoto($id_usuario, $nombreArchivo) {
        $query = "UPDATE usuarios SET foto_perfil = :foto_perfil WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':foto_perfil', $nombreArchivo);
        $stmt->bindParam(':id_usuario', $id_usuario);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function findUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE nombre_usuario = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getLastInsertedId() {
        return $this->conn->lastInsertId();
    }

}
