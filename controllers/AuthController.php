<?php
class AuthController {
    private $userModel;
    private $mustache;

    public function __construct($userModel, $mustache) {
        $this->userModel = $userModel;
        $this->mustache = $mustache;
    }

    public function showLogin() {
        echo $this->mustache->render('login');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->findUserByUsername($username);

            if ($user && password_verify($password, $user['contraseña'])) {
                $_SESSION['user_id'] = $user['id_usuario'];
                header('Location: /home');
                exit();
            } else {
                echo $this->mustache->render('login', ['error' => 'Usuario o contraseña incorrectos']);
            }
        }
    }
}
