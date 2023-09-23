<?php 
//tittulo de la pagina
$titulo_pagina = ucfirst(basename(__FILE__,".php"));
include ('base/header.php'); 

?>
<div class="page page-center">
  <div class="container-tight py-4">
    <div class="empty">
      <div class="empty-header">404</div>
      <p class="empty-title">¡ Oops… Página no encontrada !</p>
      <p class="empty-subtitle text-muted">
        
        Lo sentimos, pero la página que has solicitado no se ha encontrado.
      </p>
      <div class="empty-action">
        <a href="./." class="btn btn-primary">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="5" y1="12" x2="19" y2="12" /><line x1="5" y1="12" x2="11" y2="18" /><line x1="5" y1="12" x2="11" y2="6" /></svg>
          Volver a inicio
        </a>
      </div>
    </div>
  </div>
</div>
<?php include ('base/footer.php'); ?>