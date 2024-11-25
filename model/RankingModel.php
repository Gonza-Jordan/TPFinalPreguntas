<?php
class RankingModel {
    private $conn;
    private $table_ranking = "ranking";
    private $table_usuarios = "usuarios";
    private $table_partidas = "partidas";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function actualizarRanking() {
        $queryUsuarios = "SELECT id_usuario, puntaje_total FROM " . $this->table_usuarios . " ORDER BY puntaje_total DESC";
        $stmtUsuarios = $this->conn->prepare($queryUsuarios);
        $stmtUsuarios->execute();
        $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

        $queryRanking = "INSERT INTO " . $this->table_ranking . " (id_usuario, puntaje_total, posicion)
                     VALUES (:id_usuario, :puntaje_total, :posicion)
                     ON DUPLICATE KEY UPDATE puntaje_total = :puntaje_total, posicion = :posicion";

        $stmtRanking = $this->conn->prepare($queryRanking);

        foreach ($usuarios as $index => $usuario) {
            $posicion = $index + 1;  // Crear una variable temporal para la posición

            $stmtRanking->bindParam(':id_usuario', $usuario['id_usuario'], PDO::PARAM_INT);
            $stmtRanking->bindParam(':puntaje_total', $usuario['puntaje_total'], PDO::PARAM_INT);
            $stmtRanking->bindParam(':posicion', $posicion, PDO::PARAM_INT);

            $stmtRanking->execute();
        }
    }

    public function obtenerRanking($limite = 10, $pagina = 1) {
        $offset = ($pagina - 1) * $limite;

        $query = "
        SELECT DISTINCT id_usuario, nombre_usuario, puntaje_total
        FROM " . $this->table_usuarios . "
        WHERE puntaje_total > 0
        ORDER BY puntaje_total DESC
        LIMIT :limite OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuarios as $index => &$usuario) {
            $usuario['posicion'] = $offset + $index + 1;
        }

        return $usuarios;
    }

    public function contarUsuariosConPuntaje() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_usuarios . " WHERE puntaje_total > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] ?? 0;
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
