<?php

class Presenter
{
    public function __construct() {}

    public function show($view, $data = []){
        include_once('view/home.mustache');
    }

}