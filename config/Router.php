<?php

class Router {
    private $defaultController;
    private $defaultMethod;
    private $configuration;

    public function __construct($configuration, $defaultController = 'home', $defaultMethod = 'show') {
        $this->defaultController = $defaultController;
        $this->defaultMethod = $defaultMethod;
        $this->configuration = $configuration;
    }

    public function route($controllerName, $methodName, $id = null) {
        // Rutas sin autenticación
        if ($controllerName === 'auth' && in_array($methodName, ['show', 'login', 'logout'])) {
            $controller = $this->configuration->getAuthController();
            $controller->$methodName();
            return;
        }

        if ($controllerName === 'registro' && $methodName === 'registrar') {
            $controller = $this->configuration->getRegistroController();
            $controller->registrar();
            return;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /TPFinalPreguntas/auth/show');
            exit();
        }

        if ($controllerName === 'pregunta' && $_SESSION['tipo_usuario'] !== 'editor') {
            header('Location: /TPFinalPreguntas/home/show');
            exit();
        }

        $controller = $this->getControllerFrom($controllerName);
        if (empty($methodName)) {
            $methodName = $this->defaultMethod;
        }
        $this->executeMethodFromController($controller, $methodName, $id);
    }

    private function validarAcceso($controllerName, $methodName) {
        $permisos = [
            'editor' => [
                'pregunta' => ['crear', 'editar', 'revisarSugerencias', 'rechazarPregunta', 'aprobarPregunta', 'listarPreguntas'],
                'ranking' => ['show']
            ],
            'jugador' => [
                'pregunta' => ['sugerir'],
                'ranking' => ['show']
            ],
        ];

        if ($controllerName === 'auth' && in_array($methodName, ['show', 'login', 'logout'])) {
            return true;
        }
        if ($controllerName === 'registro' && $methodName === 'registrar') {
            return true;
        }

        $rolUsuario = $_SESSION['tipo_usuario'] ?? null;

        if ($rolUsuario && isset($permisos[$rolUsuario][$controllerName])) {
            return in_array($methodName, $permisos[$rolUsuario][$controllerName]);
        }

        return false;
    }

    private function routeToPerfilController($methodName) {
        $controller = $this->configuration->getPerfilController();

        if (empty($methodName)) {
            $methodName = 'mostrarPerfil';
        }

        if (isset($_SESSION['user_id'])) {
            $controller->mostrarPerfil($_SESSION['user_id']);
        } else {
            header('Location: /TPFinalPreguntas/auth/show');
            exit();
        }
    }

    private function routeToPerfilJugador($id) {
        $controller = $this->configuration->getRankingController();

        if ($id) {
            $controller->verPerfilJugador($id);
        } else {
            header('Location: /TPFinalPreguntas/auth/show');
            exit();
        }
    }

    private function routeToPreguntaController($methodName, $id = null) {
        if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'editor') {
            $controller = $this->configuration->getPreguntaController();
            $this->executeMethodFromController($controller, $methodName, $id);
        } else {
            header('Location: /TPFinalPreguntas/auth/show');
            exit();
        }
    }

    private function getControllerFrom($module) {
        $controllerName = 'get' . ucfirst($module) . 'Controller';
        $validController = method_exists($this->configuration, $controllerName) ? $controllerName : $this->defaultController;
        return call_user_func([$this->configuration, $validController]);
    }

    private function executeMethodFromController($controller, $method, $id = null) {
        $validMethod = method_exists($controller, $method) ? $method : $this->defaultMethod;
        $reflection = new ReflectionMethod($controller, $validMethod);
        $numParams = $reflection->getNumberOfParameters();

        switch ($numParams) {
            case 1:
                call_user_func([$controller, $validMethod], $id);
                break;
            default:
                call_user_func([$controller, $validMethod]);
        }
    }
}
