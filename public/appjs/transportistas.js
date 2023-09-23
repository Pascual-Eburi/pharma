import {mostrarNotificaciones, capitalizarPrimeraLetra, contieneSoloNumeros, inicializarTablaDatos, validarInput, ordenarArray, generarAvatar} from './modules.js';

// ui
import {UI} from './ui.js';

let tablaTransportistas;

const app = new UI();

$(document).ready(function(){
    // clase activa nav
    $('#nav-item-crm').addClass('active');
    $('#nav-item-crm .dropdown-menu').addClass('show');
    $('#crm-transportistas').addClass('active');


    // inicializar tabla de datos
    tablaTransportistas = $("#tablaTransportistas").DataTable(
        inicializarTablaDatos(
            $("#tablaTransportistas"),
            'transportistas',
            'POST',
            {accion: 'traer-registros', registro: 0}
        )
    );


    app.tablaDatos = tablaTransportistas; // metodo set: tabla de datos
    app.formulario = $('#addUpdate form');
    app.urlPeticiones = 'transportistas';
    
    
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

    // editar o eliminar pais -> vigilar que boton se ha clickeado al hacer click en el dom
    $(document).click(function(event){
        const $elemento = $(event.target); // elemento clickeado
        event.stopPropagation();

        let $id = false; // id de pais, usado para llamar la funcion editar, eliminar
        /*
        verificar si el elemento clickeado es el boton de editar o el un elemento hijo de ese boton
        */
        if( $elemento.parents('.botonEditar').length > 0 || $elemento.hasClass('botonEditar') ){
            event.preventDefault();
            if($elemento.parents('.botonEditar').attr('data-transportista')){
                $id = $elemento.parents('.botonEditar').attr('data-transportista');
            }
            if($elemento.hasClass('botonEditar')){
                $id = $elemento.attr('data-transportista');
            }
            
            //funcion editar
            if($id && $id > 0 ){editarTransportista($id);  } // llama a la funcion para editar  
            return 
        } // fin if botonEditar
        
        /**
         * verificar si el elemento clickeado es el boton de eleminar pais o un elemento hijo de este, como podría ser el icono que se encuentra dentro.
         */
        if($elemento.parents('.botonEliminar').length > 0 || $elemento.hasClass('botonEliminar')){
            event.preventDefault();
            if($elemento.parents('.botonEliminar').attr('data-transportista')){$id = $elemento.parents('.botonEliminar').attr('data-transportista') ; }
            if($elemento.hasClass('botonEliminar')){$id = $elemento.attr('data-transportista');}

            // eliminar pais;
            if($id && $id > 0 ){eliminarTransportista($id);} // delete.
              
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

    //--------------- COMUN ------------------------
    // modal eliminar registro
    app.modalEliminarEventsListeners();

    // modal importar exportar datos
    app.modalExportImportDataEventsListeners();
    
    //--------------- COMUN ------------------------

    
    $.ajax({
        url: 'transportistas',
        method: 'post',
        data:{accion:'traerInfo', registro: '1'},
        dataType: 'json',
        success: function(data){

            console.log(data)
        },
        error: function(error){
            console.log(error)
        }
    })
 
});

// llenar select de comunidades autonomas
document.addEventListener("DOMContentLoaded", async function () {  
   let selectProvincias = await app.cargarDatosSelectProvincias();
    let selectRoles = await app.cargarDatosSelectRoles('8');
    if(selectProvincias && selectRoles){
        app.inicializarSelectAvanzados()
    }
    
});
//listener ventana añadir fab
const modalAddUpdate = document.getElementById('addUpdate');
modalAddUpdate.addEventListener('hide.bs.offcanvas', ()=>{
    reiniciarFormulario(); // reiniciar formulario
    app.resetFileInputs(); // reiniciar file inputs
    resetRenderInfoTransportista(); // reset render info usuario : si id form es editar
});
modalAddUpdate.addEventListener('show.bs.offcanvas', ()=>{
    if($(app.formulario).attr('id') != 'editarRegistro' ){
        reiniciarFormulario();
        app.resetFileInputs();
    }
});

// resetear o reiniciar formaulario añadir fab
document.getElementById('resetFormBtn').addEventListener('click', (event) =>{
    event.preventDefault();
    reiniciarFormulario();
})



const reiniciarFormulario = () => {
    $(app.formulario)[0].reset();
    // reset select roles
    app.actualizarSelectAvanzado('id_roll', 0);
    // reset select paises
    app.actualizarSelectAvanzado('id_provincia', 0);
    app.resetFileInputs();
    const inputs = Array.from(app.formulario.find('.input:not(div)'));
    inputs.forEach(input => {
        if(input.classList.contains('is-valid')){
            input.classList.remove('is-valid');
        }
        if(input.classList.contains('is-invalid')){
            input.classList.remove('is-invalid');
        }
    });

    $("[data-bs-toggle='popover']").popover('hide');
    $('#guardarCambios').attr('disabled', true);
    $('#guardarCambios span').text('Sin cambios...');
    $("#guardarCambios svg").addClass('elementoOculto');


}


/*-----------------------------------------------
        AÑADIR NUEVO FABRICANTE
----------------------------------------------------*/
const addBtn = document.getElementById('registrarTransportista');
addBtn.addEventListener('click', (event)=>{
    event.preventDefault();
    //set atributo del formulario a insertarRegistro
    app.formulario[0].setAttribute('id', 'insertarRegistro');
    $(app.formulario).unbind('submit').bind('submit', async(e) =>{
        e.preventDefault();
        let respuesta = await app.submitFormulario(app.formulario);
        if(respuesta){
            $.each($(app.formulario).find('.input:not(div)'), ($indice, $campo)=>{
                $($campo).removeClass('is-invalid').removeClass('is-valid');
            });
        }
        if(respuesta.exito == true){
            app.tablaDatos.ajax.reload(null, false);
            reiniciarFormulario();
        }else{
            $('#guardarCambios span').text('Reintentar');
            $('#guardarCambios').attr('disabled', false);
        }
        // mostrar notificacion
        mostrarNotificaciones({
            tipo: (respuesta.exito)? 'exito' : 'aviso',
            titulo: respuesta.titulo,
            mensaje: respuesta.mensaje
        });
        
    })

}, {once: true});

/*---------------------------------------------------------------
            TRAER Y RENDERIZAR LA Transportista SELECCIONADO 
-----------------------------------------............................*/
const renderizarInfoTransportista = async($id = null) =>{
    if(!$id || !contieneSoloNumeros($id)){
        console.warn('Id de transportista no valido...');
        return false;
    }
    let info = await app.traerResgistros($id);

    if(!info.exito) {
        mostrarNotificaciones({
            tipo: 'error',
            titulo: info.titulo,
            mensaje: info.mensaje
        });
        return false;
    };
    const data = info.datos;
    // renderizar info en el formulario para editar
    // info.data: contiene la data del transportista
    
    /*------------------------ ID USUARIO Y transportista -------------- */
    const $id_usuario = document.getElementById('id_usuario');
    const $id_transportista = document.getElementById('id_trans');
    const $form = document.querySelector('#addUpdate form');
    if(!$id_usuario){
        $form.insertAdjacentHTML('beforeend','<input type="hidden" class="input" name="id_usuario" id="id_usuario" data-required="true" data-min-length="1" data-max-length="11" data-format="numbers"  />')
    }
    if(!$id_transportista){
        $form.insertAdjacentHTML('beforeend','<input type="hidden" class="input" name="id_trans" id="id_trans" data-required="true" data-min-length="1" data-max-length="6" data-format="numbers"/>')
    }
    /*------------- RENDERIZAR AVATAR EN EL HEADER ------------- */
    let oldavatar = document.querySelector('#addUpdate .icon-header .avatar');
    if(oldavatar) oldavatar.remove();
    
    const $icon_header = document.querySelector('#addUpdate .icon-header');
    Array.from($icon_header.children).forEach($child =>{
        $child.classList.add('elementoOculto');
    })
    let avatar ;
    if(data.foto){
        const ruta_foto = `./public/avatars/usuarios/${data.foto}`;
        avatar = await generarAvatar({tipo:'img', url: ruta_foto});
        
    }else{
        // renderizar avatar con sus iniciales
        avatar = await generarAvatar({tipo:'text', texto: data.nombre});  
    }
    if(!avatar) return false;
    $icon_header.insertAdjacentHTML('beforeend', avatar);
    /*------------------------ TITULO VENTANA Y SUBTITULO -------------- */
    document.querySelector('#addUpdate .header').textContent = `${data.nombre}`;
    document.querySelector('#addUpdate .row-header p').textContent = 'Esta es la información que puedes editar y acctualizar';


    /*------------------- INPUTS ----------------------*/
    /* foto del fabricante en input */
    if(data.foto){
        const $input_foto = document.querySelector("#addUpdate form #foto");
        const ruta_foto = `./public/avatars/usuarios/${data.foto}`;
        app.renderFileAvatar($input_foto, ruta_foto);
    }
 

    // ocultar lon inputs de password
    const $pass = Array.from(document.querySelectorAll('.input[type="password"'));
    $pass.forEach($pass =>{
        $pass.parentElement.parentElement.classList.add('elementoOculto');
        $pass.classList.remove('input');
        $pass.classList.add('elementoOculto');
    });

    // objeto map que contendrá la iformacion del registro mapeado procedente del backend
    let infoTransportista = new Map(); // datos o info del reg.  seleccionado
    const excluir = ['pedidos', 'roll', 'provincia', 'ccaa', 'fecha_registro'];
    for (let clave in data){ 
        // quitar las claves que no se van a usar
        if( !(excluir.includes(clave)) ){
            if(clave == 'foto' && !data['foto']){
                //continue;
                infoTransportista.set(clave, ''); // llenar Map datosRegistro 
            }else if(clave == 'id_provincia' && !data['id_provincia']){
                infoTransportista.set(clave, '0'); // llenar Map datosRegistro 
            }else if(clave == 'id_roll' && !data['id_roll']){
                infoTransportista.set(clave, '0'); // llenar Map datosRegistro 
            }else if(clave == 'fax' && !data['fax']){
                infoTransportista.set(clave, ' '); // llenar Map datosRegistro 
            }else{
                infoTransportista.set(clave, `${data[clave]}`); // llenar Map datosRegistro 
            }
        } 
    }
    // guardar la info del registro en la app
    app.infoRegistro = infoTransportista; 

    //valor de los inputs
    const $campos = $(app.formulario).find('.input:not(div)');
    $.each($campos, function(index, $input) {
        let $id_input = $($input).attr('id'); // id de cada input
        if( $id_input == 'foto'){
            $($input).val(''); // valor del input 
            //$($input).attr('data-foto', app.infoRegistro.get($id_input) ) 
            $($input).attr('data-foto', data['foto'] ) 
            // guardar archivo y su input
            app.registradorArchivos.set($id_input, app.infoRegistro.get($id_input)); 
        }else{
            $($input).val( app.infoRegistro.get($id_input ) ); // valor del input  
        }
        //cargar el obj con los datos editados
        //app.datosRegistroEditados.set( $id_input, app.infoRegistro.get($id_input) );
    });
    // sincoronizar select para que coja el nuevo valor 
    app.actualizarSelectAvanzado('id_provincia', app.infoRegistro.get('id_provincia'));
    app.actualizarSelectAvanzado('id_roll', app.infoRegistro.get('id_roll'));


    /*-------------- BOTON GUARDAR CAMBIOS --------- */
    if ( $('#guardarCambios').length > 0 ){
        $("#guardarCambios span").text('Sin cambios'); 
        $('#guardarCambios').attr('disabled', true);
        $("#guardarCambios svg").addClass('elementoOculto');
    }

    $($campos).removeClass('is-valid').removeClass('is-invalid');
    return true;
     
}

/**
 * Estar a la escucha de cuando se elimina la foto avatar
 * Si se elimina se tiene que actualizar el listener de inputs
 */
let $foto = document.querySelector('#foto');
const observer = new MutationObserver(function(mutations) {
  mutations.forEach(function(mutation) {
    if (mutation.type === "attributes" && mutation.attributeName === 'data-foto') {
        if(!$foto.getAttribute('data-foto')){
            $($foto).change()
            
        }
    }
  });
});

observer.observe($foto, {attributes: true //configure it to listen to attribute changes
});

const resetRenderInfoTransportista = () =>{
    const id_form = document.querySelector('#addUpdate form').getAttribute('id');
    if(id_form == 'editarRegistro'){
        /*------------------------ ID USUARIO Y FABRICANTE -------------- */
        const $id_usuario = document.getElementById('id_usuario');
        const $id_transportista = document.getElementById('id_trans');
        if($id_usuario) $id_usuario.remove();
        if($id_transportista) $id_transportista.remove();

        /*------------- RENDERIZAR AVATAR EN EL HEADER ------------- */
        const $icon_header = document.querySelector('#addUpdate .icon-header');
        Array.from($icon_header.children).forEach($child =>{
            $child.classList.remove('elementoOculto');
        });
        let avatar = document.querySelector('#addUpdate .icon-header .avatar');
        if(avatar) avatar.remove();

        /*------------------------ TITULO VENTANA Y SUBTITULO -------------- */
        document.querySelector('#addUpdate .header').textContent = `Registra un nuevo transportista`;
        document.querySelector('#addUpdate .row-header p').textContent = 'Rellena el formulario para registrar al transportista';
        /*--------- DATA-FOTO INPUT FOTO -----------*/
        const $foto = document.getElementById("foto");
        if($foto.hasAttribute('data-foto')) $foto.removeAttribute('data-foto');

        /*----------- INPUTS ------------- */
        const $inputs = Array.from(document.querySelectorAll('#addUpdate form [name]'));
        $inputs.forEach($input => {
            // quitar clase oculta si tiene
            if($input.classList.contains('elementoOculto')){
                $input.classList.remove('elementoOculto');
            }
            // poner clase input si no tiene
            if(!($input.classList.contains('input'))){
                $input.classList.add('input');
            }
            // mostrar los divs de inputs password
            if($input.getAttribute('type') === 'password'){
                $input.parentElement.parentElement.classList.remove('elementoOculto');
            }

        });
        // quitar id formulario
        document.querySelector('#addUpdate form').removeAttribute('id');
    }
    

    return
}

/*................---------------------------------- 
    EDITAR FABRICANTE
---------------------------------------------------*/

const editarTransportista = async ($id = null) =>{
    if(!$id || !contieneSoloNumeros($id)){
        console.warn('Id de fabricante no valido...');
        return
    }
    document.querySelector('#addUpdate form').setAttribute('id', 'editarRegistro');
    let render = await renderizarInfoTransportista($id);
    if(!render) return;
    $('#addUpdate').offcanvas('show');

    $('#editarRegistro').off('submit').on('submit', async (event) =>{
        event.preventDefault();
        const excluir = ['id_trans', 'id_usuario'];
        let update = await app.seleccionarDatosAActualizar(excluir);
        if(!update){
            console.warn('No se puede proceder a actualizar')
            return
        }
        
        //const actualizar = update.actualizar;
        const noActualizar = Array.from(update.noActualizar.keys());
        noActualizar.forEach(id =>{
            let $input = document.getElementById(id);
            $input.classList.remove('input');
        });

        //let $campos = $($formulario).find('.input:not(div)');console.log(noActualizar)
        let validaccion = validarInput(app.formulario.find('.input:not(div'));
        if(!validaccion.exito){
            console.warn('El formualario contiene errores');
            return;
        }

        // todo ok submit formualio
        let respuesta = await app.submitFormulario(app.formulario);
        if(respuesta){
            noActualizar.forEach(id =>{
                let $input = document.getElementById(id);
                $input.classList.add('input');
            });
            setTimeout(()=>{
                $.each($(app.formulario).find('.input:not(div'), function($indice, $campo){
                    $($campo).removeClass('is-invalid').removeClass('is-valid');
                });
            }, 100);
        }
        if(respuesta.exito == true){
           // $('#_token').val('');
            editarTransportista( $('#id_trans').val() );
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

/*----------------------------------------------
            ELIMINAR FABRICANTE
----------------------------------------------*/
const eliminarTransportista = async(id = null) =>{
    if(!id || !contieneSoloNumeros(id)){
        mostrarNotificaciones({
            tipo: 'aviso',
            titulo: 'ID no valido',
            mensaje: 'El id seleccionado no tiene un formato valido'
        });

        return;
    }
    // traer registro
    let info = await app.traerResgistros(id);
    if(!info.exito) {
        mostrarNotificaciones({tipo: 'error',titulo: info.titulo,mensaje: info.mensaje
        });
        return;
    };
    const data = info.datos;
    //------------ RENDERIZACIONES -------------
    $('#modalEliminar').modal('show'); // mostrar ventan confirmacion
    // mensaje de advertensia
    $('#smsAdvertencia').text('¿ Quieres eliminar la información de '+ data.nombre +' ?. Si eliminas este registro, los cambios realizados no se podrán deshacer.')
    $('#confirmarEliminacion').attr('data-bs-original-title',`Borrar ${capitalizarPrimeraLetra(data.nombre.split(" ")[0])}` );
    $('#confirmarEliminacion').text(`Eliminar a ${capitalizarPrimeraLetra(data.nombre.split(" ")[0])}`);
    $('#confirmarEliminacion').tooltip();
    $('#footerModalEliminar').after(`
        <input type="hidden" name="id_registro" id="id_registro" value="${id}" /> 
        <input type="hidden" name="id_usuario" id="id_usuario" value="${data.id_usuario}" />
        <input type="hidden" name="fotoEliminar" id="fotoEliminar" value="${(data.foto) ? data.foto: ''}" />
        `);

    $('#confirmarEliminacion').unbind('click').bind('click', async  (event) => {
        event.preventDefault(); event.stopPropagation();
        let $id = $('#id_registro').val(); // id o key del registro
        let dataExtra = {id_usuario: $('#id_usuario').val(), foto:( $('#fotoEliminar').val() ) ? $('#fotoEliminar').val(): ''}

        $('#confirmarEliminacion').attr('disabled', true);
        $('#confirmarEliminacion').text('Eliminado...');
        // elimianar registro
        let respuesta = await app.eliminarRegistro($id, dataExtra);
        if(respuesta.exito === true){
            app.tablaDatos.ajax.reload(null, false);
            // quitar input id registro del html
            $('#modalEliminar').find('input').remove();
            //$('#id_registro').remove(); 
            $('#modalEliminar').modal('hide'); // ocultar ventana confirmacion
        }else{
            $('#confirmarEliminacion').text('Volver a intentar...');
            $('#confirmarEliminacion').attr('disabled', false);
        }
            // mostrar notificacion
        mostrarNotificaciones({
            tipo: (respuesta.exito) ? 'exito' : 'aviso',titulo: respuesta.titulo,mensaje: respuesta.mensaje});
    });
}