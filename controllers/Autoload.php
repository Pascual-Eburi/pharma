<?php

/**
 * cargador automatico de los archivos necesarios de la app.
 * 
 */

class Autoload {
    
    public function __construct(){
        #spl_autoload_register cargara automaticamente las clases
        spl_autoload_register( function($class_name) {
            #ubicacion de las rutas de los modelos y controladores
            $ruta_models = './models/'.$class_name.'.php';
            $ruta_controllers = './controllers/' .$class_name.'.php';
            #clase helpers que contien funciones 
            $ruta_helpers = './helpers/'.$class_name.'.php';
        
            #requerir los archivos si existen
            if( file_exists($ruta_models) ) require_once($ruta_models);
            if( file_exists($ruta_controllers) ) require_once($ruta_controllers);
            if(file_exists($ruta_helpers) ) require_once ($ruta_helpers);
            
        });

        //helpers
        
    }

    #destruir el objeto para liberar memoria
    public function __destruct(){
        #unset($this);
    }
}



?>