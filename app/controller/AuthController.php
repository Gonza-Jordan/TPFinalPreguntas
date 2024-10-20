<?php
class AuthController {
    private $mustache;
    private $userModel;

    public function __construct($mustache, $userModel) {
        $this->mustache = $mustache;
        $this->userModel = $userModel;
    }

    public function show() {
        $this->mustache->show('logIn');
    }

    public function login() {
        $errorHTML = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->findUserByUsername($username);

            if ($user && password_verify($password, $user['contraseña'])) {
                // Inicia sesión y redirige
                $_SESSION['user_id'] = $user['id_usuario'];
                header('Location: /TPFinalPreguntas/app/index.php?page=home&action=show');
                exit();
            } else {
                $errorHTML = '<div class="alert alert-danger">Usuario o contraseña incorrectos</div>';
            }
        }

        $this->mustache->show('logIn', ['errorHTML' => $errorHTML]);
    }

}
