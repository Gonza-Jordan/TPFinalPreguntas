<?php

include_once 'vendor/mustache/src/Mustache/Autoloader.php';

class MustachePresenter {
    private $mustache;
    private $viewsPath;

    public function __construct($viewsPath) {
        $this->viewsPath = $viewsPath;
        $this->mustache = new Mustache_Engine(array(
            'loader' => new Mustache_Loader_FilesystemLoader($this->viewsPath),
        ));
    }

    public function render($template, $data = array()) {
        return $this->mustache->render($template, $data);
    }

    public function show($template, $data = array()) {
        echo $this->render($template, $data);
    }
}
