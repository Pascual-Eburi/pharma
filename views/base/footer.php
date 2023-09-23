 
    <footer class="footer footer-transparent d-print-none">
      <div class="container-xl">
        <div class="row text-center align-items-center flex-row-reverse">
          <div class="col-lg-auto ms-lg-auto">
            <ul class="list-inline list-inline-dots mb-0">
              <li class="list-inline-item"><a href="./docs/index.html" class="link-secondary">Documentation</a></li>
              <li class="list-inline-item"><a href="./license.html" class="link-secondary">License</a></li>
              <li class="list-inline-item"><a href="https://github.com/tabler/tabler" target="_blank" class="link-secondary" rel="noopener">Source code</a></li>
              <li class="list-inline-item">
                <a href="https://github.com/sponsors/codecalm" target="_blank" class="link-secondary" rel="noopener">
                  <!-- Download SVG icon from http://tabler-icons.io/i/heart -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon text-pink icon-filled icon-inline" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19.5 13.572l-7.5 7.428l-7.5 -7.428m0 0a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" /></svg>
                  Sponsor
                </a>
              </li>
            </ul>
          </div>
          <div class="col-12 col-lg-auto mt-3 mt-lg-0">
            <ul class="list-inline list-inline-dots mb-0">
              <li class="list-inline-item">
                Copyright &copy; 2022
                <a href="." class="link-secondary">Pascual Eburi</a>.
                All rights reserved.
              </li>
              <li class="list-inline-item">
                <a href="./changelog.html" class="link-secondary" rel="noopener">
                  v1.0.0-beta10
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>
  </div>
</div>

<?php $pg_no_autorizadas = array('Error404', 'Login', 'Dashboard'); ?>
<?php if ( !(in_array($titulo_pagina, $pg_no_autorizadas)) ): #incluiir las ventanas siguientes: ?>
<!-- ============  ELIMINAR REGISTROS =========================-->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
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
                            <button type='button' class="btn btn-danger w-100" id='confirmarEliminacion' title='Borrar este registro' data-bs-toggle="tooltip">
                                Si, borrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIN ELIMINAR REGISTROS -->

<!-- =============== VENTANA PARA IMPORTAR DATOS ======================-->
<div class="modal modal-blur fade" id="modalImportData" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-status bg-success"></div>
            <div class="modal-header" id="headerModal">
                <div>
                    <!-- icono de añadir o bandera del pais -->
                    <h5 class="modal-title" id="tituloModal" > Importar datos desde un archivo </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" id="input-file-container">
                            <a href="#" class="avatar avatar-upload rounded" id="cargador-archivo">
                                <span class="ti ti-file-search" id="icono-documento"></span>
                                <span class="avatar-upload-text">Seleccionar Archivo csv o excel</span>
                            </a>
                            <input type="file" class="custom-file-input" name="archivo" id="archivo" data-allow-format="xlsx,csv" multiple>
                        </div>
                        <div class="col-12" id="previsulizarDatos">            

                        </div>
                    </div>
                </div>
                <div class="modal-footer" id='divBotonesFormulario'>
                    <button type="button" class="btn" data-bs-dismiss="modal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>
                        <span>Cancelar</span>
                    </button>
                    <button type="button" class="btn btn-success" id="confirmarImportacion">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-upload" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                        <path d="M12 11v6"></path>
                        <path d="M9.5 13.5l2.5 -2.5l2.5 2.5"></path>
                        </svg>
                        <span>Importar datos</span>
                    </button>
                </div>
            </form>
        </div>
      </div>
</div>
<!-- IMPORTAR DATOS -->

<!--========================== VENTANA PARA EXPORTAR DATOS =========================-->
<div class="offcanvas custom-offcanvas offcanvas-end" tabindex="-1" id="exportData" aria-labelledby="exportDataLabel">
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    <div class="offcanvas-body">
        <div>
            <div class="row row-header">
                <div class="col-12">
                <h5 class="icon-header">
                    <i class="ti ti-question-mark"></i>
                </h5>
                <h5 class="header">¿cómo queres exportar los datos ?</h5>
                    <p>Elije el tipo de documento al que quieres que se exporten los registros, puedes elejir cuantos quieras</p>
                </div>
            </div>
            <div class="row picker-eleccion" id="eleccionExport">
                <div class="col-12">
                    <button type="button" class="btn eleccion" data-export-format="xlsx" data-loading data-loading-text="Generando archivo excel">
                        <img src="public/icons/excel.png" class="icono"></img>
                        <span>Exportar a documento excel</span>
                        
                    </button>
                </div>
                <div class="col-12">
                    <button type="button" class="btn eleccion" data-export-format="csv" data-loading data-loading-text="Generando archivo csv">
                        <img src="public/icons/csv.png" class="icono"></img>
                        <span>Exportar a documento csv</span>
                    </button>
                </div>
                <div class="col-12">
                    <button type="button" class="btn eleccion" data-export-format="pdf" data-loading data-loading-text="Generando archivo pdf">
                        <img src="public/icons/pdf.png" class="icono"></img>
                        <span>  Exportar a documento pdf </span>
                    </button>
                </div>
                <div class="col-12">
                    <button type="button" class="btn eleccion" data-export-format="print" data-loading data-loading-text="Imprimiendo registros">
                        <img src="public/icons/print.png" class="icono"></img>
                        <span>Imprimir todos los registros</span>
                    </button>

                </div>

            </div>
        </div>
    </div>
</div>
<!-- FIN EXPORTAR DATOS -->

<?php endif; ?>


