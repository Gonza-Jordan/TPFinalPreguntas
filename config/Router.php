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
        $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = explode('/', trim($urlPath, '/'));

        $controllerName = $segments[1] ?? $this->defaultController;
        $methodAndParams = $segments[2] ?? $this->defaultMethod;
        
        $methodName = explode('&', $methodAndParams)[0];

        error_log("Routing: Controller = $controllerName, Method = $methodName");

        if ($controllerName === 'auth' && in_array($methodName, ['show', 'login', 'logout'])) {
            $controller = $this->configuration->getAuthController();
            $controller->$methodName();
            return;
        }

        if ($controllerName === 'registro' && in_array($methodName, ['show', 'registrar'])) {
            $controller = $this->configuration->getRegistroController();
            $controller->$methodName();
            return;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /TPFinalPreguntas/auth/show');
            exit();
        }

        if ($controllerName === 'pregunta') {
            if ($_SESSION['tipo_usuario'] === 'jugador' && $methodName !== 'sugerir') {
                header('Location: /TPFinalPreguntas/home/show');
                exit();
            } elseif ($_SESSION['tipo_usuario'] !== 'editor' && $methodName !== 'sugerir') {
                header('Location: /TPFinalPreguntas/home/show');
                exit();
            }
        }

        if ($controllerName === 'partida' && $methodName === 'crearPartida' && $_SESSION['tipo_usuario'] !== 'jugador') {
            header('Location: /TPFinalPreguntas/home/show');
            exit();
        }

        if ($controllerName === 'admin' && $methodName === 'show' && $_SESSION['tipo_usuario'] === 'administrador') {
            $filtro_tiempo = $_GET['filtro_tiempo'] ?? null; // Captura el filtro enviado
            $controller = $this->configuration->getAdminController();
            $this->executeMethodFromController($controller, $methodName, $filtro_tiempo);
            return;
        }

        if ($controllerName === 'admin' && $methodName === 'show' && $_SESSION['tipo_usuario'] !== 'administrador') {
            header('Location: /TPFinalPreguntas/home/show');
            exit();
        }

        if ($controllerName === 'historial' && $methodName === 'show') {
            error_log("Accediendo al método show en HistorialController");
            $controller = $this->configuration->getHistorialController();
            $controller->show();
            return;
        }

        $controller = $this->getControllerFrom($controllerName);
        if (empty($methodName)) {
            $methodName = $this->defaultMethod;
        }

        $pagina = $_GET['pagina'] ?? null;

        $this->executeMethodFromController($controller, $methodName, $pagina, $id);
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
            'administrador' => [
                'admin' => ['show'],
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

    private function executeMethodFromController($controller, $method, $param = null, $id = null) {
        $validMethod = method_exists($controller, $method) ? $method : $this->defaultMethod;
        $reflection = new ReflectionMethod($controller, $validMethod);
        $numParams = $reflection->getNumberOfParameters();

        switch ($numParams) {
            case 1:
                call_user_func([$controller, $validMethod], $param ?? $id);
                break;
            case 2:
                call_user_func([$controller, $validMethod], $param, $id);
                break;
            default:
                call_user_func([$controller, $validMethod]);
        }
    }
}