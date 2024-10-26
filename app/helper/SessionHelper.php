<?php
class SessionHelper {
    public static function verificarSesion() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /TPFinalPreguntas/app/index.php?page=auth&action=show');
            exit();
        }
    }
}