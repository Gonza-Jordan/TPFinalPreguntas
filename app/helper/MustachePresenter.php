<?php

class MustachePresenter {
    private $templatePath;

    public function __construct($templatePath) {
        $this->templatePath = $templatePath;
    }

    public function show($template, $data = []) {
        $mustache = new Mustache_Engine();
        $templateFile = $this->templatePath . '/' . $template . '.mustache';

        if (file_exists($templateFile)) {
            $templateContent = file_get_contents($templateFile);
            echo $mustache->render($templateContent, $data);
        } else {
            throw new Exception("Template not found: " . $templateFile);
        }
    }
}