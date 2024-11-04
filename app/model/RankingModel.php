<?php
class RankingModel {
    private $conn;
    private $table_usuarios = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerRanking($limite = 10) {
        $query = "SELECT id_usuario, nombre_usuario, puntaje_total
                  FROM " . $this->table_usuarios . "
                  ORDER BY puntaje_total DESC
                  LIMIT :limite";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);


        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPosicionUsuario($id_usuario) {
        $query = "SELECT COUNT(*) + 1 AS posicion
                  FROM " . $this->table_usuarios . "
                  WHERE puntaje_total > (
                      SELECT puntaje_total
                      FROM " . $this->table_usuarios . "
                      WHERE id_usuario = :id_usuario
                  )";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPuntajeUsuario($id_usuario) {
        $query = "SELECT puntaje_total
                  FROM " . $this->table_usuarios . "
                  WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
