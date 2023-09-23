<?php
class RolesController{
    private $model;

    public function __construct(){
       $this->model = new RolesModel(); 
    }

    public function getRoles($id = 0){
        $data = array('exito' => false, 'respuesta' => array());
        if($id < 0 || !is_numeric($id)){
            $data['respuesta'] = 'Se esperaba un id numerico';
            return false;
        }
        $roles = $this->model->getRoles($id);
        if(!$roles['exito']){
            $data['respuesta'] = $roles['data'];
            return false;
        }

        $data['exito'] = true;
        $data['respuesta'] = $roles['data'];
        return $data;
    }
}


?>