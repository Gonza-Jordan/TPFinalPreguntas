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

        if ($controllerName === 'registro' && $methodName === 'registrar') {
            $controller = $this->configuration->getRegistroController();
            $controller->registrar();
            return;
        }

        if ($controllerName === 'perfil') {
            $this->routeToPerfilController($methodName);
            return;
        }

        if ($controllerName === 'ranking' && $methodName === 'verPerfilJugador') {
            $this->routeToPerfilJugador($id);
            return;
        }

        if ($controllerName === 'pregunta' && in_array($methodName, ['crear', 'editar'])) {
            $this->routeToPreguntaController($methodName, $id);
            return;
        }

        if ($controllerName === 'pregunta' && in_array($methodName, ['rechazarPregunta', 'aprobarPregunta'])) {
            $controller = $this->getControllerFrom($controllerName);
            $controller->$methodName($id);
            return;
        }

        // Ruta genérica para todos los controladores
        $controller = $this->getControllerFrom($controllerName);
        if (empty($methodName)) {
            $methodName = $this->defaultMethod;
        }
        $this->executeMethodFromController($controller, $methodName, $id);
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

    public function listarPreguntas() {
        $data = $this->preguntaModel->getPreguntas();
        $this->presenter->show('listarPreguntas', ['preguntas' => $data]);
    }
}
