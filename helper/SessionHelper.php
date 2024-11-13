<?php
class SessionHelper {
    public static function verificarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /TPFinalPreguntas/auth/show');
            exit();
        }
    }
}