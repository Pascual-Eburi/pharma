<?php $titulo_pagina = ucfirst(basename(__FILE__,".php")); require_once ('base/header.php'); ?>
<!-- Contenido - Page title -->
<div class="page-header d-print-none ">
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                inicio - General - <?php echo $titulo_pagina; ?>
            </div>
            <h2 class="page-title">
                Comunidades Autónomas
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-12 col-md-auto ms-auto d-print-none">
            <div class="btn-list" id="optionsPage">
                <span class="d-sm-inline">
                    <a href="#" class="btn btn-white" data-bs-toggle="offcanvas" data-bs-target="#exportData">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.5 20h-5.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v7.5m-16 -3.5h16m-10 -6v16m4 -1h7m-3 -3l3 3l-3 3" />
                        </svg>
                        Exportar Datos
                    </a>
                </span>

                <span class="d-sm-inline">
                    <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalImportData" aria-label="Importar datos">
                        <span class="icon-pulse icon-demo-size-32 icon-demo-stroke-150"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-database-import" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><ellipse cx="12" cy="6" rx="8" ry="3"></ellipse><path d="M4 6v8m5.009.783c.924.14 1.933.217 2.991.217 4.418 0 8-1.343 8-3v-6"></path><path d="M11.252 20.987c.246.009.496.013.748.013 4.418 0 8-1.343 8-3v-6m-18 7h7m-3-3 3 3-3 3"></path></svg>
                        </span>
                        Importar datos
                    </a>
                </span>
            </div>
        </div>
    </div>
</div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row column-reverse-xxs">
            <!-- FIN TABLA DE DATOS -->
            <div class="col-12 col-sm-9 col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div id="table-default">
                        <table id="tablaCcaa" class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                        <tr>
                            <th>
                                <input class="form-check-input m-0 align-middle selectTodosRegistros select-all-data" type="checkbox" title="Seleccionar todos los registros" aria-label="Seleccionar todos los registros" id="selectTodosRegistros">
                            </th>
                            <th>#</th>
                            <th>Nombre</th>
                            <th class="d-print-none">Opciones</th>
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
                            <th>Nombre</th>
                            <th class="d-print-none">Opciones</th>
                        </tr>
                        </tfoot>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FIN TABLA DE DATOS -->
            <!-- REGISTRO Y ACTUALIZACION -->
            <div class="col-sm-3 col-md-4" id="addUpdateCcaa">
              <div class="card sticky-element">
              <div class="modal-status bg-primary "></div>
                <div class="card-header elementoOculto">
                    <h3 class="card-title"></h3>
                </div>
                <div class="card-body">
                        <div class="row">
                        <div class="col-12 container-panel-aviso panel-info">
                            <div class="card bg-seconday-lt">
                                <div class="card-body">
                                    <h3 class="card-title"> 
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12.01" y2="8" /><polyline points="11 12 12 12 12 16 13 16" /></svg>
                                    </h3>
                                    <p class="text-muted" id="panelAviso"> Rellena este campo para registrar una nueva comunidad autonoma</p>
                                </div>
                            </div>
                        </div>
                    </div>
                  <form id="insertarRegistro">
                    <div class="form-group mb-2 ">
                      <label class="form-label">Nombre </label>
                      <div>
                        <input type="text" class="input form-control" id="nombre" name="nombre" autocomplete="off" placeholder="Nombre de la ccaa.." data-required="true" data-min-length="1" data-max-length="20" data-format="letters+spaces">
                        <small class="form-hint">Solo letras y espacios en blanco...</small>
                      </div>
                        <!-- token seguridad -->
                        <input type="hidden" class="input" name="_token" id="_token" data-format="letters+numbers">
                    </div>
                    <div class="row form-footer">
                        <div class="col-12">
                            <button type="submit" class="btn w-100 btn-primary" id="guardarCambios" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon elementoOculto" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg> 
                                <span>Esperando...</span>
                            </button>
                        </div>
                        <div class="col-12 col-finalizar elementoOculto">
                            <button type="button" class="btn w-100 btn-white" id="cancelarActualizacion" >
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            <span>Finalizar...</span>
                            </button>
                        </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!--  FIN REGISTRO Y ACTUALIZACION -->
        </div>
    </div>
</div>


<?php require_once ('base/footer.php'); ?>
<script src='<?php rutaApp() ?>public/appjs/ccaa.js' type="module" ></script>