<?php

class Router {
    private $defaultController;
    private $defaultMethod;
    private $configuration;

    public function __construct($configuration, $defaultController, $defaultMethod) {
        $this->defaultController = $defaultController;
        $this->defaultMethod = $defaultMethod;
        $this->configuration = $configuration;
    }

    public function getAuthController() {
        return new AuthController($this->getMustache(), $this->getUserModel());
    }

    public function route($controllerName, $methodName, $id = null) {
        if ($controllerName === 'perfil') {
            $this->routeToPerfilController($methodName);
            return;
        }

        if ($controllerName === 'ranking' && $methodName === 'verPerfilJugador') {
            $this->routeToPerfilJugador($id);
            return;
        }

        if ($controllerName === 'pregunta' && ($methodName === 'crear' || $methodName === 'editar')) {
            $this->routeToPreguntaController($methodName, $id);
            return;
        }

        $controller = $this->getControllerFrom($controllerName);

        if (empty($methodName)) {
            $methodName = $this->defaultMethod;
        }

        $this->executeMethodFromController($controller, $methodName);
    }

    private function routeToPerfilController($methodName) {
        $controller = $this->configuration->getPerfilController();

        if (empty($methodName)) {
            $methodName = 'mostrarPerfil';
        }

        if (isset($_SESSION['user_id'])) {
            $controller->mostrarPerfil($_SESSION['user_id']);
        } else {
            header('Location: /TPFinalPreguntas/app/index.php?page=auth&action=show');
            exit();
        }
    }

    private function routeToPerfilJugador($id) {
        $controller = $this->configuration->getRankingController();

        if ($id) {
            $controller->verPerfilJugador($id);
        } else {
            header('Location: /TPFinalPreguntas/app/index.php?page=auth&action=show');
            exit();
        }
    }

    private function routeToPreguntaController($methodName, $id) {
        if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'editor') {
            $controller = $this->configuration->getPreguntaController();
            if (empty($methodName)) {
                $methodName = 'listar';
            }
            $this->executeMethodFromController($controller, $methodName, $id);
        } else {
            echo '<div class="alert alert-danger">No tienes permisos para acceder a esta sección.</div>';
            exit();
        }
    }

    private function getControllerFrom($module) {
        $controllerName = 'get' . ucfirst($module) . 'Controller';
        $validController = method_exists($this->configuration, $controllerName) ? $controllerName : $this->defaultController;
        return call_user_func(array($this->configuration, $validController));
    }

    private function executeMethodFromController($controller, $method, $id = null) {
        $validMethod = method_exists($controller, $method) ? $method : $this->defaultMethod;
        $reflection = new ReflectionMethod($controller, $validMethod);
        $numParams = $reflection->getNumberOfParameters();

        switch ($numParams) {
            case 1:
                call_user_func(array($controller, $validMethod), $id);
                break;
            default:
                call_user_func(array($controller, $validMethod));
        }
    }
}
