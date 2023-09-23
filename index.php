<?php

#cargar el autoloader para que cargue los archivos necesarios para la app
require_once('./controllers/Autoload.php');

// funciones para validaciones
require_once('./helpers/funciones.php');
// libreria php para prevenir ataques csfr
require_once('./helpers/nocsrf.php');
require_once('./controllers/InputsValidationController.php');

// helper de funciones para la app
//require_once('./helpers/Helpers.php');


//libreria para manejo de archivos excel o csv
require_once('./helpers/phpOffice/autoload.php');

// libreria para generar archivos pdf
require_once('./helpers/TCPDF/tcpdf.php');

$autoload = new Autoload();

#obtener la ruta
if (isset($_GET['r'])) { $ruta = $_GET['r']; } else { $ruta = 'dashboard';}

#inicializar el router
$app = new Router($ruta);

$helper = new Helpers();

?>


