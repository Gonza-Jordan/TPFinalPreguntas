<?php

class HomeModel
{
    private $usuarioModel;

    public function __construct($db) {
        $this->usuarioModel = new UsuarioModel($db);
    }

    public function obtenerUsuarioPorId($id_usuario) {
        return $this->usuarioModel->obtenerUsuarioPorId($id_usuario);
    }
}
