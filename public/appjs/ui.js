// modulos necesarios
import { contieneSoloNumeros, mostrarNotificaciones, sanitizarInputs, validarInput,compararObetosMap, contieneSoloLetrasEspacios, ordenarArray, capitalizarPrimeraLetra } from "./modules.js";

// ui de la app con la que interractua el usuario

export class UI {

  #tabla_datos; // propiedad privada: tabla de datos
  #url; // url donde hacer las peticiones
  datosRegistro; // datos del registro seleccionado o con el que se esta trabajando
  datosRegistroEditados; // datos del registro editados
  formulario; //formulario crear y editar
  registros_seleccinados; // para cuando se inicie una seleccion multiple
  selectsAvanzados; // guardará todos los select avanzados de la app 
  // guarda el input y el archivo tipo img de avatars para detectar cuando actualizar o no el avatar
  registradorArchivos;
  /**
   * constructor
   * @param {object: tabla de datos html } tablaDatos 
   * @param {string : url donde la ui hará las peticiones } url 
   */
    constructor(){
        this.datosRegistro = new Map();
        this.datosRegistroEditados = new Map();
        this.registros_seleccinados = [];
        this.selectsAvanzados = [];
        this.registradorArchivos = new Map();
        //this.url = url; // url donde hacer las peticiones 
    }
    
