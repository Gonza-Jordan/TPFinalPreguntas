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
        $error = null; // Inicializa la variable error
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->findUserByUsername($username);

            if ($user && password_verify($password, $user['contraseña'])) { // Asegúrate de que este campo sea correcto
                $_SESSION['user_id'] = $user['id_usuario'];
                header('Location: /TPFinalPreguntas/app/index.php?page=home&action=show'); // Ruta correcta para tu home
                exit();
            } else {
                $error = 'Usuario o contraseña incorrectos'; // Asigna el mensaje de error
            }
        }

        var_dump($user); // Muestra el usuario encontrado
        var_dump($error); // Muestra el mensaje de error

        // Renderiza la vista con el mensaje de error
        $this->mustache->show('logIn', ['error' => $error]);
    }

}
