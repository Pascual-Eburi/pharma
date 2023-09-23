<?php 
/**
 * archivo que cotiene funciones que sirver como helpers para la app, estos archivos ejercen fucniones de parseo de datos, etc..
 */
class Helpers{
    /**
     * formatea una fecha data en el formato deseado
     * @param string $fecha => fecha a ser formatead
     * @param string $formato : formato a dar a la fecha
     * @return string $fecha : fecha formateada
     */
    public function formatDate( $fecha = null , $formato = null ){
        if(!$fecha || !$formato) return false;
        # craer nuevo objeto fecha
        $date = new DateTime($fecha);
        $fecha = $date->format($formato);
        return ($fecha)  ? $fecha : false;
    }
}


?>