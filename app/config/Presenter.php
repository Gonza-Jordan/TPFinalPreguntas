<?php

class Presenter
{
    public function __construct() {}

    public function show($view, $data = []){
        //include_once('view/header.mustache');
//        include_once('view/'.$view.'.mustache');
        include_once('view/home.mustache');
        //include_once('view/footer.mustache');
    }

}