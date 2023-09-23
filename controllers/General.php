<?php

#require_once('../models/ModelGeneral.php');

class ControllerGeneral extends ModelGeneral{
    public $model;
    public function __construct(){
        $this->model=new ModelGeneral();
    }
    public function get($tabla, $campos = ''){
        return $this->model->get($tabla);
    }
}



?>