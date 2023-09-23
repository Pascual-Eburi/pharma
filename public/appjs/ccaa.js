
import {mostrarNotificaciones, capitalizarPrimeraLetra, contieneSoloNumeros, inicializarTablaDatos, editarRegistro, validarInput, contieneSoloLetras} from './modules.js';

// ui
import {UI} from './ui.js';

let tablaCcaa;
// al hacer click en el el check bock seleccionar todos los registros
let $registros_seleccinados = [];
const app = new UI();

$(document).ready(function(){
    // clase activa nav
    $('#nav-item-general').addClass('active');
    $('#nav-item-general .dropdown-menu').addClass('show')
    $('#general-ccaa').addClass('active')
    //$('#offcanvasEnd').offcanvas('show');
    tablaCcaa = $("#tablaCcaa").DataTable(
        inicializarTablaDatos( 
            $("#tablaCcaa") , // tabla a inicializar
            'ccaa', // url procedencia datos
            'POST', // metodo
            { accion:'traer', registro: 0} // data - parametros
        )
    );
    //tabla de datos y url de peticiones
    app.tablaDatos = tablaCcaa; // metodo set: tabla de datos
    app.formulario = $('#addUpdateCcaa form');
    app.urlPeticiones = 'ccaa'; // metodo set: url para hacer nuestras peticiones

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
                $.each($(app.formulario).find('.input'), function($indice, $campo){
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


    /*setTimeout(function(){
        let $ultimo_select = $('.single-select').last().offset().top || 0;
        let $largo_pantalla = $(window).height();
        let $largo_documento = $(document).height();
    
        alert(`Ultimo select: ${$ultimo_select} , LARGO PANTALLA: ${$largo_pantalla}, LARGO DOCUMENTO: ${$largo_documento}`);
    }, 5000)*/

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
            if($id && $id > 0 ){editarCCAA($id);  } // llama a la funcion para editar   
        } // fin if botonEditar
        
        /**
         * verificar si el elemento clickeado es el boton de eleminar pais o un elemento hijo de este, como podría ser el icono que se encuentra dentro.
         */
        if($elemento.parents('.botonEliminar').length > 0 || $elemento.hasClass('botonEliminar')){
            if($elemento.parents('.botonEliminar').attr('data-id')){$id = $elemento.parents('.botonEliminar').attr('data-id') ; }
            if($elemento.hasClass('botonEliminar')){$id = $elemento.attr('data-id');}

            // eliminar pais;
            if($id && $id > 0 ){eliminarCCAA($id);} // id de la ccomunidad aut.
              
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


/**
 * hace que el formulario de registro y actualizacion de ccaa vuelva a su estado inicial: registrar
 * modificando la vista del formulario
 */
function cancelarActualizacion(){
    $('#addUpdateCcaa .modal-status').removeClass('bg-success').addClass('bg-primary');
    $(app.formulario).prop('id', 'insertarRegistro');  // formulario
    $('#addUpdateCcaa .container-panel-aviso').addClass('panel-info').removeClass('panel-success');
    $('#panelAviso').text('Rellena este campo para registrar una nueva comunidad autonoma'); // panel aviso
    $(app.formulario)[0].reset(); // reseter form
    $('.form-footer .col').removeClass('col').addClass('col-12');
    $('.col-finalizar').addClass('elementoOculto');
    // boton guardar cambios
    if ( $('#guardarCambios').length > 0 ){
        $("#guardarCambios span").text('Esperando...'); 
        $('#guardarCambios').attr('disabled', true);
        $("#guardarCambios svg").addClass('elementoOculto');
        $("#guardarCambios").removeClass('btn-success').addClass('btn-primary');
    }
    let $campos = $(app.formulario).find('.input');
    $($campos).removeClass('is-valid').removeClass('is-invalid');
    // card header
    $('#addUpdateCcaa .card-header').addClass('elementoOculto');
    $('#addUpdateCcaa .card-header .card-title').text('');
    if($('#notificacionApp').length > 0){
        $('#notificacionApp').remove();
    }
    // ID DE LA COMMM
    $('#id_ca').remove();


}

/**
 * actualiza la informacion de una comundad autonoma
 * @param {number} $id : id de la comunidad autonoma (numerico)
 */
async function editarCCAA($id){
    if ($id && contieneSoloNumeros($id)){
        let info = await app.traerResgistros($id);
        if( info.exito === true){
            $(app.formulario).prop('id', 'editarRegistro'); // id formulario
            //id de la ccaa
            if($('#id_ca').length <= 0){
                $('#nombre').after('<input type="hidden" class="input" name="id_ca" id="id_ca" data-required="true" data-min-length="1" data-max-length="2" data-format="numbers"  /> ');   
            }

            let $campos = $(app.formulario).find('.input');
            $($campos).removeClass('is-valid').removeClass('is-invalid');
            // objeto map que contendrá la iformacion del registro mapeado procedente del backend
            let infoCcaa = new Map(); // datos o info de la ccaa seleccionado
            for (let clave in info.datos){
                infoCcaa.set(clave, info.datos[clave]); // llenar Map datosRegistro [clave, valor]
            }
            infoCcaa.set('_token', '');
            // set informacion de registro con el que va a trabajar
            app.infoRegistro = infoCcaa; 
            $('#addUpdateCcaa .modal-status').removeClass('bg-primary').addClass('bg-success');
            // card header
            $('#addUpdateCcaa .card-header').removeClass('elementoOculto');
            $('#addUpdateCcaa .card-header .card-title').text(`Info de ${capitalizarPrimeraLetra(app.infoRegistro.get('nombre'))}`);
            // panel aviso
            $('#addUpdateCcaa .container-panel-aviso').removeClass('panel-info').addClass('panel-success');
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

            $(app.formulario).off('submit').on('submit', async function(event){
                event.preventDefault();
                let token = await app.generarToken();
                if(token){
                    if( $('#_token').val().trim().length <= 0){
                        $('#_token').val(token);
                    }
                    let validacion = validarInput(app.formulario.find('.input'));
                    if (validacion.exito === true){
                        let respuesta = await app.submitFormulario(app.formulario);
                        if(respuesta){
                            $.each($(app.formulario).find('.input'), function($indice, $campo){
                                $($campo).removeClass('is-invalid').removeClass('is-valid');
                            });
                        }
                
                        if(respuesta.exito == true){
                            $('#_token').val('');
                            editarCCAA( $('#id_ca').val() );
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
async function eliminarCCAA($id){
    if($id && !(isNaN($id)) ){ 
        let info = await app.traerResgistros($id); // verificar que exite
        if(info.exito == true){ // el registro existe
          $('#modalEliminar').modal('show'); // mostrar ventan confirmacion
          // mensaje de advertensia
          $('#smsAdvertencia').text('¿ Quieres eliminar la información de '+ capitalizarPrimeraLetra(info.datos.nombre) +' ?. Si eliminas este registro, los cambios realizados no se podrán deshacer.')
          $('#confirmarEliminacion').attr('data-bs-original-title',`Borrar ${capitalizarPrimeraLetra(info.datos.nombre)}` );
          $('#confirmarEliminacion').tooltip();
          $('#footerModalEliminar').after('<input type="hidden" class="input" name="id_registro" id="id_registro" value="'+$id+'" /> ');

        } else {
            mostrarNotificaciones({
                tipo: 'aviso',
                titulo: info.titulo,
                mensaje:info.mensaje
            })
        }

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
                tipo: (respuesta.exito) ? 'exito' : 'aviso',
                titulo: respuesta.titulo,
                mensaje: respuesta.mensaje
            });
        });


    } else{
        mostrarNotificaciones({
            tipo: 'aviso',
            titulo: 'Error de paramatros',
            mensaje: 'Asegurate de que el ID sea numérico y que exista un url.'
        });
    }
}




 























/*
async function eliminarMultiplesRegistros($registros = null){
    if($registros && $registros instanceof Array){
        if($registros.length > 0){
            let total_registros = $registros.length;
            let info; let errores = new Array();
            for(let i = 0; i < total_registros; i++){
                info = await app.traerResgistros($registros[i]); // verificar que exite
                if(!(info.exito)){
                    errores.push(`Error ID: ${$registros[i]}, ${info.datos}`)
                }
            }
            // check por si errores
            if(errores.length <= 0 ){ // el registro existe
                $('#modalEliminar').modal('show'); // mostrar ventan confirmacion
                // mensaje de advertensia
                $('#smsAdvertencia').text(`Vas a eliminar ${total_registros} registros, ¿estás seguro de esto ?. Si eliminas estos registros, los cambios realizados no se podrán deshacer.`)
                $('#confirmarEliminacion').attr('data-bs-original-title',`Si, Borrar ${total_registros} registros` );
                $('#confirmarEliminacion').tooltip();
               
                // espera por confirmacion
                $('#confirmarEliminacion').unbind('click').bind('click', async function (event){
                    event.preventDefault(); event.stopPropagation();
                    $('#confirmarEliminacion').attr('disabled', true);
                    $('#confirmarEliminacion').text('Eliminado...');
                    let respuesta = await app.eliminarMultiplesRegistros($registros);
                    
                    if(respuesta.exito === true){
                       // console.log('registro eliminado');
                        app.cancelarSeleccion();
                        $('#modalEliminar').modal('hide'); // ocultar ventana confirmacion
                        app.tablaDatos.ajax.reload(null, false);
                    }else{
                        $('#confirmarEliminacion').text('Volver a intentar...');
                        $('#confirmarEliminacion').attr('disabled', false);
                    }
                        // mostrar notificacion
                    mostrarNotificaciones({
                        tipo: (respuesta.exito) ? 'exito' : 'aviso',
                        titulo: respuesta.titulo,
                        mensaje: respuesta.mensaje
                    });

                    console.log($registros_seleccinados)
                }); // confirm eliminacion
      
            } else {
                  mostrarNotificaciones({tipo: 'aviso',titulo: 'Opp!!, hay errores...',mensaje: errores })
            } // if else errores.length <= 0
                  
        }else{
            console.warn('Array de registros vacío')
        } // if else $registros.length > 0
    }else{
        console.warn('Los registros se eliminan desde un array')
    } // if else registros instance of Array()
}*/