
import {mostrarNotificaciones, capitalizarPrimeraLetra, contieneSoloNumeros, inicializarTablaDatos, validarInput, ordenarArray} from './modules.js';

// ui
import {UI} from './ui.js';

let tablaProvincias;
const app = new UI();

$(document).ready(function(){
    // clase activa nav
    $('#nav-item-general').addClass('active');
    $('#nav-item-general .dropdown-menu').addClass('show')
    $('#general-provincias').addClass('active')

    //$('#offcanvasEnd').offcanvas('show');
    tablaProvincias = $("#tablaProvincias").DataTable(
        inicializarTablaDatos( 
            $("#tablaProvincias") , // tabla a inicializar
            'provincias', // url procedencia datos
            'POST', // metodo
            { accion:'traer', registro: 0} // data - parametros
        )
    );
    //tabla de datos y url de peticiones
    app.tablaDatos = tablaProvincias; // metodo set: tabla de datos
    app.formulario = $('#addUpdateProvincia form');
    app.urlPeticiones = 'provincias'; // metodo set: url para hacer nuestras peticiones



    //app.generarToken();
    // divs botones de la tabla de datos y div search input: reajustar tamaño
      $.each($('#table-default .col-md-6'), function(index, elemento){
        $(this).removeClass('col-sm-12 col-md-6').addClass('col-auto');
      });
      
      // Inicializar tooltips para los botones de la tabla de datos
      setTimeout(function (){
        $.each($('[title]'), function(i, e){
          $(this).tooltip()
        })
        

      }, 500);



    // insertar ccaa
    $('#insertarRegistro').off('submit').on('submit', async function(event){
        event.preventDefault();
        let token = await app.generarToken();
        if(token){
            if( $('#_token').val().trim().length <= 0){
                $('#_token').val(token);
            }

            let respuesta = await app.submitFormulario(app.formulario);
            if(respuesta){
                $('#_token').val('');
                $.each($(app.formulario).find('.input:not(div)'), function($indice, $campo){
                    $($campo).removeClass('is-invalid').removeClass('is-valid');
                });
            }
            
            if(respuesta.exito == true){
                $(app.formulario)[0].reset();
                
                $('#guardarCambios span').text('Sin datos que guardar');
                $('#guardarCambios').attr('disabled', true); //boton desahabilitado
                app.tablaDatos.ajax.reload(null, false)
            }else{
    
                $('#guardarCambios span').text('Volver a intentar...');
                $('#guardarCambios').attr('disabled', false);
            }
            // mostrar notificacion
            mostrarNotificaciones({
                tipo: (respuesta.exito)? 'exito' : 'aviso',
                titulo: respuesta.titulo,
                mensaje: respuesta.mensaje
            });

        }else{
            console.warn('ERROR: recarge la pagina...')
        }
    });
    

    // --



    // editar o eliminar pais -> vigilar que boton se ha clickeado al hacer click en el dom
    $(document).click(function(event){
        const $elemento = $(event.target); // elemento clickeado
        event.stopPropagation();

        let $id = 0; // id de pais, usado para llamar la funcion editar, eliminar
        /*
        verificar si el elemento clickeado es el boton de editar o el un elemento hijo de ese boton
        */
        if( $elemento.parents('.botonEditar').length > 0 || $elemento.hasClass('botonEditar') ){
            if($elemento.parents('.botonEditar').attr('data-id')){
                $id = $elemento.parents('.botonEditar').attr('data-id');
            }
            if($elemento.hasClass('botonEditar')){
                $id = $elemento.attr('data-id');
            }
            
            //funcion editar
            if($id && $id > 0 ){editarProvincia($id);  } // llama a la funcion para editar   
        } // fin if botonEditar
        
        /**
         * verificar si el elemento clickeado es el boton de eleminar pais o un elemento hijo de este, como podría ser el icono que se encuentra dentro.
         */
        if($elemento.parents('.botonEliminar').length > 0 || $elemento.hasClass('botonEliminar')){
            if($elemento.parents('.botonEliminar').attr('data-id')){$id = $elemento.parents('.botonEliminar').attr('data-id') ; }
            if($elemento.hasClass('botonEliminar')){$id = $elemento.attr('data-id');}

            // eliminar pais;
            if($id && $id > 0 ){eliminarProvincia($id);} // id de la ccomunidad aut.
              
        } // fin if botonEliminar

        // seleccionar todos los registros de la tabla de datos
        if ($elemento.hasClass('select-all-data') || $elemento.hasClass('single-select')){
            // iniciar seleccion multiple registro
            //app.inicializarSeleccionMultiple()
            app.inicializarSeleccionMultiple( $elemento );           
            // abortar o cancelar seleccion
            $('#action-cancel').on('click', () => app.cancelarSeleccion());


            // eliminar registros seleccionados
            $('#action-delete').unbind('click').bind('click', function(){ app.eliminarMultiplesRegistros(); });
   
        } // fin seleccinar todos los registros / un registro



    }); // document click


    
    /** ===================================================
     * al escribir en los inputs
     * ========================================*/ 
    app.inputsEventListerners();
    
    //cancelar actualizacion
    $('#cancelarActualizacion').unbind('click').bind('click', function(){
        cancelarActualizacion();
    });
    
    
    //--------------- COMUN ------------------------
    // modal eliminar registro
    app.modalEliminarEventsListeners();

    // modal importar exportar datos
    app.modalExportImportDataEventsListeners();
    
    //--------------- COMUN ------------------------



});

