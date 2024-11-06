<?php

class EditorController
{
    private $presenter;
    private $model;

    public function __construct($presenter, $model){
        $this->presenter = $presenter;
        $this->model = $model;
    }


}