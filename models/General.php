<?php

#require_once('../models/Database.php');

class ModelGeneral extends Database {
    #$private id;
    public $obj ;
    public function __construct() {
        $this->obj = new Database();
    }
    
    public function get($tabla, $campos = ''){
        return $this->obj->get($tabla);
    }
}



?>