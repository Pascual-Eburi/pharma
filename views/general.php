<?php

include ('../controllers/ControllerGeneral.php');

$prueba = new ControllerGeneral();
echo "<pre>";
$datos = $prueba->get('pais');
var_dump($datos);
echo "</pre>";
?>