// ui
import {UI} from './ui.js';
const app = new UI();

$(document).ready(function(){

});
async function userAvatarInfo(){
    const parametros = new URLSearchParams();
    parametros.append('accion', 'avatar-info');
    let peticion = await axios.post('usuarios', parametros);
    const resultado = await peticion.data;
    $('#user_avatar_info').html(resultado);
}
userAvatarInfo();

const search_app = document.getElementById('search_app');

search_app.addEventListener('keyup', (event)=>{
    let valor = event.target.value.trim();
    if(valor && valor.length > 0 ){
        searchLinksSidebar(valor);
    }
    
});

const searchLinksSidebar = (valor) =>{
    const li = Array.from(document.querySelectorAll('#navbar-menu .navbar-nav .dropdown-item'));
    for(let i = 0; i < li.length; i++){
        //console.log(li[i].innerText, ':', li[i].parentElement.getAttribute('href'));
        //let a = Array.from( li[i].children );
        /*for(let j = 0; j< a.length; j++){
            console.log(a[j].innerText, a[j].getAttribute('href')); 
        }*/
    }
    console.log(li);
}

// listener cuando se elimina o se clickea en el btn para resetear el cargador de fotos
document.addEventListener('click', (event) => {
    event.stopPropagation();
    const $elemento = event.target; const $padre = $elemento.parentElement;
    let $target; 
    if( $elemento.classList.contains('cargador-archivos') || $padre.classList.contains('cargador-archivos')){
        $target = ($elemento.classList.contains('cargador-archivos')) ? $elemento : $padre;
        app.fileInputs($target);
        return
    }

    if( $elemento.classList.contains('eliminar-foto') || $padre.classList.contains('eliminar-foto')){
        $target = ($elemento.classList.contains('eliminar-foto')) ? $elemento : $padre;
        app.resetFileInputs($target);
        return
    }
    
    return ;
     
});

/**
 * 
*/

document.getElementById('modalEliminar').addEventListener('hide.bs.modal', () =>{
    // quitar input id registro del html
    $('#modalEliminar').find('input').remove();

});





/*
function fileInputs($cargador = null){
    if (!$cargador){return false;}
    // el contenedor del cargador de archivos
    const $preview = $cargador.parentElement;
    // el input type file oculto del formulario
    let $input = $cargador.nextElementSibling.getAttribute('id');
    $input = document.getElementById($input);
    

    if(!$preview || !$input){return false};

    // click input
    $input.click();
    // extensiones validas:  removeEventListener('change')
    const ext_validas = Array.from($input.getAttribute('data-allow-format').split(','));

    $input.addEventListener('change', (event) => {
        event.stopPropagation();
        const reader = new FileReader();
        const files = Array.from($input.files)
        const extension = files[0].name.split('.').pop() || false;
        
        if(!extension ){ 
            console.log('no extension');
            return false
        }
        if(!ext_validas.includes(extension.toLowerCase())){
            console.log('extension no valida:', extension);
            return false;
        }

        reader.onload = (e) =>{
            // ocultar cargador
            // mostrar imagen: 
            // el input file donde se carga, url de la imagen
            renderFileAvatar($input, e.target.result);
        }
        reader.readAsDataURL(files[0]);

    }, {passive: true,once: true}
    );

}

function renderFileAvatar($input = null, url = null){
    if(!$input || !url){return false;}
    // elemento que dispara la seleccion de foto
    let $cargador = $input.previousElementSibling;
    if (!$cargador){ return false;}

    $cargador.classList.add('elementoOculto');
    $input.parentElement.insertAdjacentHTML('afterbegin', '<div class="avatar-preview file-prewiew w-100" style="background-image: url(\''+url+'\');" rel="'+url+'"><span class="eliminar-foto"><i class="ti ti-trash-x"></i></span></div>');
    
}


function resetFileInputs($target = null){
    if($target){
        //previsualizador de foto
        let $preview = $target.parentElement;
        // el elemento que dispara la seleccion de foto
        let $cargador = $preview.nextElementSibling;
        // input tipo file
        let $input = $cargador.nextElementSibling;
        // div o elemento que cotiene los elementos anteriores
        const $container = $cargador.parentNode;
        if($cargador.classList.contains('elementoOculto')){
            $cargador.classList.remove('elementoOculto');
            $container.removeChild($preview);
            $input.value = '';
        }

        
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

*/