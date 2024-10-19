<?php
class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function findUserByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM Usuarios WHERE nombre_usuario = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}