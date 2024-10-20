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
//                header('Location: /home/show');
                $this->mustache->show('home');
                exit();
            } else {
                // Entra siempre por aca, dejo el echo de abajo para verificar
                //echo '<h1>Usuario o contraseña incorrectos</h1>';
                $this->mustache->show('login', ['error' => 'Usuario o contraseña incorrectos']);
            }
        } else {
            $this->mustache->show('login');
        }

    }
}
