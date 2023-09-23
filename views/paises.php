<?php 
//tittulo de la pagina
$titulo_pagina = ucfirst(basename(__FILE__,".php"));
include ('base/header.php'); 

?>

<!-- Contenido - Page title -->
<div class="page-header d-print-none ">
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                inicio - <?php echo $titulo_pagina; ?>
            </div>
            <h2 class="page-title">
                Lista de <?php echo $titulo_pagina; ?>
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-12 col-md-auto ms-auto d-print-none">
            <div class="btn-list" id="optionsPage">
                <span class="d-none d-sm-inline">
                    <a href="#" class="btn <?php if ($titulo_pagina == 'Dashboard'){ echo 'btn-dark'; }else{ echo 'btn-white';} ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.5 20h-5.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v7.5m-16 -3.5h16m-10 -6v16m4 -1h7m-3 -3l3 3l-3 3" /></svg>
                        Exportar Datos
                    </a>
                </span>
                <!-- boton generar informe -->
                <a href="#" class="btn d-none d-sm-inline-block" id='botonGenerarInforme'>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                    Generar Informe
                </a>
                <a href="#" class="btn btn-primary d-none d-sm-inline-block" id='botonRegistrar'>
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                    Registrar pais
                </a>
                <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-report" aria-label="Create new report">
                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                </a>
            </div>
        </div>
    </div>
</div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
        <div class="card">
              <div class="card-body">
                <div id="table-default">

                <table id="tablaPaises" class="table card-table table-vcenter text-nowrap datatable">
                  <thead>
                  <tr>
                    <th>
                        <input class="form-check-input m-0 align-middle selectTodosRegistros select-all-data" type="checkbox" title="Seleccionar todos los registros" aria-label="Seleccionar todos los registros" id="selectTodosRegistros">
                    </th>
                    <th>#</th>
                    <th>Bandera</th>
                    <th>Nombre</th>
                    <th>Abrevitura</th>
                    <th>Codigo de pais</th>
                    <th>Opciones</th>
                  </tr>
                  </thead>
                  <tbody>

                  </tbody>
                  <tfoot>
                  <tr>
                  <th>
                  <input class="form-check-input m-0 align-middle selectTodosRegistros select-all-data" type="checkbox" title="Seleccionar todos los registros" aria-label="Seleccionar todos los registros">
                </th>
                  <th>#</th>
                  <th>Bandera</th>
                    <th>Nombre</th>
                    <th>Abrevitura</th>
                    <th>Codigo de pais</th>
                    <th>Opciones</th>
                  </tr>
                  </tfoot>
                </table>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>

<!-- ACTUALIZAR INFORMACION DEL PAIS -->
<div class="modal modal-blur fade" id="modalCreateUpdate" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-status bg-primary"></div>
          <div class="modal-header" id="headerModal">
            <div>
                <!-- icono de añadir o bandera del pais -->
                <h5 class="modal-title" id="tituloModal" ></h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
          <div class="row">
                <div class="col-12">
                    <div class="card bg-seconday-lt">
                    <div class="card-body">
                        <h3 class="card-title"> 
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12.01" y2="8" /><polyline points="11 12 12 12 12 16 13 16" /></svg>
                        </h3>
                        <p class="text-muted" id="panelAviso"> </p>
                    </div>
                    </div>
                </div>
            </div>
            <form id="" action="paises" method="POST">
                <div class="row">
                    <div class="col-2">
                        <a href="#" class="avatar avatar-upload rounded">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <span class="flag flag-sm flag-country-xx" id="bandera"></span>
                        <span class="avatar-upload-text">bandera</span>
                        </a>
                    </div>
                    <div class="col-10">            
                        <div class="form-floating mb-3">
                            <input type="text" class="input form-control" id="nombre" name="nombre" autocomplete="off" placeholder="Nombre del pais.." data-required="true" data-min-length="1" data-max-length="30" data-format="letters+spaces">
                            <label for="floating-input">Nombre <sup><b>*</b></sup> </label>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-12 col-sm-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="input form-control" id="abreviatura" name="abreviatura"  autocomplete="off" placeholder="Abreviatura del pais.." data-required="false" data-min-length="0" data-max-length="3" data-format="letters">
                            <label for="floating-input">Abrevitura</label>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="input form-control" id="cod_pais" name="codigo" autocomplete="off" placeholder="Codigo del pais.." data-required="false" data-min-length="0" data-max-length="2" data-format="letters">
                            <label for="floating-input"> Codigo de Pais </label>
                        </div>
                    </div>
                </div>
          </div>
            <div class="modal-footer" id='divBotonesFormulario'>
                <button type="button" class="btn" data-bs-dismiss="modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>
                    <span>Cancelar</span>
                </button>
                <button type="submit" class="btn btn-primary" id="guardarCambios">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg> 
                    <span>Sin cambios</span>
                </button>
                <button type="button" class="btn btn-delete" data-bs-dismiss="modal" id="botonEliminar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                    <span>Eliminar</span>
                </button>
            </div>
          </form>
        </div>
      </div>
