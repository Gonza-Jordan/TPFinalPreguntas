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

    // Ver clase grabada y consultar esta parte
    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    nombre_completo = :nombre_completo,
                    anio_nacimiento = :anio_nacimiento,
                    sexo = :sexo,
                    pais = :pais,
                    ciudad = :ciudad,
                    email = :email,
                    contrasenia = :contrasenia,
                    nombre_usuario = :nombre_usuario,
                    foto_perfil = :foto_perfil";

        $stmt = $this->conn->prepare($query);

        $this->nombre_completo = htmlspecialchars(strip_tags($this->nombre_completo));
        $this->anio_nacimiento = htmlspecialchars(strip_tags($this->anio_nacimiento));
        $this->sexo = htmlspecialchars(strip_tags($this->sexo));
        $this->pais = htmlspecialchars(strip_tags($this->pais));
        $this->ciudad = htmlspecialchars(strip_tags($this->ciudad));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->nombre_usuario = htmlspecialchars(strip_tags($this->nombre_usuario));

        $contrasenia_hash = contrasenia_hash($this->contrasenia, contrasenia_BCRYPT);

        $stmt->bindParam(":nombre_completo", $this->nombre_completo);
        $stmt->bindParam(":anio_nacimiento", $this->anio_nacimiento);
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
        $query = "SELECT * FROM usuarios WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
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


}