// llenar select de comunidades autonomas
document.addEventListener("DOMContentLoaded", async function () {  
    app.cargarDatosSelectCCAA();
    app.inicializarSelectAvanzados()
    //cargarSelect();
  
});







/**
 * hace que el formulario de registro y actualizacion de ccaa vuelva a su estado inicial: registrar
 * modificando la vista del formulario
 */
function cancelarActualizacion(){
    $('#addUpdateProvincia .modal-status').removeClass('bg-success').addClass('bg-primary');
    $(app.formulario).prop('id', 'insertarRegistro');  // formulario
    $('#addUpdateProvincia .container-panel-aviso').addClass('panel-info').removeClass('panel-success');
    $('#panelAviso').text('Rellena este campo para registrar una nueva comunidad autonoma'); // panel aviso
    $(app.formulario)[0].reset(); // reseter form
    app.actualizarSelectAvanzado('id_ca', 0);
    $('#_token').val('');
    $('.form-footer .col').removeClass('col').addClass('col-12');
    $('.col-finalizar').addClass('elementoOculto');
    // boton guardar cambios
    if ( $('#guardarCambios').length > 0 ){
        $("#guardarCambios span").text('Esperando...'); 
        $('#guardarCambios').attr('disabled', true);
        $("#guardarCambios svg").addClass('elementoOculto');
        $("#guardarCambios").removeClass('btn-success').addClass('btn-primary');
    }
    let $campos = $(app.formulario).find('.input:not(div)');
    $($campos).removeClass('is-valid').removeClass('is-invalid');
    // card header
    $('#addUpdateProvincia .card-header').addClass('elementoOculto');
    $('#addUpdateProvincia .card-header .card-title').text('');
    if($('#notificacionApp').length > 0){
        $('#notificacionApp').remove();
    }
    // ID DE LA COMMM
    $('#id_Privincia').remove();
    


}

/**
 * actualiza la informacion de una comundad autonoma
 * @param {number} $id : id de la comunidad autonoma (numerico)
 */