</div>
<!-- FIN ACTUALIZAR INFORMACION DEL PAIS -->

<div class="modal modal-blur fade" id="modal-success" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-success"></div>
            <div class="modal-body text-center py-4">
            <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg>
            <h3>Payment succedeed</h3>
            <div class="text-muted">Your payment of $290 has been successfully submitted. Your invoice has been sent to support@tabler.io.</div>
            </div>
            <div class="modal-footer">
            <div class="w-100">
                <div class="row">
                <div class="col"><a href="#" class="btn w-100" data-bs-dismiss="modal">
                    Go to dashboard
                    </a></div>
                <div class="col"><a href="#" class="btn btn-success w-100" data-bs-dismiss="modal">
                    View invoice
                    </a></div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- ELIMINAR REGISTROS -->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
            <!-- Download SVG icon from http://tabler-icons.io/i/alert-triangle -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
            <h3>¿ Estás seguro de esto ?</h3>
            <div class="text-muted" id="smsAdvertencia"> </div>
            </div>
            <div class="modal-footer" id="footerModalEliminar">
            <div class="w-100">
                <div class="row">
                <div class="col-6">
                    <a href="#" class="btn w-100" data-bs-dismiss="modal">
                    Cancelar
                    </a></div>
                <div class="col-6">
                    <a href="#" class="btn btn-danger w-100" id='confirmarEliminacion' title='Borrar este registro' data-bs-toggle="tooltip">
                        Si, borrar registro
                    </a>
                </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<!-- FIN ELIMINAR REGISTROS -->


<a class="btn elementoOculto" data-bs-toggle="offcanvas" href="#offcanvasEnd" role="button" aria-controls="offcanvasEnd">
                  Toggle end offcanvas
 </a>
<div class="offcanvas offcanvas-end " tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
    <div class="offcanvas-header">
    <h2 class="offcanvas-title" id="offcanvasEndLabel">End offcanvas</h2>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    
    </div>
    <div class="offcanvas-body">
        <div>
            <div class="row">
                <div class="col-12">
                    <div class="card bg-info-lt">
                    <div class="card-body">
                        <h3 class="card-title"> 
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12.01" y2="8" /><polyline points="11 12 12 12 12 16 13 16" /></svg>
                        </h3>
                        <p class="text-muted">Esta es la informacion del pais seleccinado</p>
                    </div>
                    </div>
                </div>
            </div>
            <form class"row">
                <div class="col-12">            
                    <div class="form-floating mb-3">
                        <input type="email" class="input form-control" id="floating-input" value="name@example.com" autocomplete="off">
                        <label for="floating-input">Email address</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating mb-3">
                        <input type="email" class=" input form-control" id="floating-inut" value="name@example.com" autocomplete="off">
                        <label for="floating-input">Email address</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floating-inp" value="name@example.com" autocomplete="off">
                        <label for="floating-input">Email address</label>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- fin formulario -->
<?php include ('base/footer.php'); ?>
<script src='<?php rutaApp() ?>public/appjs/paises.js' type="module" ></script>