    /**------------------------------------------
     * getter and setter para la tabla html de datos
     ------------------------------------------------*/
    set tablaDatos(tabla){
      if(typeof tabla == 'object'){
          this.#tabla_datos = tabla;
      }else{
        mostrarNotificaciones({
          tipo:'aviso',
          titulo: '¡Error de parametro!',
          mensaje:'Para establecer la tabla de datos es necesario que sea un objeto.'
        });
      }
      
    }
    get tablaDatos(){return this.#tabla_datos;}

    /**------------------------------------------
     * getter and setter para la url de peticiones
     ------------------------------------------------*/
    set urlPeticiones(url){ this.#url = sanitizarInputs(url);}
    get urlPeticiones(){return this.#url;}
    /**---------------------------------------------
      getter and setter datos o info del registro
    -----------------------------------------------*/
    set infoRegistro(info){
      (info instanceof Object )? this.datosRegistro = new Map(info):console.warn('Error, no se puede inicializar la información del registro') 
    }

    get infoRegistro(){return this.datosRegistro; }

    /**------------------------------------------
     *  validaccion de formulario
     ------------------------------------------------*/
    validarFormulario(){return validarInput(this.formulario.find('.input:not(div)'));}

    /*======================================
          DOM LISTENERS
    =======================================*/

    /**
     * Inicializa la seleccion multiple de registro en la tabla de datos de la UI
     * @param {Object} $target elemento o checkbox que dispara la funcion
     */
    inicializarSeleccionMultiple($target = null){
      if(!$target) {return false};
      // iniciar ui para seleccion multiple
      this.initSeleccionMultiple = true;
      const SELF = this;

      let $elemento = $target;
      // desabilitar inputs de la tabla de datos
      $('input[type=search]').attr('disabled', true);
      $('select.custom-select').attr('disabled', true);
      // ul paginacion
      let paginacion = document.querySelector('.dataTables_paginate ul');
      let posicion_paginacion = paginacion.offsetTop; // distancia paginacion

      //si el disparador es checkbox seleccionar todos los registros
      if ($elemento.hasClass('select-all-data')){
          // mostrar todos los registros
          if (this.tablaDatos.page.len() > -1 ) {
              this.tablaDatos.page.len( -1 ).draw();
          }
          if( $elemento.is(':checked')){
              //seleccionar todos los registros 
              $('.select-all-data').attr('checked', true).prop('checked', true);
              $.each($('.single-select'), function(index, $checkbox){
                  $(this).attr('checked', true);
                  $(this).prop('checked', true);
                  SELF.registros_seleccinados.push( $(this).attr('data-id'));    
              })
          }else{
              $('.select-all-data').attr('checked', false).prop('checked', false);
              $('.single-select').attr('checked', false).prop('checked', false);
              this.registros_seleccinados = []; 
          }
          //console.log($registros_seleccinados)
      }

      // seleccion no multiple
      if($elemento.hasClass('single-select')){
        // ir mostrando o cargando registros a medida que se hace scroll
        window.addEventListener('scroll', function(){
          if(!SELF.initSeleccionMultiple) return 
          
          let scroll  = window.scrollY ;
          const largo_documento = document.body.clientHeight;
          let distancia_paginacion = (largo_documento  - scroll) - posicion_paginacion;
          if(distancia_paginacion < 10 ){
            SELF.tablaDatos.page.len( SELF.tablaDatos.page.len() + 10 ).draw();
          }


        })



        // indice del registro
        let $index = this.registros_seleccinados.indexOf($elemento.attr('data-id'));
        if( $elemento.is(':checked') ){
            // añadir registro a lista de registros ya que no exite
            if($index < 0){
                this.registros_seleccinados.push($elemento.attr('data-id'));
            }
            //checkbox seleccionar todo los registros checkeado o no...
            if(this.registros_seleccinados.length == this.tablaDatos.page.info().recordsTotal){
                // ya se ha seleccionado todos los registros
                $('.select-all-data').attr('checked', true).prop('checked', true);
            }else{
                // no se ha seleccionado todos registros aun
                $('.select-all-data').attr('checked', false).prop('checked', false);
            }

        }else{
            // quitar de la lista si se deselecciona el checkbox del registro
            if($index >= 0){
                this.registros_seleccinados.splice($index, 1);
            }

            //checkbox seleccionar todo los registros checkeado o no...
            if(this.registros_seleccinados.length == this.tablaDatos.page.info().recordsTotal){
                $('.select-all-data').attr('checked', true).prop('checked', true);
            }else{
                $('.select-all-data').attr('checked', false).prop('checked', false);
            }
        } 
  
      }



      // mmensaje a mostrar en el panel indicando los registros seleccionados  
      if ( $('#data-selected-panel').length <= 0 ){
          $('.table').append(
              `<div class="data-selected-panel" id="data-selected-panel">
                <div>
                  <span id="rows-selected">
                    ${this.generarMensajePanelSeleccion()} 
                    </span>
                  </div>
                  <div> 
                    <span class="action action-delete" id="action-delete">Eliminar</span> 
                    <span class="action action-cancel" id="action-cancel">Cancelar</span>
                  </div>
              </div>`
          );
          setTimeout(function(){$('#data-selected-panel').addClass('show');}, 10);
      }else {
          $('#rows-selected').text(this.generarMensajePanelSeleccion()); 
      }


    
    } // fin inicaializador de seleccion multiple

    /**---------------------------------------------------------------------
     * Aborta o finaliza la selecccion de registros para eliminar
     ----------------------------------------------------------------------*/
    cancelarSeleccion(){
      // cancelar seleccion
      this.initSeleccionMultiple = false;
      removeEventListener('scroll', window);
      $('.select-all-data').attr('checked', false).prop('checked', false);
      $('.single-select').attr('checked', false).prop('checked', false);
      this.registros_seleccinados = []; 
      let len_tabla = this.tablaDatos.page.len();
      let newLen = len_tabla - (len_tabla - 10);
      this.tablaDatos.page.len( newLen ).draw(); // mostrar todos los registros
      $('#data-selected-panel').removeClass('show').addClass('hide');
      setTimeout(function (){
          $('#data-selected-panel').remove()
      }, 600);
  
      // habilitar inputs tabla de datos
      $('input[type=search]').attr('disabled', false);
      $('select.custom-select').attr('disabled', false);
      // scroll al principio
      window.scrollTo({top: 0,left: 100,behavior: 'smooth'});
    }
    /**==========================================================================
     * genera el mensaje a mostrar en el panel de seleccionar
     * @returns @string : mensaje a mostrar en el panel de seleccion
     ===========================================================================*/
    generarMensajePanelSeleccion(){
      if (this.tablaDatos.page.info().recordsTotal == this.registros_seleccinados.length) {
        $('#action-delete').removeClass('elementoOculto');
        return 'Todos los registros están seleccionados';
      }
      if (this.registros_seleccinados.length == 1){
        $('#action-delete').removeClass('elementoOculto');
          return `1 Registro seleccionado de un total de ${this.tablaDatos.page.info().recordsTotal}`; 
      }
      if(this.registros_seleccinados.length > 1){
        $('#action-delete').removeClass('elementoOculto');
          return `${this.registros_seleccinados.length} Registros seleccionados de un total de ${this.tablaDatos.page.info().recordsTotal}`;
      }else{
          $('#action-delete').addClass('elementoOculto');
          return `Ningún registro seleccionado de un total de ${this.tablaDatos.page.info().recordsTotal}`; 
      }
      
    }

    /** =========================================================
     *  listener para los inputs : para cuando se escribe en los imputs
     * ===========================================================*/
    inputsEventListerners(){
      let $formulario = this.formulario; // formulario
      let $campos = this.formulario.find('.input:not(div)'); // campos del form
    
      let SELF = this; //bind 
      $.each( $campos , function($indice, $campo){        
        // on BLUR INPUTS : validar 
        /*$($campo).on('blur', function(){
            let $validacion = validarInput($campos);
            if ($validacion.exito == false){ // hay errores
              // mostrar mensaje de error solo cuando el input que ha disparado el evento blur tiene error
              if($validacion.validaciones[$(this).attr('name')].valido == false){
                mostrarNotificaciones({
                  tipo: 'aviso',
                  titulo: '¡ Hay errores que corregir !',
                  mensaje: $validacion.mensajes
                });
              }
               
            }else{
                if ( $('#notificacionApp').length > 0){$('#notificacionApp').remove();}
            }
        });*/
        
        // KEY UP INPUTS
        if($campo.localName == 'input' && $campo.nodeName == 'INPUT'){
          $($campo).off('keyup').on('keyup', (event) => {
            event.stopPropagation();
            
              //let $valor_campo = $(this).val().trim();
              let $validacion = validarInput(SELF.formulario.find('.input:not(div)'));
              
              //si hay errores
              if ($validacion.exito == false){
                  // marcar cada input si valido o no segun resultado de su validacion
                  for (let i = 0; i < Object.keys($validacion.validaciones).length ; i++ ){
                      let campo = Object.keys($validacion.validaciones)[i];
                      if ($validacion.validaciones[campo].valido == false){ // validacion no ok
                          $('input[name='+campo+']').addClass('is-invalid').removeClass('is-valid');
                      }else{
                          $('input[name='+campo+']').removeClass('is-invalid').addClass('is-valid'); // validacion ok
                      }
                  } // for: find errors
  
                if ( $($formulario).attr('id') == 'insertarRegistro' ){ //  
                        $('#guardarCambios svg').addClass('elementoOculto');
                        $('#guardarCambios').attr('disabled', true);
                        $("#guardarCambios span").text('Hay errores');
                    
                } // fin if formulario registrar pais
  
  
              }else { // todo ok; no errores en la validacion
                  $($campos).removeClass('is-invalid').addClass('is-valid');
                  // si el formulario es para registrar pais id = insertarRegistro
                  if ( $($formulario).attr('id') == 'insertarRegistro' ){ // 
                      $('#guardarCambios svg').removeClass('elementoOculto');
                      $('#guardarCambios').attr('disabled', false);
                      $("#guardarCambios span").text('Guardar...'); 
                                          
                  } 
              } // if else validacion == false
  
              // si el formulario es para actualizar info de pais id= editarPais
              if ( $($formulario).attr('id') == 'editarRegistro' ){
                  //por cada input  guardar su id y valor en el obj map datosResgistroesEditados cuando se escribe
                  // excluir inputs tipo file
                  $.each(SELF.formulario.find('.input:not(div)'), function(indice, $elemento ) {
                    if($($elemento).attr('type') === 'file'){
                      if( !$($elemento).attr('data-foto') ){
                        SELF.datosRegistroEditados.set( $($elemento).attr('id') , '' );
                      }else{
                        SELF.datosRegistroEditados.set( $($elemento).attr('id') , $($elemento).attr('data-foto') );
                      }

                    }else{
                      SELF.datosRegistroEditados.set( $($elemento).attr('id') , $($elemento).val() );
                    }
                  });
                  
                  // comparar si los dos objetos maps tienen los mismos valores, para deteminar cuando hay cambios que guardar y cuando no.
                  //compararObjetosMap => true : ===> los datos siguen siendo los mismos

                  if( compararObetosMap( SELF.datosRegistroEditados, SELF.infoRegistro ) ||  $validacion.exito == false ){
                      $('#guardarCambios').attr('disabled', true);
                      $('#guardarCambios span').text('Sin cambios...');
                      $("#guardarCambios svg").addClass('elementoOculto');
  
                  }else{ //hay cambios, hay datos que han cambiado
                      // boton guardar desab si el nombre, cod,abv son validos
                      if ($validacion.exito == true ){
                          $('#guardarCambios').attr('disabled', false);
                          $("#guardarCambios svg").removeClass('elementoOculto');
                          $('#guardarCambios span').text('Guardar');
                      }
                  }
              } // fin if form editarPais
              
  
          });    
        }
    
        if($campo.nodeName == 'SELECT' || $($campo).attr('type') == 'file'){
          //console.log('select')
          $($campo).off('change').on('change', (event) => {
           // event.stopPropagation();
          
              //let $valor_campo = $(this).val().trim();
              let $validacion = validarInput(SELF.formulario.find('.input:not(div)'));
             // $('.input[name="id_ca"]').addClass('is-invalid')
              //si hay errores
              if ($validacion.exito == false){
                  // marcar cada input si valido o no segun resultado de su validacion
                  for (let i = 0; i < Object.keys($validacion.validaciones).length ; i++ ){
                      let campo = Object.keys($validacion.validaciones)[i];
                      if ($validacion.validaciones[campo].valido == false){ // validacion no ok
                          $('.input[name='+campo+']').addClass('is-invalid').removeClass('is-valid');
                      }else{
                          $('.input[name='+campo+']').removeClass('is-invalid').addClass('is-valid'); // validacion ok
                      }
                  } // for: find errors
  
                if ( $($formulario).attr('id') == 'insertarRegistro' ){ //  
                        $('#guardarCambios svg').addClass('elementoOculto');
                        $('#guardarCambios').attr('disabled', true);
                        $("#guardarCambios span").text('Hay errores');
                    
                } // fin if formulario registrar pais
  
  
              }else { // todo ok; no errores en la validacion
                  $($campos).removeClass('is-invalid').addClass('is-valid');
                  // si el formulario es para registrar pais id = insertarRegistro
                  if ( $($formulario).attr('id') == 'insertarRegistro' ){ // 
                      $('#guardarCambios svg').removeClass('elementoOculto');
                      $('#guardarCambios').attr('disabled', false);
                      $("#guardarCambios span").text('Guardar...'); 
                                          
                  } 
              } // if else validacion == false
  
              // si el formulario es para actualizar info de pais id= editarPais
              if ( $($formulario).attr('id') == 'editarRegistro' ){
                  //por cada input  guardar su id y valor en el obj map datosResgistroesEditados cuando se escribe
                  //SELF.formulario.find('.input:not(div, [type="file"])')
                  $.each(SELF.formulario.find('.input:not(div)'), function(indice, $elemento ) {
                    if($($elemento).attr('type') == 'file'){
                      setTimeout( function(){
                          if( !$($elemento).attr('data-foto') ){
                            SELF.datosRegistroEditados.set( $($elemento).attr('id') , '' );
                          }else{
                            SELF.datosRegistroEditados.set( $($elemento).attr('id') , $($elemento).attr('data-foto') );
                          }
                      },200) 
                    }else{
                      SELF.datosRegistroEditados.set( $($elemento).attr('id') , $($elemento).val() );
                    }
                      //SELF.datosRegistroEditados.set( $($elemento).attr('id') , $($elemento).val() );
                  });
                  
                  // comparar si los dos objetos maps tienen los mismos valores, para deteminar cuando hay cambios que guardar y cuando no.
                  //compararObjetosMap => true : ===> los datos siguen siendo los mismos
                  if( compararObetosMap( SELF.datosRegistroEditados, SELF.infoRegistro ) ||  $validacion.exito == false ){
                      $('#guardarCambios').attr('disabled', true);
                      $('#guardarCambios span').text('Sin cambios...');
                      $("#guardarCambios svg").addClass('elementoOculto');
  
                  }else{ //hay cambios, hay datos que han cambiado
                      // boton guardar desab si el nombre, cod,abv son validos
                      if ($validacion.exito == true ){
                          $('#guardarCambios').attr('disabled', false);
                          $("#guardarCambios svg").removeClass('elementoOculto');
                          $('#guardarCambios span').text('Guardar...');
                      }
                  }
              } // fin if form editarPais
  
  
          }); 
        }
      });
    } // listenerINputst

    /**
     * Esta a la escucha de guando se cierra la ventana de eliminar registros
     */
    modalEliminarEventsListeners(){
      const SELF = this;
      $('#modalEliminar').on('hide.bs.modal', function(event){
        $('#confirmarEliminacion').attr('disabled', false);
        $('#confirmarEliminacion').text('Si, borrar');
        // cancelar la seleccion de registros para eliminar si estuviera activa
        if($('#data-selected-panel').length > 0 ){ 
          SELF.cancelarSeleccion(); }
      });
    }

    /**
     * Esta a la escucha de los eventos en las ventanas de importacion y exoportacion
     */
    modalExportImportDataEventsListeners(){
      const SELF = this; // bind al mismo objeto
      // modal importar datos
      $('#modalImportData').on('show.bs.modal', function(){
        SELF.reiniciarCargadorArchivos();
      });

      $("#modalImportData").on('hide.bs.modal', function(event){
          if($(this).hasClass('blocked')){
              mostrarNotificaciones({
                  tipo: 'info',
                  titulo: 'Espera un momento',
                  mensaje: 'Hay un proceso ejecutandose, espere un momento...'
              })    
              return false;
          }
      });

      $("#cargador-archivo").on('click', function(){
          SELF.cargarArchivoImportacion();
          //let resultado = await mensajesCargaArchivo()
      });

      // al ocultar pandel de eleccion de formato de doc a exportar
      $("#exportData").on('hide.bs.offcanvas', function(){
          let buttons_loading = $(this).find('button.loading');
          if (buttons_loading.length > 0){
              $.each(buttons_loading, function(i, button){
                  SELF.mostrarLoaderBotones($(this));
              });
          }
      });

      //click boton exportar
      $('#eleccionExport').off('click').on('click', '.eleccion', function(){
          SELF.exportarDatos($(this));
      });
 
    }

    /**
     * Muestra u oculta loader de un botton
     * @param {object} $boton 
     */
    mostrarLoaderBotones($boton){
      if($boton && $($boton).length > 0){
         let text_boton = $($boton).attr('data-loading-text') || 'procesando';
         $($boton).toggleClass('loading');

          if($boton.hasClass('loading')){
            $($boton).attr('disabled', true).prop('disabled', 'disabled');
              $boton.children(':not(.loader-button)').addClass('elementoOculto');
              if( $boton.find('.loader-button').length <= 0){
                  $boton.append(`
                  <span class="d-flex loader-button">
                      <span class="spinner-border text-success" role="status"></span>
                      <span class="etiqueta-archivo text-success"> ${text_boton} <span class="animated-dots"></span> </span>
                  </span>
              `);
              }
  
          }else{
            $($boton).attr('disabled', false).prop('disabled', false);
              $boton.find('.loader-button').remove();
              $boton.children().removeClass('elementoOculto'); 
          }
      }else{
        console.warn('No se ha recibido boton')
      }
    
      //$('#confirmarImportacion')
    }


    /**-------------------------------------------------------------------------
     * genera token de seguridad
     * @returns @String token de seguridad
     ----------------------------------------------*/
    async generarToken(){
      const peticion = await axios.get('generarToken');
      const token = await peticion.data; // data
      return (token) ? token : '';
    }



    /**-----------------------------------------------------------------------------
     * obtiene la informacion de un registro o todos los registros
     * @param {number} id_registro : id del registro, tiene que ser numerio: 0 => todos los registros
     * @returns object {exito: true| false, ... }
     -------------------------------------------------------------------------------*/
    async  traerResgistros(id_registro = 0 ) {
      
      // validar id_registro: tiene que ser numerico
        if (id_registro && contieneSoloNumeros(id_registro)){
          try {
            // parametros a mandar al backend 
            let parametros = new URLSearchParams();
            parametros.append('accion', 'traerInfo'); //accion
            parametros.append('registro', id_registro); // id, clave del registro a traer
            // peticion => url = paises 
            const peticion = await axios.post(this.urlPeticiones, parametros);
            const datos = await peticion.data; // data
            //console.log(datos)
            if (datos.exito == true ) {
              return {exito: true, datos: datos.respuesta[0]}; // exito hay data: data en array pos 0
            }else{
              return {exito: false, titulo: `Operación no exitosa`,  mensaje: datos.respuesta}; // no existe el registro
            }
  
          } catch (error) { // errores en la peticion
            let titulo = (error.response.status)? `Error ${error.response.status}`: 'Error del servidor';
            let mensaje = (error.response.statusText) ? `Error en la petición al servidor,>> ${error.response.statusText}`: 'Ha fallado un error en la petición al servidor.';

            return {exito: false, titulo: titulo,  mensaje: mensaje};
          } // fin try catch
        }else{
           return {exito: false, titulo: `ID de Registro Invalido`,  mensaje: ` No se puede traer la información de este registro, porque el id no es numérico`}; // no existe el registro
        }

    } // fin async traer registros

    /**
     * Traer los datos necesarios para cargar en un select
     * @param {string} url url de donde se va a traer los datos o hacer la peticion
     * @param {number} id  id de registro en particular
     * @returns @bool false si no hay datos o algun error 
     * @returns @object si todo ha ido bien
     */
    async traerDatosSelect(url = null, id = 0){
      try {
        const parametros = new URLSearchParams();
        parametros.append('accion', 'traerInfo'); //accion
        parametros.append('registro', id); // todos los registros
        const peticion = await axios.post(url, parametros);
        const datos = await peticion.data; // data
        if(datos && datos.exito == true){
          return datos.respuesta;
        }else{
          //console.log(datos)
          return false;
          
        }
      } catch (error) {
        console.warn('Ha ocurido un error: '+ error);
        return false;
      }

    }
    /**
     * carga los datos del select de comunidades autonmas
     */
    async cargarDatosSelectCCAA(){
      let ccaa = await this.traerDatosSelect('ccaa', 0);
      if (ccaa) {
          const data = ordenarArray(ccaa,'nombre'); const $select = $('#id_ca'); let nombre ;
          for (let i= 0; i < data.length; i++){
              nombre = capitalizarPrimeraLetra(data[i]['nombre']);
              $select.append(`<option value="${data[i]['id_ca']}">${nombre}</option>`);
          }
      } else {
          $select.append(`<option value="--">No hay datos</option>`);
      }
    }

    /**
     * carga los datos del select de paises
     */
      async cargarDatosSelectPaises(){
        let paises = await this.traerDatosSelect('paises', 0);
        if (paises) {
            const data = ordenarArray(paises,'nombre');
            
            const $select = $('#id_pais'); let nombre ; let codigo;
            for (let i= 0; i < data.length; i++){
                nombre = capitalizarPrimeraLetra(data[i]['nombre']);
                codigo = data[i]['cod_pais'].toLowerCase();
                $select.append(`<option value="${data[i]['id_pais']}" data-custom-properties="&lt;span class=&quot;flag flag-xs flag-country-${codigo}&quot;&gt;&lt;/span&gt;">${nombre}</option>`);
            }
            
            
        } else {
            $select.append(`<option value="--">No hay datos</option>`);
            
        }
        return true;
        
      }
    /**
     * carga los datos del select de provincias
     */
     async cargarDatosSelectProvincias(){
      let provincias = await this.traerDatosSelect('provincias', 0);
      if (provincias) {
          const data = ordenarArray(provincias,'nombre');
          const $select = $('#id_provincia'); let nombre ;
          for (let i= 0; i < data.length; i++){
              nombre = capitalizarPrimeraLetra(data[i]['nombre']);
              $select.append(`<option value="${data[i]['id_provincia']}" data-custom-properties="&lt;span class=&quot;ti ti-map-pin&quot;&gt;&lt;/span&gt;">${nombre}</option>`);
          }
          
          
      } else {
          $select.append(`<option value="--">No hay datos</option>`);
          
      }
      return true;
      
    }
      async cargarDatosSelectRoles(rol){
        let roles = await this.traerDatosSelect('roles', rol);
        const $select = $('#id_roll');
        if (roles) {
           // console.log(roles)
            const data = ordenarArray(roles,'nombre');
            
             let nombre ; let abv; let avt = '';
            for (let i= 0; i < data.length; i++){
                nombre = capitalizarPrimeraLetra(data[i]['nombre']);
                abv = nombre.split(" ");
                if(abv.length > 1){
                  for(let j=0; j < abv.length; j++){
                    avt += abv[j].toString().substring(0,1);
                  }
                  
                }else{
                  avt = nombre.substring(0,2).toUpperCase();
                  
                }
                
                $select.append(`<option value="${data[i]['id_roll']}" data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot;&gt;${avt}&lt;/span&gt;">${nombre}</option>`);
            }
            
            
        } else {
            $select.append(`<option value="--">No hay datos</option>`);
            
        }
        return true;
        
      }
    
    inicializarDatePickers(){
      let dateInputs = Array.from(document.querySelectorAll('.date-picker'));
     if(dateInputs.length < 1) return false;
      let picker ;
      dateInputs.forEach(input =>{
        let id = input.getAttribute('id');
        picker = new Litepicker({
          element: document.getElementById(id),
          buttonText: {
            previousMonth: `
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
            nextMonth: `
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
          },
          format: 'DD-MM-YYYY'
        });
        // simular typing para el input se actualize
        picker.on('hide', () => $(input).keyup())

      });

    }  
    
    /**
     * Inicializa todos los selects de la ui actual: ha de ser llamado cuando se ha cargado los select
     * @returns @bool false si no hay select que inicializar
     */
    inicializarSelectAvanzados(){
      const SELF = this; // bind al objeto
      // buscar los select con id
      const options = {

    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	};
      if($('select[id]').length > 0){
          $.each($('select[id]') , function(){
            // inicializar cada select y guardarlo en el array de selects avanzados
              SELF.selectsAvanzados[$(this).attr('id')] = new TomSelect(`#${$(this).attr('id')}`,options);
          });
      }else{
          return;
      }
    }



    /**
     * Actualiza o sincroniza un select, util para ediciones donde la pagina no recarga
     * @param {object} $select  el select al actualizar
     * @param {*} id_registro  // valor o id que que tomará el select
     */
    actualizarSelectAvanzado($select, id_registro = 0){
      let id = (id_registro) ? id_registro : 0;
      $(`#${$select}`).val(id);
      this.selectsAvanzados[ $select].sync();
    }
    /**
     * Detecta qué datos del registro se han modificado
     * los selecciona y selecciona los que no han sido modificados
     * @param {array} keys_excluidas 
     * @returns @bool | @object
     */
    async seleccionarDatosAActualizar(keys_excluidas = null) {
      // informacion origianrl
      const original = this.infoRegistro;
      // informacion editata
      const editado = this.datosRegistroEditados;
      if(original.size != editado.size){
        console.warn('Los dos objetos no son iguales, no se puede hacer la seleccion');
        return false;
      }

      // actualizar, no actualizar
      const actualizar = new Map(); 
      const noActualizar = new Map(); 
      let k_excluidas = keys_excluidas; // keys excluidas en la compracion
      let valorActual;
      for (let [llave, valor] of editado){
        valorActual = original.get(llave);
        if( isNaN(valorActual)){
            if(valorActual.toLowerCase() !== valor.toLowerCase()){
                actualizar.set(llave, valor);
            }else if( k_excluidas.includes(llave) ){
                actualizar.set(llave, valor)
            }else{
                noActualizar.set(llave, valor)
            }

        }else{
            if(valorActual !== valor){
                actualizar.set(llave, valor); 
            }else if(k_excluidas.includes(llave)){
                actualizar.set(llave, valor)
            }else{
                noActualizar.set(llave, valor)
            }
        }
      }

      // si longitud es mayor que 2(los dos campos excuidos), es que hay data que actualizar
      if(actualizar.size > k_excluidas.length){
        return {actualizar: actualizar, noActualizar: noActualizar}
      }else{
        return false;
      }




    }
    /**---------------------------------------------------------------
     * valida y actualiza los datos del formulario recibido
     * @param {object} $formulario :formulario a validar
     * @returns 
     --------------------------------------------------------------------------*/
    async submitFormulario( $formulario = null ){
      if( $formulario && typeof $formulario === 'object'){
        let $campos = $($formulario).find('.input:not(div)');
        
       let validacion = validarInput($campos); // validacion
        //console.log(validacion);
        if (validacion.exito === true){ // todo ok
            try {
                  // parametros a mandar al backend 
                  let parametros = ($formulario[0].getAttribute('enctype')) ? new FormData() : new URLSearchParams();
                  let titulo_exito; //
                  let titulo_error;
                  //actualizaciones
                  if($($formulario).attr('id') == 'editarRegistro'){
                    parametros.append('accion', 'actualizar'); //accion
                    titulo_exito = '¡ Datos actualizados !';
                    titulo_error = '¡ Error de actualización !';
                    
                  }

                  // insersiones
                  if($($formulario).attr('id') == 'insertarRegistro'){
                    parametros.append('accion', 'registrar'); //accion
                    titulo_exito = '¡ Datos registrados !';
                    titulo_error = '¡ Error de inserción !';
                  }

                  // parametros
                  $.each($campos, function(index, $campo){
                    if($($campo).attr('type') == 'file'){
                      parametros.append($(this).attr('name'), $($campo)[0].files[0]);  
                    }
                    parametros.append($(this).attr('name'), $(this).val()); 
              
                  });

                //peticion 
                const peticion = await axios.postForm(this.urlPeticiones, parametros);
                const resultado = await peticion.data; // data
                console.log(resultado);
                //return;
               
                  // si hay respuesta del servidor, sera un objeto => {exito: true|false, mensaje: array()}
                if ( resultado && typeof resultado === 'object' ) {
                  return {exito: resultado.exito, titulo: (resultado.exito) ? titulo_exito: titulo_error, mensaje: resultado.mensaje}; // exito hay data: data en array pos 0
                }else{ // la respuesta no es valida
                  
                  return {exito: false, titulo: `Operación no exitosa`,  mensaje: 'Ha ocurrido un error, no se ha recibido datos del servidor'}; // no existe el registro
                }
                
            } catch (error) { // error 
                console.log(error)
              let titulo = (error.response.status) ? `Error ${error.response.status}`: 'Error del servidor';
              let mensaje = (error.response.statusText) ? `Error en la petición al servidor,>> ${error.response.statusText}`: 'Ha fallado un error en la petición al servidor.';
  
              return {exito: false, titulo: titulo,  mensaje: mensaje};
              
            }// try catch

              
        }else{ // validacion no ok
          return {exito: false, titulo: 'Error de validación de formulario',  mensaje: 'Hay campos del formulario con datos o formato invalido'};
        } // fin if else validacion 
        
      }else{ // if else formulario == object
        return {exito: false, titulo: '¡ Error de parametro !',  mensaje: 'El formulario pasado como parametro para actualizar el registro tiene que ser un objeto'};

      } // fin if else formulrio == object
    } // fin funcion actualizar registro

    /**------------------------------------------------------------------------------
     * Recibe el id del registro a eliminar, hace la peticion al backend para eliminar el registro
     * @param {int} id_registro id del registro a eliminar
     * @returns @object con el estado de la solicitud y mensajes de error o exito
     ------------------------------------------------------------------------------------------*/
    async eliminarRegistro(id_registro = null, extraData = null){
      //console.log('funcion eliminar llamada');
      if(id_registro && !isNaN(id_registro)){
        try {
          let token = await this.generarToken();
          if(token){
            let parametros_enviar = new URLSearchParams();
            parametros_enviar.append('accion', 'eliminar'); //accion
            parametros_enviar.append('_token', token);
            parametros_enviar.append('id_registro', id_registro); // id del registro a elimnar
            // datos extra a enviar: ejemplo, eliminado usuarios se manda la foto del usuario para ser eliminado o su id, etc
            if(extraData && typeof extraData === 'object'){
              for(let llave in extraData){
                parametros_enviar.append(llave, extraData[llave]);
              }
            }
            //peticion 
            const peticion = await axios.post(this.urlPeticiones, parametros_enviar);
            const resultado = await peticion.data; // data
            // si hay respuesta del servidor, sera un objeto => {exito: true|false, mensaje: array()}

            //console.log(resultado)

            if ( resultado && typeof resultado == 'object' ) {
              return {exito: resultado.exito, titulo: (resultado.exito) ? '¡ Registro Eliminado !': '¡ Registro no eliminado ! ', mensaje: resultado.mensaje}; // exito hay data: data en array pos 0
            }else{ // la respuesta no es valida
              return {exito: false, titulo: `Operación no exitosa`,  mensaje: 'Ha ocurrido un error, no se ha recibido datos del servidor'}; // no existe el registro
            }

          }else{
            return {exito: false, titulo: `Error de Token`,  mensaje: 'Token de seguridad vacío, vuelve a intarlo.'}; // no existe el registro
          }
          
        } catch (error) {
          let titulo = (error.response.status) ? `Error ${error.response.status}`: 'Error del servidor';
          let mensaje = (error.response.statusText) ? `Error en la petición al servidor,>> ${error.response.statusText}`: 'Ha fallado un error en la petición al servidor.';

          return {exito: false, titulo: titulo,  mensaje: mensaje};
        }
      }else{
        return {exito: false, titulo: '¡ Error de parametro !',  mensaje: 'Para eliminar un registro, el ID tiene que ser numerico'};
      }
    } // eliminar registro

    /**----------------------------------------------------------
     * Recibe varios registros para ser eliminados
     * @param {Array} registros array de registros a eliminar
     * @returns @object con el estado de la solicitud y mensajes de error o exito
     ------------------------------------------------------------*/
    async eliminarMultiplesRegistros(){
      const registros = this.registros_seleccinados;
      // se espera un array
      if( registros && registros instanceof Array && registros.length > 0 ){
        let info; let errores = new Array();
        for(let i = 0; i < registros.length; i++){
          info = await this.traerResgistros(registros[i]);
          if(!(info.exito)){
            errores.push(`Error ID ${registros[i]} : ${info.datos}`);
          }
        }
        // check for errores
        if(errores.length <= 0){ // todo ok
          $('#modalEliminar').modal('show'); // mostrar ventan confirmacion
          // mensaje de advertensia
          $('#smsAdvertencia').text(`Vas a eliminar ${registros.length} registros, ¿estás seguro de esto ?. Si eliminas estos registros, los cambios realizados no se podrán deshacer.`);
          // toolpit boton confirmacion
          let total_txt = (registros.length == 1)? `Eliminar 1 registro` : `Eliminar ${registros.length} registos`
          $('#confirmarEliminacion').attr('data-bs-original-title',`Si, ${total_txt}` );
          $('#confirmarEliminacion').text(total_txt);
          $('#confirmarEliminacion').tooltip();

          // esperar confirmacion
          const SELF = this; // bind al objeto
          $('#confirmarEliminacion').unbind('click').bind('click', async  (event) => {
              event.preventDefault(); event.stopPropagation();
              $('#confirmarEliminacion').attr('disabled', true);
              $('#confirmarEliminacion').text('Eliminado...');

              try {
                let token = await this.generarToken();
                if(token){
                  let parametros_enviar = new URLSearchParams();
                  parametros_enviar.append('accion', 'eliminar_multiple'); //accion
                  parametros_enviar.append('_token', token);
                  parametros_enviar.append('registros', registros); // id del registro a elimnar
              
                  //peticion 
                  const peticion = await axios.post(this.urlPeticiones, parametros_enviar);
                  const resultado = await peticion.data; // data
      
                  //console.log(resultado);
                  // si hay respuesta del servidor, sera un objeto => {exito: true|false, mensaje: array()}
                  if ( resultado && typeof resultado == 'object' ) {
                    if(resultado.exito == true){
                      SELF.cancelarSeleccion(); // finalizar seleccion
                      $('#modalEliminar').modal('hide'); // close modal
                      SELF.tablaDatos.ajax.reload(null, false); // recargar tabla
                    }else{
                      $('#confirmarEliminacion').text('Volver a intentar...');
                      $('#confirmarEliminacion').attr('disabled', false);
                    }

                    // mostrar notificacion
                    mostrarNotificaciones({
                        tipo: (resultado.exito) ? 'exito' : 'aviso',
                        titulo: (resultado.exito) ? '¡ Registros Eliminados !': '¡ Registros no eliminados ! ',
                        mensaje: resultado.mensaje
                    });

                  }else{ // la respuesta no es valida
                    mostrarNotificaciones({
                      tipo: 'error',
                      titulo: '¡ Operación no exitosa',
                      mensaje:'Ha ocurrido un error, no se ha recibido datos del servidor'
                    });
                  }
      
                }else{ // no token
                    mostrarNotificaciones({
                      tipo: 'error',
                      titulo: '¡ Token vacío !',
                      mensaje:'Es necesario un token de seguridad'
                    });
                }
                
              } catch (error) {
                //console.log(error);
                let titulo = (error.response.status) ? `Error ${error.response.status}`: 'Error del servidor';
                let mensaje = (error.response.statusText) ? `Error en la petición al servidor,>> ${error.response.statusText}`: 'Ha fallado un error en la petición al servidor.';

                mostrarNotificaciones({
                  tipo: 'error',
                  titulo: titulo,
                  mensaje:mensaje
                });
      
                //return {exito: false, titulo: titulo,  mensaje: mensaje};
              }

          }); // confirmacion
        }else{ // hay errores
          mostrarNotificaciones({tipo: 'aviso',titulo: 'Opp!!, hay errores...',mensaje: errores });
        } // if else errores.length
        
      }else{ // no array
        mostrarNotificaciones({tipo: 'aviso',titulo: 'Error de parametro...',mensaje: 'Para eliminar multiples registros, es necesario un array' });

      } // fin if array
    } // eliminar multiples registros

    /**
     * carga una foto en inputs de tipo file
     * @param {*} $cargador el elemento que dispara la fn
     * @returns 
     */
    fileInputs($cargador = null){
      if (!$cargador){return false;}
      // el contenedor del cargador de archivos
      const $preview = $cargador.parentElement;
      // el input type file oculto del formulario
      let $input = $cargador.nextElementSibling.getAttribute('id');
      let input = $('#'+$input);
      $input = document.getElementById($input);

      if(!$preview || !$input){return false};
  
      // click input
      $input.click();
      // extensiones validas:  removeEventListener('change')
      const ext_validas = Array.from($input.getAttribute('data-allow-format').split(','));
      
      const SELF = this; // bind al objeto
      $input.addEventListener('change', (event) => {
          event.stopPropagation();
          const reader = new FileReader();
          const files = Array.from($input.files); // archivos
          // extension
          const extension = files[0].name.split('.').pop() || false;
          
          // check extension
          if(!extension ){ 
              SELF.resetFileInputs();
              mostrarNotificaciones({
                tipo: 'error',
                titulo: 'Archivo sin extensión',
                mensaje: 'El archivo que has seleccionado no tiene extension'
              });
              return false;
          }
          // validar extension 
          if(!ext_validas.includes(extension.toLowerCase())){
             SELF.resetFileInputs();
             mostrarNotificaciones({
              tipo: 'error',
              titulo: 'Extensión no válida',
              mensaje: `La extensión: ${extension} no es valida, solo se admite: ${ext_validas.toString()}`
            });
              return false;
          }
  
          reader.onload = (e) =>{
              // ocultar cargador// mostrar imagen: 
              // el input file donde se carga, url de la imagen
              SELF.renderFileAvatar($input, e.target.result);
              $input.setAttribute('data-foto', files[0].name);
          }
          reader.readAsDataURL(input[0].files[0]);

  
      }, {passive: true,once: true}
      );
    }
    
    /**
     * Renderiza o muestra la img cargada en un input de tipo file
     * @param {*} $input El input donde se ha cargado la img
     * @param {*} url url de la img
     * @returns 
     */
    renderFileAvatar($input = null, url = null){
        if(!$input || !url){return false;}
        // elemento que dispara la seleccion de foto
        let $cargador = $input.previousElementSibling;
        if (!$cargador){ return false;}
    
        $cargador.classList.add('elementoOculto');
        // check si existe prewiev antes de renderizar
        const $prewiev = $cargador.previousElementSibling;
        if($prewiev) $prewiev.remove(); // quitar preview
        // injectar preview
        $input.parentElement.insertAdjacentHTML('afterbegin', '<div class="avatar-preview file-prewiew w-100" style="background-image: url(\''+url+'\');" rel="'+url+'"><span class="eliminar-foto"><i class="ti ti-trash-x"></i></span></div>');
        
    }
  
    /**
     * resetea el cargador de archivos de fotos
     * @param {*} $target el boton eliminar foto que ha disparado el evento
     */
     resetFileInputs($target = null){
      if($target){
          //previsualizador de foto
          let $preview = $target.parentElement;
          // el elemento que dispara la seleccion de foto
          let $cargador = $preview.nextElementSibling;
          // input tipo file
          let $input = $cargador.nextElementSibling;
          $input.removeAttribute('data-foto');
          // div o elemento que cotiene los elementos anteriores
          const $container = $cargador.parentNode;
          if($cargador.classList.contains('elementoOculto')){
              $cargador.classList.remove('elementoOculto');
              $container.removeChild($preview);
              
          }
  
          $input.value = '';
      }else{
          let $previews = Array.from(document.getElementsByClassName('avatar-preview'));
          let $cargadores = Array.from(document.getElementsByClassName('cargador-archivos'));
          let $inputs = Array.from(document.getElementsByClassName('custom-file-input'));
  
          // resetear todo
          $previews.forEach( $preview => {$preview.remove();});
          //cargadores
          $cargadores.forEach($cargador => {
              if($cargador.classList.contains('elementoOculto')){
                  $cargador.classList.remove('elementoOculto');
              }
          });
  
          // inputs
          $inputs.forEach($input => {
              if($input.value != ''){$input.value = '';}
          });
          
      }
    }


    /**============================================================
     * Importar registros desde un archivo csv o excel
     * ========================================================*/
    cargarArchivoImportacion() {
      // boton distarador
      let botonCargador = $('#cargador-archivo') // boton o disparador
      let input = $('#archivo') // input tipo file
      let file_preview = $('#input-file-container') // donde se cargara la foto
      const SELF = this; // bind al objeto
      
      input.click();
      input.off('change').on('change', async function (event) { 
          event.stopPropagation();
          // extenciones validas
          let allow_extension = Array.from(input.attr('data-allow-format').split(','));
       
      
          let reader = new FileReader();
          let extension = input[0].files[0].name.split('.').pop() || null;
          let nombre = input[0].files[0].name || null;
          //console.log(input[0].files[0].type)
          if ( extension && allow_extension.includes(extension.toLowerCase()) ) {
              reader.onload = function(event) {
                  let icono = (extension == 'csv') ? 'csv': 'excel';
                  file_preview.prepend('<div class="avatar file-prewiew"><img src="public/icons/'+icono+'.png" class="file-thumb"></img> <span class="eliminar-archivo" id="eliminar-archivo"><i class="ti ti-trash"></i></span><span class="etiqueta-archivo">'+nombre+'</span></div>');
  
                  botonCargador.addClass('elementoOculto');
  
                  $('#modalImportData .modal-footer').removeClass('elementoOculto');
                  $("#modalImportData .modal-footer button").attr('disabled', false).prop('disabled', false);
  
              }
              reader.readAsDataURL(input[0].files[0]);
              // app.importarRegistros(input[0].files[0]);
              
          }else{
  
              //input.val('');
              SELF.reiniciarCargadorArchivos();
              mostrarNotificaciones({ tipo: 'aviso', titulo: 'Archivo no valido',
                  'mensaje': `Solo archivos con extensicion: ${input.attr('data-allow-format')}. La extensión ${extension} no es valida.` 
              });
              
              //return;
          }
          
         //resultado = result;
         
      });
      
      // clic en icono eliminar o cambiar imagen
      file_preview.off('click').on('click', '.eliminar-archivo', function(){
          SELF.reiniciarCargadorArchivos();
      });
  
      // click boton importar datos del archivo
      $('#confirmarImportacion').off('click').on('click', async function(event){
          event.stopPropagation(); event.preventDefault();
          SELF.mostrarLoaderArchivo();  
         let resultado = await SELF.importarRegistros(input[0].files[0]);
         
         if(resultado){
              if(resultado.exito == true){
                  SELF.reiniciarCargadorArchivos();
                  SELF.mostrarLoaderArchivo(); //ocultarLoaderArchivo();
                  SELF.tablaDatos.ajax.reload(null, false);
  
              }else{SELF.mostrarLoaderArchivo(); /*ocultarLoaderArchivo();*/}
  
              let titulo;
              if(Object.hasOwn(resultado, 'titulo')){
                  titulo = resultado.titulo;
              }else{
                  titulo = (resultado.exito == true) ? '¡¡¡ Registros Importados !!!!!' : '¡ Ha ocurrido un error !';
              }
  
              mostrarNotificaciones({
                  tipo:(resultado.exito) ? 'exito': 'error',
                  titulo: titulo,
                  mensaje: resultado.mensaje
              });
  
              if(Object.hasOwn(resultado, 'registros_invalidos') && resultado.registros_invalidos == true){
                  // crear link de descarga
                  let link_descarga = 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'+ resultado.documento.url ;
                  let Link = document.createElement("a");
                  let nombre_archivo = resultado.documento.nombre;
           
                  Link.href = link_descarga;
                  Link.download = nombre_archivo;
                  Link.click();
                  setTimeout(() => Link.remove(), 500);
              }
         }else{
           SELF.reiniciarCargadorArchivos()
         }
  
      });
     // console.log(resultado)
    }
  
    //reset boton
    reiniciarCargadorArchivos() {
      //console.log(elemento)
      $('.file-prewiew').remove(); // quitar prewiew del archivo
      $('.file-loading').remove(); // quitar loader si hay
      $('#cargador-archivo').removeClass('elementoOculto'); // mostrar cargador archivos
      $('#archivo').val(''); // input vacio
      $('#modalImportData .modal-footer').addClass('elementoOculto'); // ocultar pie de ventana
      // botones de pie de ventana deshabilitados
      $("#modalImportData .modal-footer button").attr('disabled', false).prop('disabled', false);
  
      //
      $("#confirmarImportacion svg").removeClass('elementoOculto').siblings().text('Importar datos').find('span.animated-dots').remove();
    }
  
    // mostra loader
    mostrarLoaderArchivo(){
      // quitar previsualizador
      $('.file-prewiew').toggleClass('elementoOculto'); 
      // añadir loader 
      if($('.file-prewiew').hasClass('elementoOculto')){
          if($('.file-loading').length <= 0 ){
              $('#input-file-container').prepend(`
                  <div class="avatar file-loading">
                  <span>
                      <span class="spinner-border text-teal" role="status"></span>
                  </span>
                  <span class="etiqueta-archivo">Procesando archivo, no cierre esta ventana</span>
              </div>`);
          }
          $("#modalImportData").addClass('blocked');
          //$("#myModal3").modal({backdrop: "static"});
          $("#modalImportData .modal-footer button").attr('disabled', true).prop('disabled', true);
          $("#confirmarImportacion svg").addClass('elementoOculto').siblings().text('Procesando').append(`
          <span class="animated-dots"></span>`);
  
      }else{
  
          if($('.file-loading').length >= 0 ) $('.file-loading').remove();
          $("#modalImportData").removeClass('blocked');
          // botones habilitados
          $("#modalImportData .modal-footer button").attr('disabled', false).prop('disabled', false);
          $("#confirmarImportacion svg").removeClass('elementoOculto').siblings().text('Intentar de nuevo..').find('span.animated-dots').remove();
  
      }
  
    }

    async importarRegistros( archivo ){
      if (archivo){
        //const formData = new FormData()

        const peticion = await axios.postForm(this.urlPeticiones, {'accion': 'importar-registros',
          'archivo': archivo
      });
        const resultado = await peticion.data;

        //console.log(resultado);

        // si hay respuesta del servidor, sera un objeto => {exito: true|false, mensaje: array()}
        if ( resultado && typeof resultado == 'object' ) {
         return resultado;
        }else{ // la respuesta no es valida
          return {exito: false, titulo: `Operación no exitosa`,  mensaje: 'Ha ocurrido un error, no se ha recibido datos del servidor'}; // no existe el registro
        }


      }else{
        return {exito: false, titulo: '¡ Error de parametro !',  mensaje: 'Es necesario un archivo para poder importar los datos.'};
      }
    }

    /**
     * Recibe un objecto que es un  boton(del dom), el cual tiene un atributo con el formato de documento al que se quire exportar los datos.
     * @param {object} target boton que ha originado o disparado el metodo 
     */
    async exportarDatos(target) {
      if (target && target instanceof Object){
        // mostrar loader del boton
        this.mostrarLoaderBotones(target);
        // formato de doc exportacion
        let formato = target.attr('data-export-format') || false;
        if(formato){
          let parametros_enviar = new URLSearchParams();
          parametros_enviar.append('accion', 'exportar-datos'); //accion
          parametros_enviar.append('formato', formato); // formato
      
          //peticion 
          const peticion = await axios.post(this.urlPeticiones, parametros_enviar);
          const resultado = await peticion.data; // data
          // si hay respuesta valida del back-end, sera un objeto => 
        
          if ( resultado && typeof resultado == 'object' ) {
            if(resultado.exito === true){
              // check si nos han devuelto un documento a descargar
              if(Object.hasOwn(resultado, 'documento') ){
                // crear link de descarga
                let Link = document.createElement("a");
         
                Link.href = resultado.documento.url;
                Link.download = resultado.documento.nombre;
                Link.click();
                setTimeout( () =>{ Link.remove()}, 1000);
              }
              
              // check si es html para impresion
              if(Object.hasOwn(resultado, 'html')){
                
                const estilos = `
                <style>
                body{padding-top: 10px;text-align:center; margin: 0 auto;}
                h2{margin-bottom:1px;font-family: 'Consolas',sans-serif;}
                p{text-align:center;font-size:14px; font-weight: bold; color:#888;margin-top:1px; padding-top:0px;font-family: 'Source Sans Pro','Candara', sans-serif;}
                table { border-spacing:1;border-collapse: collapse; width: 98%; margin: 0 auto; background:#fff;border-radius:8px;overflow:hidden;margin:0 auto;position:relative}
                thead tr {height: 40px;background-color: #36304a1; text-align: center;}
                thead tr th { font-family: monospace; padding:10px; text-transform: uppercase; font-size: .9rem; font-weight: bold;color: #fff;  border: 1.8px solid #36304a;}
                tbody td {padding: 8px; text-align: center; border: 1px solid #DDD; font-family: 'Source Sans Pro', sans-serif; font-weight: 500;}
                tbody tr {font-size:14px; color: #333;}
                tbody tr:last-child{border:0}
                tbody tr:nth-child(even){background-color:#f5f7fb}
                </style>`;
            
                const tabla = resultado.html.tabla;
                const titulo = resultado.html.titulo;
                let ventana = window.open('', 'Catalogo Farmacia', 'height=400, width=600');
                ventana.document.write(`<html><head><title>${titulo}</title>${estilos}</head><body>`);
                ventana.document.write(tabla);
                ventana.document.write('<body></html>');
                ventana.document.close(); // para IE >=10
                ventana.focus(); // IE >=10
                ventana.print();
                setTimeout( () => ventana.close(), 1000);
                
              } // if resultado.html

            }else{
              mostrarNotificaciones({
                tipo: 'aviso',
                titulo: '¡¡¡ Ocurrió un error !!!',
                mensaje: resultado.mensaje
              });
  
            } // if else resultado.exito == true


          }else{ // la respuesta no es valida
            mostrarNotificaciones({
              tipo: 'aviso',
              titulo: 'Ocurrió un error...',
              mensaje: 'La respuesta recibida del servidor no es correcta.'
            })
          }
  
        }else{

          mostrarNotificaciones({
            tipo: 'aviso',
            titulo: 'Formato vacio',
            mensaje: 'Es necesario especificar un formato de documento'
          })
        } // if else formato boton

        // quitar loader del boton
        this.mostrarLoaderBotones(target);

      }else{
        console.warn('Se esperaba un objeto')
      }
    }
}