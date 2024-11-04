<?php
class RankingModel {
    private $conn;
    private $table_usuarios = "usuarios";
    private $table_partidas = "partidas";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerRanking($limite = 10) {
        $query = "SELECT u.id_usuario, u.nombre_usuario, u.puntaje_total, r.posicion
              FROM " . $this->table_usuarios . " u
              JOIN ranking r ON u.id_usuario = r.id_usuario
              ORDER BY r.posicion ASC
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

    public function obtenerRankingPorPais() {
        $query = "
            SELECT 
                u.pais,
                u.nombre_usuario,
                u.nombre_completo,
                u.foto_perfil,
                (SELECT MAX(p.puntos_sumados) 
                 FROM $this->table_partidas p 
                 WHERE p.id_usuario = u.id_usuario) AS mejor_puntaje,
                (SELECT COUNT(DISTINCT p.id_partida) 
                 FROM $this->table_partidas p 
                 WHERE p.id_usuario = u.id_usuario) AS partidas_jugadas,
                RANK() OVER (PARTITION BY u.pais ORDER BY (SELECT MAX(p.puntos_sumados) 
                                                            FROM $this->table_partidas p 
                                                            WHERE p.id_usuario = u.id_usuario) DESC) AS posicion_pais,
                RANK() OVER (ORDER BY (SELECT MAX(p.puntos_sumados) 
                                       FROM $this->table_partidas p 
                                       WHERE p.id_usuario = u.id_usuario) DESC) AS posicion_global
            FROM $this->table_usuarios u
            WHERE u.validado = true
            ORDER BY u.pais, mejor_puntaje DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener Ranking por Pais: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerRankingPorCiudad() {
        $query = "
            SELECT 
                u.pais,
                u.ciudad,
                u.nombre_usuario,
                u.nombre_completo,
                u.foto_perfil,
                (SELECT MAX(p.puntos_sumados) 
                 FROM $this->table_partidas p 
                 WHERE p.id_usuario = u.id_usuario) AS mejor_puntaje,
                (SELECT COUNT(DISTINCT p.id_partida) 
                 FROM $this->table_partidas p 
                 WHERE p.id_usuario = u.id_usuario) AS partidas_jugadas,
                RANK() OVER (PARTITION BY u.ciudad ORDER BY (SELECT MAX(p.puntos_sumados) 
                                                            FROM $this->table_partidas p 
                                                            WHERE p.id_usuario = u.id_usuario) DESC) AS posicion_ciudad,
                RANK() OVER (ORDER BY (SELECT MAX(p.puntos_sumados) 
                                       FROM $this->table_partidas p 
                                       WHERE p.id_usuario = u.id_usuario) DESC) AS posicion_global
            FROM $this->table_usuarios u
            WHERE u.validado = true
            ORDER BY u.pais, u.ciudad, mejor_puntaje DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener Ranking por Ciudad: " . $e->getMessage());
            return false;
        }
    }
}