<div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">New report</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" class="form-control" name="example-text-input" placeholder="Your report name">
        </div>
        <label class="form-label">Report type</label>
        <div class="form-selectgroup-boxes row mb-3">
          <div class="col-lg-6">
            <label class="form-selectgroup-item">
              <input type="radio" name="report-type" value="1" class="form-selectgroup-input" checked>
              <span class="form-selectgroup-label d-flex align-items-center p-3">
                <span class="me-3">
                  <span class="form-selectgroup-check"></span>
                </span>
                <span class="form-selectgroup-label-content">
                  <span class="form-selectgroup-title strong mb-1">Simple</span>
                  <span class="d-block text-muted">Provide only basic data needed for the report</span>
                </span>
              </span>
            </label>
          </div>
          <div class="col-lg-6">
            <label class="form-selectgroup-item">
              <input type="radio" name="report-type" value="1" class="form-selectgroup-input">
              <span class="form-selectgroup-label d-flex align-items-center p-3">
                <span class="me-3">
                  <span class="form-selectgroup-check"></span>
                </span>
                <span class="form-selectgroup-label-content">
                  <span class="form-selectgroup-title strong mb-1">Advanced</span>
                  <span class="d-block text-muted">Insert charts and additional advanced analyses to be inserted in the report</span>
                </span>
              </span>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-8">
            <div class="mb-3">
              <label class="form-label">Report url</label>
              <div class="input-group input-group-flat">
                <span class="input-group-text">
                  https://tabler.io/reports/
                </span>
                <input type="text" class="form-control ps-0"  value="report-01" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="mb-3">
              <label class="form-label">Visibility</label>
              <select class="form-select">
                <option value="1" selected>Private</option>
                <option value="2">Public</option>
                <option value="3">Hidden</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-6">
            <div class="mb-3">
              <label class="form-label">Client name</label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-lg-6">
            <div class="mb-3">
              <label class="form-label">Reporting period</label>
              <input type="date" class="form-control">
            </div>
          </div>
          <div class="col-lg-12">
            <div>
              <label class="form-label">Additional information</label>
              <textarea class="form-control" rows="3"></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
          Cancel
        </a>
        <a href="#" class="btn btn-primary ms-auto" data-bs-dismiss="modal">
          <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
          Create new report
        </a>
      </div>
    </div>
  </div>
</div>
<!-- boton ir a inicio o principio  

<i class="uil uil-angle-down icono-final"></i> <i class="uil uil-angle-up icono-inicio"></i>

-->
  <a class="ir-a" id='' title="Ir al principio de la página" href="#"><i class="ti " id="icono-indicador"></i> </a>



    <!-- jQuery -->
    <script src="<?php rutaApp();?>public/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php rutaApp();?>public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
   
    
    <?php 
      //paginas que no utilizan tablas de datos
      $paginas = array('Dashboard', 'Login', 'Error404');
    ?>

    <?php if ( !in_array($titulo_pagina, $paginas)): #incluir plugins para tablas de datos ?>
      <!-- DataTables  & Plugins:  -->
        <script src="<?php rutaApp(); ?> public/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php rutaApp(); ?> public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="<?php rutaApp(); ?> public/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="<?php rutaApp(); ?> public/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="<?php rutaApp(); ?> public/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
        <script src="<?php rutaApp(); ?> public/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
        <script src="<?php rutaApp(); ?> public/plugins/jszip/jszip.min.js"></script>
        <script src="<?php rutaApp(); ?> public/plugins/pdfmake/pdfmake.min.js"></script>
        <script src="<?php rutaApp(); ?> public/plugins/pdfmake/vfs_fonts.js"></script>
        <script src="<?php rutaApp(); ?> public/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
        <script src="<?php rutaApp(); ?> public/plugins/datatables-buttons/js/buttons.print.min.js"></script>
        <script src="<?php rutaApp(); ?> public/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
      <!-- END -- DataTables  & Plugins:  -->
    <?php endif;  ?>
 

    <?php if ($titulo_pagina == 'Dashboard'): ?>
      <!-- Libs JS plugins para tabler dashboard -->
       <!-- librerias para el dashborad --> 
      <script src="<?php rutaApp() ?>public/plugins/tabler/libs/apexcharts/dist/apexcharts.min.js" defer></script>
      <script src="<?php rutaApp() ?>public/plugins/tabler/libs/jsvectormap/dist/js/jsvectormap.min.js" defer></script>
      <script src="<?php rutaApp() ?>public/plugins/tabler/libs/jsvectormap/dist/maps/world.js" defer></script>
      <script src="<?php rutaApp() ?>public/plugins/tabler/libs/jsvectormap/dist/maps/world-merc.js" defer></script>

    <?php endif; ?>
 
    <script src="public/plugins/litepicker/dist/litepicker.js" defer></script>
    <script src="public/plugins/tom-select/dist/js/tom-select.base.min.js" defer></script>

    <!-- Tabler Core -->
    <script src="<?php rutaApp(); ?> public/plugins/tabler/js/tabler.min.js" defer></script>
    <script src="<?php rutaApp(); ?> public/plugins/tabler/js/demo.min.js" defer></script>

    <!-- axios cdn 
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    -->
     <!-- axios local -->
    <script src="public/plugins/axios/axios.min.js"></script>
    <?php if (isset($_SESSION['id_usuario'])) : ?>
    <!-- common js para paginas -->
    <script src="public/appjs/main.js" type="module"></script>
    <?php endif; ?>
    <script src="public/appjs/index.js"></script>
</body>
</html>