<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="theme-color" content="#1e293b">
  <meta name="mobile-web-app-capable" content="yes">
  <title> <?php echo $titulo_pagina; ?> - Pharma</title>
  <!-- ================= ICONOS ICONSCOUT ============================== 
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.6/css/unicons.css">
    -->

  <!-- CSS files -->
  <!-- cdn libreria de icones  
    <link rel="stylesheet" href="https://unpkg.com/@tabler/icons@latest/iconfont/tabler-icons.min.css">
    -->
  <!-- libreria de iconos local -->
  <link rel="stylesheet" href="public/plugins/tabler/iconfont/tabler-icons.min.css">
  <link href="<?php rutaApp(); ?>public/plugins/tabler/css/tabler.min.css" rel="stylesheet" />
  <link href="<?php rutaApp(); ?>public/plugins/tabler/css/tabler-flags.min.css" rel="stylesheet" />
  <link href="<?php rutaApp(); ?>public/plugins/tabler/css/tabler-payments.min.css" rel="stylesheet" />
  <link href="<?php rutaApp(); ?>public/plugins/tabler/css/tabler-vendors.min.css" rel="stylesheet" />
  <link href="<?php rutaApp(); ?>public/plugins/tabler/css/demo.min.css" rel="stylesheet" />

  <!-- DataTables -->
  <link rel="stylesheet" href="<?php rutaApp(); ?>public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php rutaApp(); ?>public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php rutaApp(); ?>public/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <!-- custom css -->
  <link href="<?php rutaApp(); ?>public/appcss/custom.css" rel="stylesheet" />
</head>


<body id="body-app" class='<?php if ($titulo_pagina == 'Login' || $titulo_pagina == 'Error404') {
                              echo 'border-top-wide border-primary d-flex flex-column';
                            }  ?>'>
  <!-- loader --->
  <div class="contenedor-loader show" id="loader-app">
    <div class="loader-app-logo">
      <img src="./public/logo.png" alt="pharma">
    </div>
    <div class="loader-app">
      <div></div>
      <div></div>
      <div></div>
    </div>
  </div>

  <?php if ($titulo_pagina != 'Login' && $titulo_pagina != 'Error404') : ?>
    <div class="page">
      <!-- DASH -->
      <?php require_once('aside.php'); ?>
      <?php require_once('top-bar.php'); ?>
      <div class="page-wrapper">
        <div class="container-xl">
        <?php endif; ?>