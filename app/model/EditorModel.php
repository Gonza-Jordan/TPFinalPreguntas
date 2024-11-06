<?php

class EditorModel
{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

}