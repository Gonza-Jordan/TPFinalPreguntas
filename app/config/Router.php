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

    public function route($controllerName, $methodName)
    {
        // Agregar manejo especial para 'perfil'
        if ($controllerName === 'perfil') {
            $this->routeToPerfilController($methodName);
            return;
        }

        // Continuar con la lógica original
        $controller = $this->getControllerFrom($controllerName);

        // Verifica si el método está especificado, si no, usa el método por defecto
        if (empty($methodName)) {
            $methodName = $this->defaultMethod; // Asegúrate de que $this->defaultMethod esté configurado
        }

        $this->executeMethodFromController($controller, $methodName);
    }

    private function routeToPerfilController($methodName) {
        $controller = $this->configuration->getPerfilController();

        // Si no se especifica un método, usar 'mostrarPerfil' por defecto
        if (empty($methodName)) {
            $methodName = 'mostrarPerfil';
        }

        // Suponiendo que siempre se mostrará el perfil del usuario autenticado
        if (isset($_SESSION['user_id'])) {
            $controller->mostrarPerfil($_SESSION['user_id']);
        } else {
            // Redirigir al login si no hay sesión
            header('Location: /TPFinalPreguntas/app/index.php?page=auth&action=show');
            exit();
        }
    }

    private function getControllerFrom($module) {
        $controllerName = 'get' . ucfirst($module) . 'Controller';
        $validController = method_exists($this->configuration, $controllerName) ? $controllerName : $this->defaultController;
        return call_user_func(array($this->configuration, $validController));
    }

    private function executeMethodFromController($controller, $method) {
        $validMethod = method_exists($controller, $method) ? $method : $this->defaultMethod;
        call_user_func(array($controller, $validMethod));
    }
}
