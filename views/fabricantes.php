<?php $titulo_pagina = ucfirst(basename(__FILE__,".php")); require_once ('base/header.php'); ?>
<!-- Contenido - Page title -->
<div class="page-header d-print-none ">
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                inicio - inventario - <?php echo $titulo_pagina; ?>
            </div>
            <h2 class="page-title">
                Lista de fabricantes
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
                <span class="d-sm-inline">
                    <a href="#" id="registrarFabricante" class="btn btn-white" data-bs-toggle="offcanvas" data-bs-target="#addUpdate" >
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                        <path d="M16 11h6m-3 -3v6"></path>
                        </svg>
                        Registrar
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
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div id="table-default">
                        <table id="tablaFabricantes" class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                        <tr>
                            <th>
                                <input class="form-check-input m-0 align-middle selectTodosRegistros select-all-data" type="checkbox" title="Seleccionar todos los registros" aria-label="Seleccionar todos los registros" id="selectTodosRegistros">
                            </th>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>pais</th>
                            <th>roll</th>
                            <th>farmacos</th>
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
                            <th>pais</th>
                            <th>roll</th>
                            <th>farmacos</th>
                            <th class="d-print-none">Opciones</th>
                        </tr>
                        </tfoot>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FIN TABLA DE DATOS -->
        </div>
    </div>
</div>

<!--=========== VENTANA PARA REGISTRAR Y ACTUALIZAR  =========================-->
<div class="offcanvas custom-offcanvas offcanvas-end " tabindex="-1" id="addUpdate" aria-labelledby="exportDataLabel">
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    <div class="offcanvas-body">
        <div>
            <div class="row row-header">
                <div class="col-12">
                <h5 class="icon-header text-success">
                    <i class="ti ti-user-plus"></i>
                </h5>
                <h5 class="header text-reset">Registra un nuevo fabricante</h5>
                <p>Rellena el formulario para registrar al fabricante</p>
                </div>
            </div>
            <form enctype="multipart/form-data">
                <div class="row picker-eleccion ">
                    <div class="col-md-3">
                        <a href="#" class="w-100 avatar avatar-upload rounded cargador-archivos" id="cargador-foto">
                            <span class="ti ti-photo icono-foto"></span>
                            <span class="avatar-upload-text">
                                foto
                            </span>
                        </a>
                        <input type="file" class="input custom-file-input" name="foto" id="foto" data-required="false" data-min-length="0" data-format="any" data-allow-format="jpg,png">

                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Nombre</label>
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="7" r="4"></circle><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path></svg>
                            </span>
                            <input type="text" class="input form-control" placeholder="Nombre..." name="nombre" id="nombre" data-required="true" data-min-length="1" data-max-length="50" data-format="text">
                        </div>

                    </div>
                    <div class="col-md-12">
                    <label class="form-label">Email</label>
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                    <polyline points="3 7 12 13 21 7"></polyline>
                                </svg>
                            </span>
                            <input type="text"  class="input form-control" placeholder="Email..." name= "email" id = "email" data-required="true" data-min-length="1" data-max-length="100" data-format="email">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            Clave temporal
                            <span class="form-help" data-bs-toggle="popover" data-bs-placement="top" data-bs-content="Una clave temporal que será cambiada cuando el usuario inicie sesión" data-bs-html="true" data-bs-original-title="" title="">?</span>
                        </label>
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-key" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <circle cx="8" cy="15" r="4"></circle>
                                    <line x1="10.85" y1="12.15" x2="19" y2="4"></line>
                                    <line x1="18" y1="5" x2="20" y2="7"></line>
                                    <line x1="15" y1="8" x2="17" y2="10"></line>
                                </svg>
                            </span>
                            <input type="password" class="input form-control" name='clave' id='clave' placeholder="clave..." data-required="true" data-min-length="1" data-max-length="16" data-format="" >
                        </div>

                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Repetir clave</label>
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-key" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <circle cx="8" cy="15" r="4"></circle>
                                    <line x1="10.85" y1="12.15" x2="19" y2="4"></line>
                                    <line x1="18" y1="5" x2="20" y2="7"></line>
                                    <line x1="15" y1="8" x2="17" y2="10"></line>
                                </svg>
                            </span>
                            <input type="password" class="input form-control" name='clave_repetida' id='clave_repetida' placeholder="clave..." data-required="true" data-min-length="1" data-max-length="16" data-format="">
                        </div>

                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pais</label>
                        <div class="input-icon mb-3">
                                <select type="text" class="input form-select" placeholder="Selecciona un pais" id="id_pais" name="id_pais" data-required="false" data-min-length="0" data-format="numbers">
                                    <option value="0" data-custom-properties="&lt;span class=&quot;flag flag-xs flag-country-xx&quot;&gt;&lt;/span&gt;"> No asignado </option>
                                </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Roll</label>
                        <div class="input-icon mb-3">
                                <select type="text" class="input form-select" placeholder="Selecciona un roll" id="id_roll" name="id_roll" value="" data-required="false" data-min-length="0" data-format="numbers">
                                    <option value="0" data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot;&gt;NA&lt;/span&gt;" > No asignado </option>
                                </select>
                        </div>
                    </div>

                </div>
                <div class="row mt-3">
                    <div class="col-12 col-md-12 ">
                        <div class="btn-list" >
                            <span class="d-sm-inline">
                                <a href="#" class="btn btn-white" data-bs-dismiss="offcanvas" aria-label="Close">
                                    Cerrar
                                </a>
                            </span>

                            <span class="d-sm-inline">
                                <button type="submit" class="btn btn-success" id="guardarCambios">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M5 12l5 5l10 -10"></path>
                                        </svg>
                                    <span>Registrar</span>
                                </button>
                            </span>
                            <span class="d-sm-inline">
                                <a href="#" class="btn btn-white" id="resetFormBtn">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-rotate-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M15 4.55a8 8 0 0 0 -6 14.9m0 -4.45v5h-5"></path>
                                        <line x1="18.37" y1="7.16" x2="18.37" y2="7.17"></line>
                                        <line x1="13" y1="19.94" x2="13" y2="19.95"></line>
                                        <line x1="16.84" y1="18.37" x2="16.84" y2="18.38"></line>
                                        <line x1="19.37" y1="15.1" x2="19.37" y2="15.11"></line>
                                        <line x1="19.94" y1="11" x2="19.94" y2="11.01"></line>
                                    </svg>
                                    Reiniciar
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- FIN EXPORTAR DATOS -->

<?php require_once ('base/footer.php'); ?>
<script src='public/appjs/fabricantes.js' type="module" ></script>