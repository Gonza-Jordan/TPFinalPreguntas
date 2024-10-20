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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->findUserByUsername($username);

            if ($user && password_verify($password, $user['contraseña'])) {
                $_SESSION['user_id'] = $user['id_usuario'];
                header('Location: /home/show');
                exit();
            } else {
                $this->mustache->show('logIn', ['error' => 'Usuario o contraseña incorrectos']);
            }
        } else {
            $this->mustache->show('logIn');
        }
    }
}

