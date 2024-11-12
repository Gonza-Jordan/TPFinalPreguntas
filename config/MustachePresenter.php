<?php

class MustachePresenter {
    private $mustache;
    private $partialsPathLoader;

    public function __construct($partialsPathLoader) {
        Mustache_Autoloader::register();
        $this->mustache = new Mustache_Engine(
            array(
                'partials_loader' => new Mustache_Loader_FilesystemLoader($partialsPathLoader)
            )
        );
        $this->partialsPathLoader = $partialsPathLoader;
    }

    public function show($contentFile, $data = array()) {
        $data['nombre_usuario'] = $_SESSION['nombre_usuario'] ?? 'Invitado';
        $data['foto_perfil'] = $_SESSION['foto_perfil'] ?? 'default.png';

        echo $this->generateHtml($this->partialsPathLoader . '/' . $contentFile . ".mustache", $data);
    }

    public function generateHtml($contentFile, $data = array()) {
        $contentAsString = file_get_contents($contentFile);
        return $this->mustache->render($contentAsString, $data);
    }
}
