<?php
class UsuarioModel {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nombre_completo;
    public $fecha_nacimiento;
    public $sexo;
    public $pais;
    public $ciudad;
    public $email;
    public $contrasenia;
    public $nombre_usuario;
    public $foto_perfil;

    // Ver clase grabada y consultar esta parte
    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    nombre_completo = :nombre_completo,
                    fecha_nacimiento = :fecha_nacimiento,
                    sexo = :sexo,
                    pais = :pais,
                    ciudad = :ciudad,
                    email = :email,
                    contrasenia = :contrasenia,
                    nombre_usuario = :nombre_usuario,
                    foto_perfil = :foto_perfil";

        $stmt = $this->conn->prepare($query);

        $this->nombre_completo = htmlspecialchars(strip_tags($this->nombre_completo));
        $this->fecha_nacimiento = htmlspecialchars(strip_tags($this->fecha_nacimiento));
        $this->sexo = htmlspecialchars(strip_tags($this->sexo));
        $this->pais = htmlspecialchars(strip_tags($this->pais));
        $this->ciudad = htmlspecialchars(strip_tags($this->ciudad));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->nombre_usuario = htmlspecialchars(strip_tags($this->nombre_usuario));

        $contrasenia_hash = contrasenia_hash($this->contrasenia, contrasenia_BCRYPT);

        $stmt->bindParam(":nombre_completo", $this->nombre_completo);
        $stmt->bindParam(":fecha_nacimiento", $this->fecha_nacimiento);
        $stmt->bindParam(":sexo", $this->sexo);
        $stmt->bindParam(":pais", $this->pais);
        $stmt->bindParam(":ciudad", $this->ciudad);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":contrasenia", $contrasenia_hash);
        $stmt->bindParam(":nombre_usuario", $this->nombre_usuario);
        $stmt->bindParam(":foto_perfil", $this->foto_perfil);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function emailExiste() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        $num = $stmt->rowCount();
        return $num > 0;
    }

    public function nombre_usuarioExiste() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE nombre_usuario = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->nombre_usuario);
        $stmt->execute();
        $num = $stmt->rowCount();
        return $num > 0;
    }

    public function obtenerUsuarioPorId($id_usuario) {
        $query = "SELECT id_usuario, nombre_usuario, contraseña, nombre_completo, email, fecha_nacimiento, sexo, pais, ciudad, foto_perfil, tipo_usuario FROM " . $this->table_name . " WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarPerfil($id_usuario, $fechaNacimiento, $sexo, $pais, $ciudad, $email, $password) {
        $query = "UPDATE " . $this->table_name . " SET fecha_nacimiento = :fechaNacimiento, sexo = :sexo, pais = :pais, ciudad = :ciudad, email = :email, contraseña = :password WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        // Vincular los parámetros
        $stmt->bindParam(':fechaNacimiento', $fechaNacimiento);
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

}
