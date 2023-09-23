<?php
/**
 * Controlador que se encarga de manejar las vistas de la aplicacion
 * 
 */

class ViewController {
    #ruta de la vista
    private static $ruta_vista = './views/';

    public function load_view($vista){
        if( file_exists(self::$ruta_vista . $vista. '.php') ){
           require_once(self::$ruta_vista . $vista. '.php');

        } else {
            echo 'El archivo '.self::$ruta_vista . $vista. '.php no existe';
        }
        
    }

    #contructor de la clase
    public function __destruct(){

    }
}



?>