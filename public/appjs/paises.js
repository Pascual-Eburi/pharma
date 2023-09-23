import {mostrarNotificaciones, capitalizarPrimeraLetra, contieneSoloNumeros,  inicializarTablaDatos} from './modules.js';

// ui
import {UI} from './ui.js';

let tablaPaises  ; // tabla de datos de paises
//const $formulario = $('#modalCreateUpdate form'); //formulario
//const $campos = $('#modalCreateUpdate form :input'); // inputs del formulario

//var datosResgistro; // guardara los datos del pais seleccionado
//var datosResgistroEditados = new Map();// datos del pais editados
const app = new UI() ;

$(document).ready(function(){
    // clase activa nav
    $('#nav-item-general').addClass('active');
    $('#nav-item-general .dropdown-menu').addClass('show')
    $('#general-paises').addClass('active')
    //$('#offcanvasEnd').offcanvas('show');
    tablaPaises = $("#tablaPaises").DataTable(
        inicializarTablaDatos( 
            $("#tablaPaises") , // tabla a inicializar
            'paises', // url procedencia datos
            'POST', // metodo
            { accion:'traer', registro: 0} // data - parametros
        )
    );
    //tabla de datos y url de peticiones
    app.tablaDatos = tablaPaises; // metodo set: tabla de datos
    app.formulario = $('#modalCreateUpdate form');
    app.urlPeticiones = 'paises'; // metodo set: url para hacer nuestras peticiones


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


      //console.log(x)

    //$("#yes").click();*/
    // click boton registra pais 
    $("#botonRegistrar").on('click', function(){
        registrarPais();
    }); 
    // al hacer click en el el check bock seleccionar todos los registros
    let $registros_seleccinados = [];

    /*setTimeout(function(){
        let $ultimo_select = $('.single-select').last().offset().top || 0;
        let $largo_pantalla = $(window).height();
        let $largo_documento = $(document).height();
    
        alert(`Ultimo select: ${$ultimo_select} , LARGO PANTALLA: ${$largo_pantalla}, LARGO DOCUMENTO: ${$largo_documento}`);
    }, 5000)*/

    // editar o eliminar pais -> vigilar que boton se ha clickeado al hacer click en el dom
    $(document).click(function(event){
        const $elemento = $(event.target); // elemento clickeado

        let $id_pais = 0; // id de pais, usado para llamar la funcion editar, eliminar
        /*
        verificar si el elemento clickeado es el boton de editar o el un elemento hijo de ese boton
        */
        if($elemento.parents().hasClass('botonEditar') || $elemento.hasClass('botonEditar')){
            if( $elemento.parents().attr('data-id') || $elemento.hasClass('botonEditar') ){
                $id_pais = ($elemento.parents().attr('data-id')) ? $elemento.parents().attr('data-id'):  $elemento.attr('data-id');
            }
            //console.log('VAMOS A EDITAR ------');
            //funcion editar
            if($id_pais && $id_pais > 0 ){
                editarPais($id_pais); // llama a la funcion para editar
            }   
        } // fin if botonEditar

        
        /**
         * verificar si el elemento clickeado es el boton de eleminar pais o un elemento hijo de este, como podría ser el icono que se encuentra dentro.
         */

        if($elemento.parents().hasClass('botonEliminar') || $elemento.hasClass('botonEliminar')){
            if( $elemento.parents().attr('data-id') || $elemento.hasClass('botonEliminar') ){
                $id_pais = ($elemento.parents().attr('data-id')) ? $elemento.parents().attr('data-id'):  $elemento.attr('data-id');
            }
            // eliminar pais;
            if($id_pais && $id_pais > 0 ){
                eliminarPais($id_pais);// id del pais
            }
           
            
        } // fin if botonEliminar

        // seleccionar todos los registros de la tabla de datos
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

    // boton ir al inicio
    /*var e=a(".scroll-to-top-btn");
	if( e.length> 0 && ( a(window).on( "scroll", function(){
		a(this).scrollTop()>600?e.addClass("visible"):e.removeClass("visible")}),

        e.on("click",function(b){b.preventDefault(),a("html").velocity("scroll",{offset:0,duration:1200,easing:"easeOutExpo",mobileHA:!1})})),a(document).on("click",".scroll-to",function(b){
			var c=a(this).attr("href");if("#"===c)return!1;var d=a(c);if(d.length>0){var e=d.data("offset-top")||65;a("html").velocity("scroll",{offset:a(this.hash).offset().top-e,duration:1e3,easing:"easeOutExpo",mobileHA:!1})}b.preventDefault()}));
            */



    

    /**********************************************************
     *al cerrar ventana para añadir y editar paises 
    ***************************************************************/ 
    $("#modalCreateUpdate").on('hide.bs.modal', function () {
        $.each($(app.formulario).find('.input'), function($indice, $campo){ // quitar las clases valido-invalido a los input
            $($campo).removeClass('is-invalid').removeClass('is-valid');
        });

        //reiniciar formulario de agregar y editar
        $(app.formulario)[0].reset();
        // ocultar bandera
        $('#bandera').removeClass( $("#bandera").attr('class').substring(13)).addClass('flag-country-xx');
        $('#id_pais').remove();

        // ocualtar cualquier notificacion
        $('#notificacionApp').remove();    
    });

    /** ===================================================
     * al escribir en los inputs
     * ========================================*/ 
    app.inputsEventListerners();
     
    
    /*
    $.each($campos, function($indice, $campo){
        // BLUR INPUTS
        $($campo).on('blur', function(){
            let $validacion = validarInput($campos);
            if ($validacion.exito == false){ // hay errores
                mostrarNotificaciones({
                    tipo: 'aviso',
                    titulo: '¡ Hay errores que corregir !',
                    mensaje: $validacion.mensajes
                });  
            }else{
                if ( $('#notificacionApp').length > 0){$('#notificacionApp').remove();}
            }
        });
        // KEY UP INPUTS
        $($campo).on('keyup', function(event) {
            //let $valor_campo = $(this).val().trim();
            let $validacion = validarInput($campos);

            //si hay errores
            if ($validacion.exito == false){
                for (let i = 0; i < Object.keys($validacion.validaciones).length ; i++ ){
                    let campo = Object.keys($validacion.validaciones)[i];
                    if ($validacion.validaciones[campo].valido == false){
                        $('#'+campo).addClass('is-invalid').removeClass('is-valid');
                    }else{
                        $('#'+campo).removeClass('is-invalid').addClass('is-valid');
                    }
                }

                if ( $formulario.attr('id') == 'insertarRegistro' ){ // 
                    // $("#nombre").val().length > 0 && ( contieneSoloLetras( $("#nombre").val()) )   
                        $('#guardarCambios svg').addClass('elementoOculto');
                        $('#guardarCambios').attr('disabled', true);
                        $("#guardarCambios span").text('Sin datos que guardar');
                    
                } // fin if formulario registrar pais


            }else { // todo ok
                $($campos).removeClass('is-invalid').addClass('is-valid');

                // si el formulario es para registrar pais id = insertarRegistro
                if ( $formulario.attr('id') == 'insertarRegistro' ){ // 
                    $('#guardarCambios svg').removeClass('elementoOculto');
                    $('#guardarCambios').attr('disabled', false);
                    $("#guardarCambios span").text('Insertar registro'); 
                                        
                } // fin if formulario registrar pais
            }



            // si el formulario es para actualizar info de pais id= editarPais
            if ( $formulario.attr('id') == 'editarPais' ){
                //por cada input  guardar su id y valor en el obj map datosResgistroesEditados cuando se escribe
                $.each($campos, function(indice, $elemento ) {
                    datosResgistroEditados.set( $($elemento).attr('id') , $($elemento).val() );

                });
                
                // comparar si los dos objetos maps tienen los mismos valores, para deteminar cuando hay cambios que guardar y cuando no.
                //compararObjetosMap => true : ===> los datos siguen siendo los mismos
                if( compararObetosMap( datosResgistroEditados, datosResgistro ) ||  $validacion.exito == false ){
                    $('#guardarCambios').attr('disabled', true);
                    $('#guardarCambios span').text('Nada que guardar...');
                    $("#guardarCambios svg").addClass('elementoOculto');

                }else{ //hay cambios, hay datos que han cambiado
                    // boton guardar desab si el nombre, cod,abv son validos
                    if ($validacion.exito == true ){
                        $('#guardarCambios').attr('disabled', false);
                        $("#guardarCambios svg").removeClass('elementoOculto');
                        $('#guardarCambios span').text('Guardar cambios');
                    }
                }
            } // fin if form editarPais

 
        })     
    });*/



    // al hacer click en btn eliminar del modal
    $('#divBotonesFormulario #botonEliminar').on('click', function(){
        //eliminar pais: data-id contiene el id del pais
        eliminarPais( $(this).attr('data-id') ); // id del pais  
    });



});

//app.getFormInputs();
/*async function datos(){
    
    //let users = await app.traerResgistros('a');

    let a = await app.validarFormulario()
    console.log(a) 
}
datos();*/

/**
 * registrar un nuevo pais en la base de datos
 */
/*
export function registrarPaises(){
    $('#modalCreateUpdate').modal('show'); // mostrar modal
    $formulario.prop('id','registrarPais'); // cambiar id del formulario
    $('#nombre').focus();

    //==> botones del formulario
    $('#guardarCambios svg').addClass('elementoOculto');
    $('#guardarCambios').attr('disabled', true);
    $("#guardarCambios span").text('Sin datos que guardar');
    $("#divBotonesFormulario .btn-delete").addClass('elementoOculto'); // hide, no se necesita aqui

    //header del modal
    $('#headerModal').addClass('elementoOculto'); //ocultar

    //==>> contenido modal
    // panel notificacion
    $('#panelAviso').text('Rellena los campos del formulario para registrar un nuevo pais. Recuerda que el nombre del pais es obligatorio');

    // formulario de registro 
    $('#registrarPais').off('submit').on('submit', function(event){
        event.preventDefault();
        // enviar data al backed soolo si todo esta ok
        if($nombre_valido && $codigo_valido && $abv_valida){
            let form = $(this);
            let datos = form.serialize();
            $.ajax({
                url: 'paises',
                method: 'POST',
                data: {accion: 'registrar', datos : datos  },
                dataType: 'json',
                beforeSend: function (){
                    $('#guardarCambios span').text('Registrando pais...');
                }, 
                success: function($respuesta){

                    // quitar las clases valido-invalido a los input
                    $.each($campos, function($indice, $campo){
                        $($campo).removeClass('is-invalid').removeClass('is-valid');
                    });

                    if ($respuesta.exito == true ){ // todo ok, registro insertado
                        $formulario[0].reset(); // resetear form
                        // quitar bandera del pais registrado
                        let $bandera_actual = $("#bandera").attr('class').substring(13);
                        $('#bandera').removeClass($bandera_actual).addClass('flag-country-xx');
                        tablaPaises.ajax.reload(null, false); // recargar tabla
                        $('#guardarCambios span').text('Sin datos que guardar');
                        $('#guardarCambios').attr('disabled', true); //boton desahabilitado
                        mostrarNotificaciones({
                            tipo: 'exito',
                            titulo: '¡ Operación exitosa !',
                            mensaje: $respuesta.mensaje
                        });
                        
                    } else {
                        $('#guardarCambios span').text('Volver a intertar');
                        mostrarNotificaciones({
                            tipo: 'error',
                            titulo: '¡ Algo ha salido mal !',
                            mensaje: $respuesta.mensaje
                        });
                    }

                },
                error: function(request, error){
                    console.log('ha ocurrido un error')
                    console.log(error)
                   
                }
            });
        } 
    });
  
}*/

// cambia y muestra un previsualizacion de la bandera del pais al escribir en el campo codigo
function cambiarBanderaAlEscribir(){
    $('#cod_pais').on('keyup', function(){
        let $bandera_actual = $("#bandera").attr('class').substring(13); // bandera actual
        if ($(this).val().trim().length == 2 ){ // cambiar solo cuando se ha instroducido mas de 2 caracteres
            $('#bandera').removeClass($bandera_actual).addClass('flag-country-'+$(this).val().toLowerCase());
        }else{
            if( $bandera_actual != 'flag-country-xx'){
                $('#bandera').removeClass($bandera_actual).addClass('flag-country-xx');
            }
        }
    });

}
cambiarBanderaAlEscribir();


function registrarPais(){
    $('#modalCreateUpdate').modal('show'); // mostrar modal
    $('#headerModal').addClass('elementoOculto'); //ocultar header del modal
    $(app.formulario).prop('id','insertarRegistro'); // cambiar id del formulario
    
    //==>> contenido modal
    // panel notificacion
    $('#panelAviso').text('Rellena los campos del formulario para insertar un nuevo registro. Recuerda que los campos con * son obligatorios');

    //botones formulario
    $('#guardarCambios svg').addClass('elementoOculto');
    $('#guardarCambios').attr('disabled', true);
    $("#guardarCambios span").text('Sin datos que guardar');
    $("#divBotonesFormulario .btn-delete").addClass('elementoOculto'); // hide, no se necesita aqui
    $(app.formulario).off('submit').on('submit', async function(event){
        event.preventDefault();
        let respuesta = await app.submitFormulario(app.formulario);
        if(respuesta){
            $.each($(app.formulario).find('.input'), function($indice, $campo){
                $($campo).removeClass('is-invalid').removeClass('is-valid');
            });
        }

        if(respuesta.exito == true){
            let $bandera_actual = $("#bandera").attr('class').substring(13);
            $('#bandera').removeClass($bandera_actual).addClass('flag-country-xx');
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
    });
 


}

/**
 * editarPais permite editar la informacion del pais seleccionado
 * cod_pais es un parametro numerio
 * @param {int} $cod_pais 
 */
async function editarPais($id_pais){  
    if($id_pais && contieneSoloNumeros($id_pais)){
        let info = await app.traerResgistros($id_pais); // traer info del pais
        if (info.exito == true){ // hay data que procesar:
            $(app.formulario).prop('id', 'editarRegistro'); // cambiar form a editar
            // eliminar la bandera que hubiera del html para poder pintar la nueva
              if(  $('.modal-header span.flag').length > 0 ){
                  $('.modal-header span.flag').remove();
              }
              // si el icono de añadir esta visible, sacarlo del dom
              if ( ($('.modal-header div svg').length > 0 ) ) {
                $(".modal-header div svg").remove();
              }
              $('#headerModal').removeClass('elementoOculto'); //mostrar header del modal
  
            // ocultar el svg en btn guardar cambios si estuviera visible
            if ( $('#guardarCambios').length > 0 && $(app.formulario).attr('id') == 'editarRegistro'){
              $("#guardarCambios span").text('Sin cambios'); 
              $('#guardarCambios').attr('disabled', true);
              $("#guardarCambios svg").addClass('elementoOculto');
            }
  
            // boton eliminar - mostrarlo si esta oculto
            if ( $('#botonEliminar').hasClass('elementoOculto') ) {
                $('#botonEliminar').removeClass('elementoOculto');
            }
            // btn eliminar del modal
            $('#divBotonesFormulario #botonEliminar').attr('data-id', $id_pais);
  
            // objeto map que contendrá la iformacion del registro mapeado procedente del backend
            let infoPais = new Map(); // datos o info del pais seleccionado
            for (let clave in info.datos){
              infoPais.set(clave, info.datos[clave]); // llenar Map datosRegistro [clave, valor]
            }

            // set informacion de registro con el que va a trabajar la UI o APP
            app.infoRegistro = infoPais; 
            
            $('#modalCreateUpdate').modal('show'); // mostrar modal
  
              //console.log(app.datosRegistro)
            /*=============================================
                    elementos html del modal
            ==============================================*/
            //titulo: header
            $('#tituloModal').text( capitalizarPrimeraLetra( app.infoRegistro.get('nombre') ) );

            //bandera del pais - header modal
            $('.modal-header div').prepend('<span class="flag flag-sm flag-country-'+app.infoRegistro.get('cod_pais').toLowerCase()+'"></span>');

            //panel de asivo
            $('#panelAviso').text(`Esta es la informacion sobre  ${capitalizarPrimeraLetra(app.infoRegistro.get('nombre'))} que puedes editar. Recuerda que el nombre del pais es obligatorio.`);

            //--- inputs del formulario
            //id del pais
            $('#cod_pais').after('<input type="hidden" class="input" name="id_pais" id="id_pais" data-required="true" data-min-length="1" data-max-length="3" data-format="numbers" value="'+$id_pais+'" /> ');
            
            // bandera
            let $bandera_actual = $("#bandera").attr('class').substring(13);
            $('#bandera').removeClass($bandera_actual).addClass(`flag-country-${app.infoRegistro.get('cod_pais').toLowerCase()} `);

            // recorrer los inputs del formulario asignandoles su valor correspondiente segun info del pais
            let $campos = $(app.formulario).find('.input');
            $.each($campos, function(index, $input) {
                let $id_input = $($input).attr('id'); // id de cada input
                $($input).val( app.infoRegistro.get($id_input) ); // valor del input
                
                //cargar el obj con los datos del pais editados
                app.datosRegistroEditados.set( $id_input, app.infoRegistro.get($id_input) );
            });

            // -- 
            $(app.formulario).off('submit').on('submit', async (event) =>{
                event.preventDefault();
                let respuesta = await app.submitFormulario(app.formulario);
                if(respuesta){
                    $.each($campos, function($indice, $campo){
                        $($campo).removeClass('is-invalid').removeClass('is-valid');
                    });
                }

                if(respuesta.exito == true){
                    $('#guardarCambios').attr('disabled', true);
                    $('#guardarCambios span').text('Sin cambios...');
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
            });// submit form

            
        }else{ // no hay data: mostrar errores
            mostrarNotificaciones({
              tipo: 'aviso',
              titulo: info.titulo,
              mensaje:info.mensaje
            })
        }

    //$('#divBotonesFormaulario').after();

    }else{ //end if cod_pais
        mostrarNotificaciones({
            tipo: 'aviso',
            titulo: '¡ ID con formato incorrecto !',
            mensaje: `El ID pasado como parametro no tiene un formato correcto.`
        });
        
    }
    
} // end funcion


/**
 * recibe un codigo de pais, verifica que exista y elmina dicho pais de la base de datos 
 * @param {*} $id_pais 
 */
/*
export function eliminarPaises($cod_pais = null){
    if($cod_pais && contieneSoloNumeros($cod_pais)){
        //asgurarse de que existe el pais que se quiere eliminar
        $.ajax({
            url: 'paises',
            method: 'POST',
            data: {accion:'traer', pais: $cod_pais},
            dataType: 'json',
            beforeSend: function(){
                console.log('Enviando peticion...')
            },
            success: function (respuesta){
               
                const $expresion = 'data-id="'+$cod_pais+'"';

                 //asegurarse de que exite el id del pais en la respuesta recibida del servidor - backend
                if( respuesta['data'][0][6].match($expresion) ){
                    let $nombre_pais = respuesta['data'][0][3]; // nombre del pais
                    $('#modalEliminar').modal('show'); // mostrar ventan confirmacion
                    // mensaje de advertensia
                    $('#smsAdvertencia').text('¿ Quieres eliminar la información de '+ capitalizarPrimeraLetra($nombre_pais) +' ?. Si eliminas este pais, los cambios realizados no se podrán deshacer.')
                    $('#confirmarEliminacion').attr('data-bs-original-title',`Borrar ${capitalizarPrimeraLetra($nombre_pais)}` );
                    $('#confirmarEliminacion').tooltip();
                    $('#footerModalEliminar').after('<input type="hidden" name="id_pais" id="id_pais" value="'+$cod_pais+'" /> ');
                    

                    $('#confirmarEliminacion').on('click', function (event){
                        event.preventDefault();
                        let $id = $('#id_pais').val(); // id del pais
                        $.ajax({
                            url: 'paises',
                            method: 'POST',
                            data: {accion: 'eliminar', pais: $id },
                            dataType: 'json',
                            beforeSend: function(){
                                console.log(`eliminando pais ${$id}`)
                            },
                            success: function($respuesta){
                                if($respuesta.exito == true){
                                    tablaPaises.ajax.reload(null, false); // recargar tabla
                                    $('#modalEliminar').modal('hide'); //ocultar ventanda confirm
                                    mostrarNotificaciones({
                                        tipo: 'exito',
                                        titulo: '¡ Operación exitosa !',
                                        mensaje: $respuesta.mensaje
                                    });

                                }else {
                                    mostrarNotificaciones({
                                        tipo: 'error',
                                        titulo: '¡ Algo salido mal !',
                                        mensaje: $respuesta.mensaje
                                    });
                                }
                            },
                            error: function(request, error){
                               
                                mostrarNotificaciones({
                                    tipo: 'error',
                                    titulo: '¡ Error en la petición AJAX !',
                                    mensaje: `Ha ocurrido un error en la petición ajax mientras se intentaba eliminar el pais: ${error}`
                                });

                            }
                        });
                    });
                    
                } else {
                    mostrarNotificaciones({
                        tipo: 'aviso',
                        titulo: '¡ Algo ha salido mal !',
                        mensaje: 'La respuesta recibida del servidor no incluye el id del pais seleccionado, vuelve a intentarlo.'
                    });
                } // fin if else respuesta data
                
            }, // fin succes function
            error: function(){
                mostrarNotificaciones({
                    tipo: 'error',
                    titulo: '¡ Error petición AJAX !',
                    mensaje: 'Ha ocurrido un error en la petición ajax al servidor mientras se intentaba traer los datos del pais seleccionado, vuelve a intentarlo.'
                });
            }
        });// ajax traer info del pais

        $('#footerModalEliminar').after();
        
    } else {
        console.warn('Esta función no ha recibido un codigo de pais o el codigo recibido no es de tipo numérico')
    }

    //$cod_pais = null;
}*/

/**
 *
 * @param {number} $pais id del pais a eliminar
 */
async function eliminarPais($id = null){
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
            //event.preventDefault();
            event.stopPropagation();

            let $id = $('#id_registro').val(); // id o key del registro
            $('#confirmarEliminacion').attr('disabled', true);
            $('#confirmarEliminacion').text('Eliminado...');
            let respuesta = await app.eliminarRegistro($id);
            
            if(respuesta.exito === true){
               // console.log('registro eliminado');
                $('#confirmarEliminacion').text('Si, eliminar');
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

