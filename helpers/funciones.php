<?php

/**
 * funciones que servirá como helpers en la aplicacion
 */
#devuelve la ruta de la app
function rutaApp(){
    return 'localhost/catalogoFarmacia/';
}
# sanitizarInput(): sanitiza los campos de entrada de formularios y o variables en la app
function sanitizarInput($input) {
    if($input && !(empty($input))){
        $output = htmlspecialchars(stripslashes(trim($input)));
    }else {
        $output = false;
    }
  return htmlspecialchars($output);
 
}

#emailValido(): verificará que un email es valido
function validarEmail($email){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }else{
        return true;
    }
}

#validarNumero(): verifica que solo contenga numeros
function validarNumero($numero){
    if (!is_numeric($numero)) {
        return false;
    }else{
        return true;
    } 
}

#validarLetras(): verifica que solo contenga letras
function validarLetras($texto){
    if (!ctype_alpha ($texto )) {
        return false;
    }else{
        return true;
    }
}

#validarAlfaNumerios(): verificar que contenga solo letras un numeros
function validarAlfaNumerico($texto){
    if(ctype_alnum($texto)) {
        return true;
    }else{
        return false;
    }
}

#validarTexto(): verifica que solo contenga letras y espacios
function validarTexto($texto){
    if ((preg_match("/^[a-zA-Z-' ]+$/",$texto)) ) {
        return true;
    }else{
        return false;
    }
}

#validarUrl
function validarWeb($web){
    if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$web)) {
        return false;
    }else {
        return true;
    }
}


// funcion para generar avatar para presonal
function generarAvatar($character, $carpeta) {
	$directorio = "../img/".$carpeta;
	if (!is_dir($directorio)) {
       mkdir($directorio, 0755, true);
    }
    $file = time() . ".png";
    $path = $directorio."/".$file; 
	$font = dirname(__FILE__) . '/font/arial.ttf';
	$image = imagecreate(200, 200);
	$red = rand(0, 255);
	$green = rand(0, 255);
	$blue = rand(0, 255);
    imagecolorallocate($image, $red, $green, $blue);  
    $textcolor = imagecolorallocate($image, 255,255,255);  

    imagettftext($image, 100, 0, 55, 150, $textcolor, $font, $character);  
    //header("Content-type: image/png");   
    imagepng($image, $path);
    imagedestroy($image);
    return $file;
}


?>