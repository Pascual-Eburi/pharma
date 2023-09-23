<?php 
//tittulo de la pagina
$titulo_pagina = ucfirst(basename(__FILE__,".php"));
include ('base/header.php');
// libreria php para prevenir ataques csfr
#require_once('./helpers/nocsrf.php');

?>

<div class="page page-center">
      <div class="container-tight py-4">
        <div class="text-center mb-4 login-logo">
          <a href="." class="navbar-brand navbar-brand-autodark">
            <img src="static/logo-negro.png" alt=""></a>
        </div>
        <form class="card card-md" method="post" autocomplete="off" id='formularioLogin'>
          <div class="card-body">
            <h2 class="card-title text-center mb-4">Inicia sesion para acceder al sistema </h2>
            <div class="mb-3">
              <label class="form-label">Tu email</label>
              <!-- email -->
              <input type="email" class="form-control" id="emailUsuario" name="email" placeholder="Introduzca tu email..." autocomplete="off">
            </div>
            <div class="mb-2">
              <label class="form-label">
                Contraseña
                <span class="form-label-description">
                  <a href="<?php rutaApp(); ?>forgot-password.html">He olvidado mi clave</a>
                </span>
              </label>
              <div class="input-group input-group-flat">
                <!-- clave -->
                <input type="password" id="claveUsuario" name="clave" class="form-control"  placeholder="Password"  autocomplete="off">
                <span class="input-group-text">
                  <a href="#" id="opcionesClave" class="link-secondary" title="Mostrar clave" data-bs-toggle="tooltip">
                    <!-- mostrar clave -->
                    <svg xmlns="http://www.w3.org/2000/svg" id="mostrarClave" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <circle cx="12" cy="12" r="2" /><path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                    </svg>
                    <!-- ocultar clave -->
                    <svg xmlns="http://www.w3.org/2000/svg" id="ocultarClave" class="icon elementoOculto" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="3" y1="3" x2="21" y2="21" /><path d="M10.584 10.587a2 2 0 0 0 2.828 2.83" /><path d="M9.363 5.365a9.466 9.466 0 0 1 2.637 -.365c4 0 7.333 2.333 10 7c-.778 1.361 -1.612 2.524 -2.503 3.488m-2.14 1.861c-1.631 1.1 -3.415 1.651 -5.357 1.651c-4 0 -7.333 -2.333 -10 -7c1.369 -2.395 2.913 -4.175 4.632 -5.341" /></svg>
                  </a>
                </span>

              </div>
            </div>
            <div class="mb-2">
              <label class="form-check">
                <input type="checkbox" class="form-check-input"/>
                <span class="form-check-label">Recordame en este dispositivo</span>
              </label>
            </div>
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100" id="botonLogin">
              <span class="spinner-border spinner-border-sm me-2 elementoOculto" role="status" id='spinnerLogin'></span>
                <span id='textoLogin'>Iniciar Sesion</span>
                
                </button>
            </div>
          </div>
        </form>
        <div class="text-center text-muted mt-3">
          ¿ Necesitas ayuda ? Solicítala <a href="./sign-up.html" tabindex="-1">aqui</a>
        </div>
      </div>
    </div>

<?php include ('base/footer.php'); ?>

<script type='module' src='public/appjs/login.js'></script>