
<?php

class Pagina {
    private $titulo;
    public function __construct($path){
        $this->titulo=ucfirst($path);

    }

    public function setTitulo($pagina){
        $this->titulo=ucfirst($pagina);
    }

    public function getTitulo(){
        return $this->titulo;
    }
}


/*
function base_url() {
    $url_proyecto ='localhost/catalogoFarmacia/';
    return $url_proyecto;
}
echo '<pre>';
echo 'La ruta del proyecto es: '.base_url().'<br>';


function titulo (){
    $archivo = __DIR__;
    return $archivo;
}
echo 'el DIR es: '.titulo().'<br>';

$r = dirname(__FILE__);
echo 'R es: '.$r;

echo '</pre>';

$path = __FILE__ ;
echo ucfirst(basename($path,".php"));

*/

?> 

