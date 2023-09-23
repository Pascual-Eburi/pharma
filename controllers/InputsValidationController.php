
<?php

class InputsValidacionController{
    //public $inputs = [];

    public function validarNumeros($campo, $valor, $min, $max){
        $validez = array('valido' => false, 'error' => '');
        if (validarNumero($valor)){
            if ($valor < $min || $valor > $max ){
                if($valor < $min){
                    $validez['error'] = 'El campo '.$campo.' tiene que ser mayor o igual que '.$min;
                } else {
                    $validez['error'] = 'El campo '.$campo.' tiene que ser menor que '.$max;
                }
                $validez['valido'] = false;
                return $validez;
            } else {
                $validez['valido'] = true;
                return $validez;
            }
        }else {
            $validez['error'] = 'El campo '.$campo.' con valor '.$valor.' no es un numero';
            return $validez;
        }
    
        
    }


    public function validarNombre($campo, $valor = null, $min, $max){
        $validez = array('valido' => false, 'error'=> '');
        if(!$valor || $valor == null){
            $validez['error'] = "El campo $campo  es está vacío";
            return $validez;
        }
        if( strlen($valor) > $max || strlen($valor) < $min ){
            if(strlen($valor) > $max ){  
                $validez['error'] = "El campo $campo como máximo admite $max carateres";
            }else {
                $validez['error'] = "El campo $campo como minimo admite $min carater/es";
            }
            $validez['valido'] = false;
            return $validez;

        } else {
            if( $this->validarTexto($valor) ){
                $validez['valido'] = true;
                return $validez;
            }else {
                $validez['valido'] = false;
                $validez['error'] = "El campo $campo solo puede contener letras y espacios"; 
                return $validez;
            }

        }
        
        
    }

    # sanitizar inputs
    public function sanitizarInput($input) {
        if($input && !(empty($input))){
            $output = htmlspecialchars(stripslashes(trim($input)));
        }else {
            $output = false;
        }
      return htmlspecialchars($output);
    }

    public function comparDosClaves($clave_1 = null, $clave_2 = null){
        if(!$clave_1 || !$clave_2){
            return false;
        }
        if($clave_1 === $clave_2){
            return true;
        }
        return false;
    }


    /**
     * validar extension del archivo
     */
    public function validarExtensionArchivo($archivo = array(), $extensiones_validas = array()){
        $validez = array('valido' => false, 'error'=> '');

        if(count($archivo) <=0){
            $validez['error'] = "Se esperaba un archivo a ser validado";
            return $validez;  
        }
        if(!$archivo['name']){
            $validez['error'] = "Asegúrate de proporcionar un archivo con nombre";
            return $validez;  
        }
        if(count($extensiones_validas) <=0){
            $validez['error'] = "Se esperaba un array de extensiones validas";
            return $validez;  
        }

        # todo ok
        $nombre_dividido = explode('.', $archivo['name']);
        $extension = end($nombre_dividido);
        if( !in_array($extension, $extensiones_validas)){
            $validez['error'] = "La extension $extension no es valida, solo: ".implode(', ', $extensiones_validas );
            return $validez;   
        }

        $validez['valido'] = true;
        return $validez;
    }
    
    #emailValido(): verificará que un email es valido
    public function validarEmail($campo, $email, $min, $max){
        $validez = array('valido' => false, 'error'=> '');
        if(!$email || empty($email) || $email == 'null' || $email == null){
            $validez['error'] = "El campo $campo está vacio";
            return $validez; 
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $validez['error'] = "El campo $campo no tiene formato de email valido";
            return $validez;
        }

        if(strlen($email) < $min){
            $validez['error'] = "El campo $campo tiene que tener más de $min carrateres";
            return $validez; 
        }

        if(strlen($email) > $max){
            $validez['error'] = "El campo $campo tener que tener menos de $max carrateres";
            return $validez;
        }

        $validez['valido'] = true;
        return $validez;
    }
    
    #validarNumero(): verifica que solo contenga numeros
    public function validarNumero($campo, $numero, $min, $max = null){
        $validez = array('valido' => false, 'error'=> '');
        if(!$numero || empty($numero)){
            $validez['error'] = "El campo $campo está vacío";
            return $validez; 
        }
        if (!is_numeric($numero)) {
            $validez['error'] = "El campo $campo no es un número";
            return $validez;
        }

        if(strlen((string)abs($numero)) < $min){
            $validez['error'] = "El campo $campo tiene que tener más de $min carrateres";
            return $validez; 
        }

        if(strlen((string)abs($numero)) > $max){
            $validez['error'] = "El campo $campo tener que tener menos de $max carrateres";
            return $validez;
        }

        $validez['valido'] = true;
        return $validez;
    }

    /**
     * valida un fecha comprobando el añao minimo y el año maximo
     * @param string $campo: el nombre del campo que contiene la fecha
     * @param string $fecha: la fecha a validar
     * @param int $min : el año minimo que puede contener la fecha
     * @param int $max: el año maximo que puede contener la fecha
     * @return array $ validez ('valido' => boolean, 'error' => string)
     * 
     */
    public function validarFecha($campo, $fecha, $min, $max = null){
        $validez = array('valido' => false, 'error'=> '');
        if(!$fecha || empty($fecha)){
            $validez['error'] = "El campo $campo está vacío";
            return $validez; 
        }

        $year = date('Y', strtotime($fecha));
        if(!$year){ 
            $validez['error'] = "El campo $campo no contiene un formato correcto";
            return $validez; 
        }

        if($year < $min){
            $validez['error'] = "El año tiene que ser menor a $min";
            return $validez;
        }

        if($year > $max){
            $validez['error'] = "El año tiene que ser mayor a $max";
            return $validez;
        }

        $validez['valido'] = true;
        return $validez;
    }
    
    #validarLetras(): verifica que solo contenga letras
    public function validarLetras($campo, $texto, $min, $max){
        if (!ctype_alpha ($texto )) {
            $validez['error'] = "El campo $campo no contiene solo letras";
            return false;
        }        
        if(strlen($texto) < $min){
            $validez['error'] = "El campo $campo tiene que tener más de $min carrateres";
            return $validez; 
        }

        if(strlen($texto) > $max){
            $validez['error'] = "El campo $campo tener que tener menos de $max carrateres";
            return $validez;
        }

        $validez['valido'] = true;
        return $validez;
    }
    
    #validarAlfaNumerios(): verificar que contenga solo letras un numeros
    public function validarAlfaNumerico($texto){
        if(ctype_alnum($texto)) {
            return true;
        }else{
            return false;
        }
    }
    
    #validarTexto(): verifica que solo contenga letras y espacios
    public function validarTexto($texto){
        if ((preg_match("/^[a-zA-Z-ñÑ.&@' ]+$/",$texto)) ) {
            return true;
        }else{
            return false;
        }
    }
    
    #validarUrl
    public function validarWeb($web){
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$web)) {
            return false;
        }else {
            return true;
        }
    }


}


?>