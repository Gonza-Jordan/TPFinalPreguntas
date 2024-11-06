<?php
class AuthController {
    private $mustache;
    private $userModel;

    public function __construct($mustache, $userModel) {
        $this->mustache = $mustache;
        $this->userModel = $userModel;
    }

    public function show() {
        $mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : null;
        $data = [];

        if ($mensaje === 'activacion_exito') {
            $data['alert_success'] = 'Tu cuenta ha sido activada con éxito. Ahora puedes iniciar sesión.';
        } elseif ($mensaje === 'token_invalido') {
            $data['alert_error'] = 'El enlace de activación no es válido o ha expirado.';
        }

        $this->mustache->show('logIn', $data);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Buscar usuario en la base de datos por nombre de usuario
            $user = $this->userModel->findUserByUsername($username);

            // Verificar si el usuario existe y si la contraseña es correcta
            if ($user && password_verify($password, $user['contraseña'])) {
                // Iniciar sesión y almacenar datos importantes del usuario en la sesión
                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['tipo_usuario'] = $user['tipo_usuario']; // Guardar el rol de usuario (ej., 'editor')

                // Redireccionar al home
                header('Location: /TPFinalPreguntas/app/index.php?page=home&action=show');
                exit();
            } else {
                // Si las credenciales son incorrectas, mostrar un mensaje de error
                $data = [
                    'errorHTML' => '<div class="alert alert-danger">Usuario o contraseña incorrectos</div>'
                ];
                $this->mustache->show('logIn', $data);
            }
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /TPFinalPreguntas/app/index.php?page=auth&action=show');
        exit();
    }
}