async function editarProvincia($id){
    if ($id && contieneSoloNumeros($id)){
        $('#_token').val('');
        let info = await app.traerResgistros($id);
        if( info.exito === true){
            $(app.formulario).prop('id', 'editarRegistro'); // id formulario
            //id de la ccaa
            if($('#id_provincia').length <= 0){
                $('#nombre').after('<input type="hidden" class="input" name="id_provincia" id="id_provincia" data-required="true" data-min-length="1" data-max-length="2" data-format="numbers"  /> ');   
            }

            //console.log(info.datos)
            let $campos = $(app.formulario).find('.input:not(div)');
            $($campos).removeClass('is-valid').removeClass('is-invalid');
            // objeto map que contendrá la iformacion del registro mapeado procedente del backend
            let infoProvincia = new Map(); // datos o info de la ccaa seleccionado
            for (let clave in info.datos){  
                infoProvincia.set(clave, info.datos[clave]); // llenar Map datosRegistro [clave, valor]
            }
            infoProvincia.set('_token', '');
            // set informacion de registro con el que va a trabajar
            app.infoRegistro = infoProvincia; 
            $('#addUpdateProvincia .modal-status').removeClass('bg-primary').addClass('bg-success');
            // card header
            $('#addUpdateProvincia .card-header').removeClass('elementoOculto');
            $('#addUpdateProvincia .card-header .card-title').text(`Info de ${capitalizarPrimeraLetra(app.infoRegistro.get('nombre'))}`);
            // panel aviso
            $('#addUpdateProvincia .container-panel-aviso').removeClass('panel-info').addClass('panel-success');
            // texto panel aviso
            $('#panelAviso').text(`Esta es la informacion sobre  ${capitalizarPrimeraLetra(app.infoRegistro.get('nombre'))} que puedes editar.`);
            //footer formulario

            $('.form-footer .col-12').removeClass('col-12').addClass('col');
            $('.col-finalizar').removeClass('elementoOculto');
            // boton guardar cambios
            if ( $('#guardarCambios').length > 0 ){
                $("#guardarCambios span").text('Sin cambios'); 
                $('#guardarCambios').attr('disabled', true);
                $("#guardarCambios svg").addClass('elementoOculto');
                $("#guardarCambios").removeClass('btn-primary').addClass('btn-success');
            }

   
            //valor de los inputs
            $.each($campos, function(index, $input) {
                let $id_input = $($input).attr('id'); // id de cada input
                $($input).val( app.infoRegistro.get($id_input) ); // valor del input  
                //cargar el obj con los datos de la ccaa editados
                app.datosRegistroEditados.set( $id_input, app.infoRegistro.get($id_input) );
            });
            // sincoronizar select para que coja el nuevo valor de la ccaa
            app.actualizarSelectAvanzado('id_ca', app.infoRegistro.get('id_ca'));

            $(app.formulario).off('submit').on('submit', async function(event){
                event.preventDefault();
                let token = await app.generarToken();
                if(token){
                    
                    $('#_token').val(token);
                    
                    let validacion = validarInput(app.formulario.find('.input:not(div'));
                    if (validacion.exito === true){
                        let respuesta = await app.submitFormulario(app.formulario);
                        if(respuesta){
                            $.each($(app.formulario).find('.input:not(div'), function($indice, $campo){
                                $($campo).removeClass('is-invalid').removeClass('is-valid');
                            });
                        }
                
                        if(respuesta.exito == true){
                            $('#_token').val('');
                            editarProvincia( $('#id_provincia').val() );
                            app.tablaDatos.ajax.reload(null, false)
                        }else{
                
                            $('#guardarCambios span').text('Volver a intentar...');
                            $('#guardarCambios').attr('disabled', false);
                        }
                        // mostrar notificacion
                        mostrarNotificaciones({
                            tipo: (respuesta.exito)? 'exito' : 'aviso',
                            titulo: respuesta.titulo,
                            mensaje: respuesta.mensaje
                        });
                    }else{ // validacion no ok
                        mostrarNotificaciones({
                            tipo: 'error',
                            titulo: '¡ Validación error !',
                            mensaje: `El formulario contiene errores, revisalo...`
                        });
                    }
                } else{
                    console.warn('ERROR: recarge la pagina');
                }

            }); // submit form
        }
    }
}

/**
 * elimina la comunidad autonoma seleccionada
 * @param {number} $id id de la com. autonoma
 */
async function eliminarProvincia($id){
    if($id && !(isNaN($id)) ){ 
        let info = await app.traerResgistros($id); // verificar que exite
        if(info.exito == true){ // el registro existe
          $('#modalEliminar').modal('show'); // mostrar ventan confirmacion
          // mensaje de advertensia
          $('#smsAdvertencia').text('¿ Quieres eliminar la información de '+ capitalizarPrimeraLetra(info.datos.nombre) +' ?. Si eliminas este registro, los cambios realizados no se podrán deshacer.')
          $('#confirmarEliminacion').attr('data-bs-original-title',`Borrar ${capitalizarPrimeraLetra(info.datos.nombre)}` );
          $('#confirmarEliminacion').tooltip();
          $('#footerModalEliminar').after('<input type="hidden" name="id_registro" id="id_registro" value="'+$id+'" /> ');

          $('#confirmarEliminacion').unbind('click').bind('click', async function (event){
              event.preventDefault(); event.stopPropagation();
              let $id = $('#id_registro').val(); // id o key del registro
              $('#confirmarEliminacion').attr('disabled', true);
              $('#confirmarEliminacion').text('Eliminado...');
              let respuesta = await app.eliminarRegistro($id);
              
              if(respuesta.exito === true){
                 // console.log('registro eliminado');
                  $('#modalEliminar').modal('hide'); // ocultar ventana confirmacion
                  $('#id_registro').remove(); // quitar input id registro del html
                  app.tablaDatos.ajax.reload(null, false);
              }else{
                  $('#confirmarEliminacion').text('Volver a intentar...');
                  $('#confirmarEliminacion').attr('disabled', false);
              }
                  // mostrar notificacion
              mostrarNotificaciones({
                  tipo: (respuesta.exito) ? 'exito' : 'aviso',titulo: respuesta.titulo,mensaje: respuesta.mensaje});
          });
        } else {
            mostrarNotificaciones({tipo: 'aviso',titulo: info.titulo,mensaje:info.mensaje});
            return;
        }


    } else{
        mostrarNotificaciones({tipo: 'aviso',titulo: 'Error de paramatros',mensaje: 'Asegurate de que el ID sea numérico y que exista un url.'});
        return;
    }
}




 




