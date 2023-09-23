/**
 * modulos js de la app
 */

/**
 * muestra u oculta la constraseña en un input
 * @param {Object{boton, input, svg ocultarClave, svg mostrarClave}} $opciones 
 */
export function mostrar_ocultar_clave( $opciones ){
    if ( $opciones && typeof($opciones) === 'object'){
        const $boton = $opciones.boton;
        const $input = $opciones.input;
        const $ocultarClave = $opciones.ocultarClave;
        const $mostrarClave = $opciones.mostrarClave;

        if($input.attr('type') == 'password'){
            $input.prop('type', 'text');
            //svg ver clave
            $($ocultarClave).removeClass('elementoOculto');
            //svg ocultar clave
            $($mostrarClave).addClass('elementoOculto');
            $($boton)[0].dataset.bsOriginalTitle ='Ocultar clave';
            
        }else{
            $($input).prop('type', 'password');
            //svg ver clave
            $($ocultarClave).addClass('elementoOculto');
            //svg ocultar clave
            $($mostrarClave).removeClass('elementoOculto');
            $($boton)[0].dataset.bsOriginalTitle ='Mostrar clave';
        }
        
    }else{
        console.error('ALGO SALIO MAL---------------')
    }
}

/**===========================================================================================
 * mostrara notificaciones en la app
 * recibe como parametro un objeto con las opciones
 * @param {object{tipo: String, titulo: String, mensaje : Array | String}} $opciones 
 **========================================================================================*/
export function mostrarNotificaciones($opciones){
    //$opcines tiene que ser un objeto
    if ($opciones && typeof($opciones) == 'object'){
        if (Object.keys($opciones).length == 3){ //el objeto tiene que tener 3 keys
            const $tipos = { // tipos de notificaciones vs nombre de su clase y su icono;
                info : {
                    nombreClase: 'alert-info',
                    icono : '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12.01" y2="8"></line><polyline points="11 12 12 12 12 16 13 16"></polyline></svg>'
                }, 
                exito: {
                    nombreClase: 'alert-success',
                    icono : '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>'
                },
                error :{
                    nombreClase: 'alert-danger',
                    icono: '<svg xmlns="http://www.w3.org/2000/svg" class="icon " width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>'
                },
                aviso: {
                    nombreClase: 'alert-warning',
                    icono: '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v2m0 4v.01"></path><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"></path></svg>'
                }
            } //fin $tipos

            //verificar keys
            if ('tipo' in $opciones && 'titulo' in $opciones && 'mensaje' in $opciones){
                const $tipo = $opciones.tipo; //tipo de notificacion a mostrar
                const $titulo = $opciones.titulo; //tito de la notificacion
                const $mensaje = $opciones.mensaje; //mensaje a mostrar

                //el tipo tiene que existir en el objeto de tipos de notificaciones
                if ($tipo in $tipos){
                    if($('#notificacionApp').length > 0 ){ //compruebar si ya existe en el DOM

                        //si la notificacion es distinta a la que hay que poner
                        if( !($('#notificacionApp').hasClass($tipos[$tipo].nombreClase)) ){
                            //loop sobre los tipos => su clase
                            Object.values($tipos).forEach( function($item){
                                //remover la clase a la notificacion cuadno se encuentre
                                if ( $('#notificacionApp').hasClass($item.nombreClase) ){
                                    $('#notificacionApp').removeClass($item.nombreClase);
                                }
                            });

                            //añadirle su clase
                            $('#notificacionApp').addClass($tipos[$tipo].nombreClase);
                            //quitar el icono que tenia
                            $('#iconoNotificacion svg').remove();
                            //poner su icono
                            $('#iconoNotificacion').append($tipos[$tipo].icono);
                            //poner titulo y mensaje
                            $('#tituloNotificacion').text($titulo);

                            //mensaje o mensajes
                            $('#mensajeNotificacion p').remove(); // sacar del dom p con mensajes
                            $('#mensajeNotificacion').append(leerArrayMensajes($mensaje)); // poner nuevos sms
                            $("#notificacionApp").delay(50).show(10, function() {
                                //mostrar
                                $(this).addClass('show');
                                //ocultar y eliminar del dom
                                $(this).delay(6000).hide(5, function() {
                                    $(this).remove();
                                });
                            });
                            
                        }else{ // si es la misma clase, solo modificar el titulo y sms

                             //titulo y mensaje
                             $('#tituloNotificacion').text($titulo);
                            //mensaje o mensajes
                             $('#mensajeNotificacion p').remove(); // sacar del dom p con mensajes
                             $('#mensajeNotificacion').append(leerArrayMensajes($mensaje)); // poner nuevos sms
                             $("#notificacionApp").delay(50).show(10, function() {
                                //mostrar
                                $(this).addClass('show');
                                //ocultar y eliminar del dom
                                $(this).delay(6000).hide(5, function() {
                                    $(this).remove();
                                });
                            });
                        }
                        
                        
                    }else{
                        //no existe el elemento en dom - crearlo
                        const $notificacion = `<div class="alert ${$tipos[$tipo].nombreClase}  alert-dismissible custom-alert" role="alert" id="notificacionApp">
                        <div class="d-flex">
                                <div id="iconoNotificacion">
                                    <h3> ${$tipos[$tipo].icono /*icono de la notificacion*/}</h3>
                               
                                </div>
                                <div>
                                <h4 class="alert-title" id="tituloNotificacion" >${$titulo}</h4>
                                <div class="text-muted" id="mensajeNotificacion" >
                                ${leerArrayMensajes($mensaje) /* leer los mensajes y los mostrara aqui*/}
                                </div>
                                </div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                            </div>`;
                            
                            //clase css de la notificacion


                            $('body').append($notificacion); //al body
                            //mostrar y ocultar la notificacion
                            $("#notificacionApp").delay(50).show(10, function() {
                                //mostrar
                                $(this).addClass('show');

                                //ocultar y eliminar del dom
                                $(this).delay(7000).hide(50, function() {
                                    $(this).remove();
                                });
                            }); 

                    } //end if else compruebar si ya existe en el DOM

                }else{

                    //el tipo no esta en el objeto de tipos
                    console.warn(`NOTA:  ${$tipo}  No es un tipo valido de notificación, prueba usar => info, exito, error o aviso.`);
                } // end if else tipo in tipos
                

            } else {
                console.error('las key del objeto tienen que ser: tipo, titulo y mensaje');
            } //end if else verificar keys
        }else {
            console.error('Esta función recibe un objeto de 3 keys');
        } //end if else numero de keys del objeto recibido

    }else{
        //el parametro no es un objeto
        console.error('La funcion mostrarNotificaciones recibe como parametro un objeto');
    } //end if else $opciones tiene que ser un objeto

}

/*******************************************************
 * leerArrayMensajes recibe un array de mensajes o string , 
 * lo recorre y devuelve un elemento p por cada mensaje
 * @param {string | Array} $mensaje 
 * @returns <p> mensaje a mostrar </p>
 *******************************************************/
function leerArrayMensajes($mensaje){
    // si $mensaje es array, recorrerlo para mostrar los mensajes
    if($mensaje instanceof Array) {
        // recorrer el array de mensajes
        let $p = '';
        for(let i= 0; i < $mensaje.length; i++){
            $p += `<p> <span>&#8227;</span> ${$mensaje[i]}</p>`;    
        } 
        return $p;
        
    } else {
        return `<p> <span>&#8227;</span> ${$mensaje}</p>`; //teronarnar simplemente el mensaje, no es array
        
    }

}

/**=================================================
 * verifica que el email recibido tenga un formato valido
 * @param {*} $mail 
 * @returns bool: true | false
 */
export function ValidarEmail($email) {
    if($email){
        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($email) ){
            return (true);
        }else{
           return (false);
        } 
    }else {
        return false;
    }
 
}

export function contieneSoloLetras(input = null){ 
    var patron = /^[A-Za-z-ñÑ]+$/;
    if(input.match(patron)){
        return true;
    } else {
        return false;
    }
}

/**
 * verifica que un input solo contiene letras
 * @param {*} input 
 * @returns bool
 */
export function contieneSoloLetrasEspacios(input){ 
    var patron = /^[A-Za-z-ñÑ' ]+$/;
    if(input.match(patron)){
        return true;
    } else {
        return false;
    }
}

/**
 * Verifica que solo contenga numeros
 * @param {*} input 
 * @returns bool
 */
export function contieneSoloNumeros(input) {
      var formato = /^[0-9]+$/;
      if(input && input.match(formato)) {
            //document.form1.text1.focus();
        return true;
      } else {
        return false;
      }
}

/**
 * contieneSoloNumerosLetras
 * valida si un string contiene solo numeros y letras
 * @param {*} input 
 * @returns bool true o false
 */
export function contieneSoloNumerosLetras(input) {
    var formato = /^[A-Za-z0-9]+$/;
    if(input.match(formato)) {
      return true;
    } else {
      return false;
    }
}
/**
 * valida sin un string contiene solo numeros y espacios
 * @param {*} input 
 * @returns bool true o false
 */
export function contieneSoloNumerosEspacios(input){ 
    var patron = /^[0-9-' ]+$/;
    return (input.match(patron)) ? true : false;  
}

export function contieneTexto(texto){
    const patron = /^[a-zA-Z-ñÑ.&@á-úÁ-Ú' ]+$/;
    return (texto.match(patron)) ? true : false;  
}

/**
 * valida si una url es valida
 * @param {*} url 
 * @returns bool: true o false
 */
export function urlWebValida(url){
    var patron = /\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i;
    if(url.match(patron)){
        return true;
    }else {
        return false;
    }
}


/**
 * redirecciona a una nueva url despues de transcurrir el tiempo indicado en $tiempoEspera
 * @param {*} $url : url a la que se quiere redireccionar
 * @param {*} $tiempoEspera : tiempo que hay que esperar para redireccionar
 */

export function Redireccionar($url, $tiempoEspera) {
    setInterval(redireccion, $tiempoEspera);
    function redireccion() {
        window.location.href = $url; 
    }
    
}


/**
 * recibe una palabra o frase y convierte la primera letra de cada palabra en mayuscula
 * @param { string } $string string a captitalizar
 * @returns 
 */
export function capitalizarPrimeraLetra($string = null){
    //console.log($string.toLowerCase())
    var $stringCapitalizado = '';
    var $string = $string.toLowerCase();

    if( $string.length > 0 ) {
        var $arrayLetras = $string.split(" ");   
        for (var $i = 0; $i < $arrayLetras.length; $i++) {
            $arrayLetras[$i] = $arrayLetras[$i].charAt(0).toUpperCase() + $arrayLetras[$i].slice(1);
        }
        // Unir otra vez todos los elementos del array a un string utilizando como separador un espacio en blanco 
        $stringCapitalizado = $arrayLetras.join(" ");
        
    }else{
        $stringCapitalizado = $string;
    }

    return $stringCapitalizado;
}

export async function generarAvatar(config = null){
    if(!config || typeof config  !== 'object') return false;
    let avatar;
    if(config.tipo == 'text'){
        let texto = config.texto;
        let abv = texto.split(" ");
        let iniciales = '';
        if(abv.length > 1){
          for(let j=0; j < abv.length; j++){
            iniciales += abv[j].toString().substring(0,1);
          }
          
        }else{
          iniciales = texto.substring(0,2).toUpperCase(); 
        }
        if(!iniciales) return false;
        let bg = '';
        if(iniciales.length > 3){
            bg = 'bg-yellow-lt';
        }else if(iniciales.length == 3){
            bg = 'bg-blue-lt';
        }else if (iniciales.length == 2){
            bg = 'bg-green-lt';
        }

        avatar = `<span class='avatar rounded-circle ${bg}'>${iniciales}</span>`
        
        return avatar;
    }

    if(config.tipo == 'img'){
        let url = config.url;
        if(!url) return false;
        avatar = `<span class="avatar rounded-circle bg-transparent" style="background-image: url(${url})"></span>`;
        return avatar;
    }

    return false;
}

/**
 * recibe dos objetos de tipos map y verifica si son iguales (keys y valores)
 * @param {Object} map1 //opbjeto map numero 1
 * @param {Object} map2 // objeto map numero 2
 * @returns @boolean : true | false
 */
export function compararObetosMap(map1, map2) {
    let valorPrueba; // valor de testeo
    //console.log(map1,': MAP:2 ', map2)
    if (map1.size !== map2.size) { //comparar tamaño
        return false; 
    }
    for (var [llave, valor] of map1) {
        valorPrueba = map2.get(llave); 
        // En caso de valor no definidos(undefined) asegurarse que la llave(key) .
        // en realidad exite en el objeto, so no habrá falsos positivos
        // coincidencia exacta => FR = fr
        if( isNaN(valorPrueba)){ // si es string hacer la comparacion con miniscular
            if (valorPrueba.toLowerCase() !== valor.toLowerCase() || (valorPrueba === undefined && !map2.has(llave))) {
                return false;
            }
        }else{ // es un numero, toLowecase generaria error
            if (valorPrueba !== valor || (valorPrueba === undefined && !map2.has(llave))) {
                return false;
            }
        }

    }

    return true;
}
/**
 * Recibe un array a ordenar y lo ordena segun el campo que se pase a la funcion
 * @param {Array} $array : array a ordenar
 * @param {string} campo : campo de ordenacion 
 * @returns @array si todo ok o @bool false si hay algun error
 */
export function ordenarArray($array = null, campo = null){
    if (typeof $array == 'object' && typeof campo == 'string'){
        const campo_ordenacion = campo;
        function orderPorColumna(a, b, c = campo_ordenacion) {
            // check si el campo de ordenacion existe como key
            if(c in a && c in b){
                // check si el campo de ordenacion es de tipo string
                // si es, convertir a miniscula y hacer las comparaciones
                if(typeof a[c] == 'string' && typeof b[c] == 'string'){
                    if (a[c].toLowerCase() === b[c].toLowerCase()) {
                        return 0;
                    }
                    else {
                        return (a[c].toLowerCase() < b[c].toLowerCase()) ? -1 : 1;
                    }
                }else{ return (a[c] === b[c]) ?  0 :  (a[c] < b[c]) ? -1 : 1;}

            } else { 
                console.error(`${c} no existe como clave...`)
                return null;
            }
        }

        return $array.sort(orderPorColumna);


    }else{
        console.warn('Se esperaba un objeto/array')
        return false;
    }

}

/**
 *  Toma un string y lo sanitiza
 * @param {String} $string 
 * @returns String sanitizado
 */
export function sanitizarInputs( $string ) {
    const caracteres_especiales = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#x27;',
        //"/": '&#x2F;',
    };
    const $regExp = /[&<>"']/ig;
    return $string.replace($regExp, (match)=>(caracteres_especiales[match]));
  }
/**
 * 
 * @param {selector de la tabla: $('#idTabla')} tabla 
 * @param {string: url de datos} $url 
 * @param {string: post o get} $method 
 * @param {objeto: {accion:'..', datos:{..}}} $data 
 * @returns configuracion de la tabla
 */
export function inicializarTablaDatos(tabla, $url, $method, $data ){
    let $configuracion;
    let $id_tabla = tabla.attr('id');
    if( sanitizarInputs($url) && sanitizarInputs($method) && typeof($data) == 'object' && $id_tabla ){
        $configuracion = {
            "ajax":{
                "url": $url,
                "type": $method,
                "cache": true,
                "data": $data
            },

            "responsive": true,
            "select": true,
            "autoWidth": false,
            "lengthChange": true,
            "rowReorder": true,
            "columnDefs": [
                { orderable: false, className: 'reorder', targets: [0,-1] },
                { orderable: true, targets: '_all' }
            ],
            "oLanguage": {
                "sProcessing":     "Procesando...",
                  "sLengthMenu": ' <select class="custom-select" title="Registros a mostrar por página">'+
                      '<option value="10">Ver 10 Registros.</option>'+
                      '<option value="20">Ver 20 Registros.</option>'+
                      '<option value="30">Ver 30 Registros.</option>'+
                      '<option value="40">Ver 40 Registros</option>'+
                      '<option value="50">Ver 50 Registros</option>'+
                      '<option value="100">Ver 100 Registros</option>'+
                      '<option value="150">Ver 150 Registros</option>'+
                      '<option value="200">Ver 200 Registros</option>'+
                      '<option value="-1">Ver Todos</option>'+
                      '</select>',    
                  "sZeroRecords":    "No se encontraron resultados",
                  "sEmptyTable":     "Ningún dato disponible en esta tabla",
                  "sInfo":           "Mostrando (_START_ a _END_) de _TOTAL_ registros",
                  "sInfoEmpty":      "Mostrando 0 a 0  de 0 registros",
                  "sInfoFiltered":   "(filtrados de _MAX_ registros)",
                  "sInfoPostFix":    "",
                  "sSearch":         "",
                  sSearchPlaceholder:"Escribe para buscar...",
                  "sUrl":            "",
                  "sInfoThousands":  ",",
                  "sLoadingRecords": "Por favor espere - cargando...",
                  "oPaginate": {
                        sFirst:"Primero",
                        sLast:"Ultimo",
                        sNext:"Siguiente",
                        sPrevious:"Anterior"
                  },
                  "oAria": {
                      "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                      "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                  }
            },
            "buttons": [
                /*{
                    extend: 'csv',
                    text: '<img src="public/icons/csv-80.png" class="icono"></img> <span>Csv</span>',
                    titleAttr: 'Exportar a un archivo csv'
                    
                }, 
                {
                    extend: 'excel',
                    text: '<img src="public/icons/excel.png" class="icono"></img><span>Excel</span>',
                    titleAttr: 'Exportar a hoja de Excel'
                    
                }, 
                {
                    extend: 'pdf',
                    text: '<img src="public/icons/pdf-80.png" class="icono"></img><span> Pdf</span>',
                    titleAttr: 'Exportar en formato pdf'
                    
                }, 
                {
                    extend: 'print',
                    text: '<img src="public/icons/imprimir-40.png" class="icono"></img><span>Imprimir</span>',
                    titleAttr: 'Imprimir todos los registros'
                    
                },*/
                {
                    extend: 'colvis',
                    text: '<span>Columnas visibles</span>',
                    titleAttr: 'Columnas visibles'
                    
                }
                
            ],
            initComplete: function () {
               tabla.DataTable().buttons().container().appendTo('#'+$id_tabla+'_wrapper .col-auto:eq(0)'); 
            }
        }

    }

    return $configuracion;

}


export function formatDate(fecha, formato){
    if(!fecha || !formato) return false;
    let delimiter = null;
    let formatEs = /[Dd]{2}[-:/]{1}[Mm]{2}[-:/]{1}[Yy]{4}/;
    let formatEn = /[Yy]{4}[-:/]{1}[Mm]{2}[-:/]{1}[Dd]{2}/; 

    fecha = new Date(fecha);
    let dia = fecha.getDate(); //x
    let mes = fecha.getMonth() + 1; // porque enero = 0
    let year = fecha.getFullYear(); // xxxx
    
    dia = (dia < 10 ) ? `0${dia}` : dia;
    mes = (mes  < 10 ) ? `0${mes}` : mes;
    //fecha = dia + mes + year;
    
    if(formato.match(formatEs)){
        delimiter = formato.substring(2,3);
        return `${dia}${delimiter}${mes}${delimiter}${year}`;
    }

    if(formato.match(formatEn)){
        delimiter = formato.substring(4,5); 
        return `${year}${delimiter}${mes}${delimiter}${dia}`;
    }

    console.warn('FORMATO DE FECHA NO SOPORTADO')
    return false;

}


/**
 * valida los input de un formulario
 * @param {html collection : campos de un formulario} $inputs 
 * @returns resultado{ exito: true | false, validaciones: {campo: {valido : true | false} }, mensajes:[]}
 */
export function validarInput($inputs = null){
    if($inputs && typeof $inputs == "object"){
        let $resultado = { exito: false, validaciones:{}, mensajes: []} // almacenara todas la validaciones de los inputs
        $.each($inputs, function($index, $input){
            let $valor = $($input).val().trim();
            let $nombre =  capitalizarPrimeraLetra($($input).attr('name'));
            let $required = $($input).attr('data-required') || false; // requerido
            let $minLongitud = $($input).attr('data-min-length') || 0; // longitud minima: 0 => puede estar vacio
            let $maxLongitud = $($input).attr('data-max-length') || 'any'; // longitud minima: 0 => puede estar vacio
            let $formato = $($input).attr('data-format') || ''; // longitud minima: 0 => puede estar vacio
            
            $resultado.validaciones[$nombre.toLowerCase()] = { valido: false}
            let $valid_required = false;
            let $valid_min_longitud = false;
            let $valid_max_longitud = false;
            let $valid_formato = false;
            
            // valor required
            if( $required == 'true' && $valor.length <= 0 ){
                $resultado.mensajes.push(`El campo ${$nombre} es obligatorio`);
                $valid_required = false;
            }else{
                $valid_required = true;
                
            }

            // min longitud
            if ($minLongitud > 0 && ($valor.length < $minLongitud) ){
                $resultado.mensajes.push(`El campo ${$nombre} debe tener como mínimo ${$minLongitud} carracteres`);
                $valid_min_longitud = false;
            }else{
                $valid_min_longitud = true;
            }

            // max longitud
            if ( ($maxLongitud != 'any' || $maxLongitud != '') && ($valor.length > $maxLongitud) ){
                $resultado.mensajes.push(`El campo ${$nombre} debe tener como máximo ${$maxLongitud} carracteres`)
                $valid_max_longitud = false;
            }else{
                $valid_max_longitud = true;
            }

            //formato
            
            if($formato != 'any' && $formato != '' ){
               
                //validar solo texto: string  
                if($formato == 'letters' ){
                    if( $valor.length > 0 && !(contieneSoloLetras($valor)) ){
                        $resultado.mensajes.push(`El campo ${$nombre} solo debe contener letras`);
                        $valid_formato = false;
                    }else {
                        $valid_formato = true;
                    }
                } 
                //validar solo texto: textos largos con carracteres  
                if($formato == 'text' ){
                    if( $valor.length > 0 && !(contieneTexto($valor)) ){
                        $resultado.mensajes.push(`El campo ${$nombre} admite texto con carracteres`);
                        $valid_formato = false;
                    }else {
                        $valid_formato = true;
                    }
                }      
                
                // validar solo numeros
                if($formato == 'numbers'){
                    if($valor.length > 0 && !(contieneSoloNumeros($valor)) ){
                        $resultado.mensajes.push(`El campo ${$nombre} solo debe contener números`);
                        $valid_formato = false;
                    }else {
                        $valid_formato = true;
                    }
                }
                
                // validaar numeros y letras
                if($formato == 'numbers+letters'|| $formato == 'letters+numbers'){
                    if( $valor.length > 0 && !(contieneSoloNumerosLetras($valor)) ){
                        $resultado.mensajes.push(`El campo ${$nombre} solo debe contener numeros y letras`);
                        $valid_formato = false;
                    }else {
                        $valid_formato = true;
                    }
                }
                
                //validar texto mas espacios
                if($formato == 'spaces+letters'|| $formato == 'letters+spaces'){
                    if( $valor.length > 0 && !(contieneSoloLetrasEspacios($valor)) ){
                        $resultado.mensajes.push(`El campo ${$nombre} solo debe contener letras y espacios`);
                        $valid_formato = false;
                    }else {
                        $valid_formato = true;
                    }
                }
                
                //validar numeros + espacios
                if($formato == 'spaces+numbers'|| $formato == 'numbers+spaces'){
                    if($valor.length > 0 && !(contieneSoloNumerosEspacios($valor)) ){
                        $resultado.mensajes.push(`El campo ${$nombre} solo debe contener números y espacios`);
                        $valid_formato = false;
                    }else {
                        $valid_formato = true;
                    }
                }
                
                //email
                if($formato == 'email'|| $($input).prop('type') == 'email' ){
                    if($valor.length > 0 && !(ValidarEmail($valor)) ){
                        $resultado.mensajes.push(`El campo ${$nombre} debe tener un formato de email correcto`);
                        $valid_formato = false;
                    }else {
                        $valid_formato = true;
                    }
                }
                
                // url web
                if($formato == 'web'|| $($input).prop('type') == 'url' ){
                    if($valor.length > 0 && !(urlWebValida($valor)) ){
                        $resultado.mensajes.push(`El campo ${$nombre} debe tener un formato de url valida.`);
                        $valid_formato = false;
                    }else {
                        $valid_formato = true;
                    }
                }

                
                
            } else {
                $valid_formato = true;
            }

            // set validacion del input
            if($valid_required && $valid_min_longitud && $valid_max_longitud && $valid_formato){
                $resultado.validaciones[$nombre.toLowerCase()].valido = true;
            }else{
                $resultado.validaciones[$nombre.toLowerCase()].valido = false; 
            }

        });

        // validaciones de los inputs : si alguno esta en false: no hay exito
        for (let x in $resultado.validaciones){
            if ($resultado.validaciones[x].valido == false){
                $resultado.exito = false;
                break;
            }else{
                $resultado.exito = true;
            }   
        }

        return $resultado;
    }
               
}

/**
 * valida el formulario de registro y manda los datos al backend para ser insertados en la BD
 * @param {object} $formulario : formulario a validar
 * @param {object} $tabla : tabla de datos  
 */
export function insertarRegistro( $formulario, $tabla ){
    if (typeof $formulario == 'object' && typeof $tabla == 'object'){

        $('#modalCreateUpdate').modal('show'); // mostrar modal
        $formulario.prop('id','insertarRegistro'); // cambiar id del formulario
        
        //campos del formulario
        let $campos = $formulario.find('input');
        if ( $campos.length > 0 ){
            //==> botones del formulario
            $('#guardarCambios svg').addClass('elementoOculto');
            $('#guardarCambios').attr('disabled', true);
            $("#guardarCambios span").text('Sin datos que guardar');
            $("#divBotonesFormulario .btn-delete").addClass('elementoOculto'); // hide, no se necesita aqui
        
            //header del modal
            $('#headerModal').addClass('elementoOculto'); //ocultar
        
            //==>> contenido modal
            // panel notificacion
            $('#panelAviso').text('Rellena los campos del formulario para insertar un nuevo registro. Recuerda que los campos con * son obligatorios');
        
            // formulario de registro 
            $formulario.off('submit').on('submit', function(event){
                event.preventDefault();
                // enviar data al backed soolo si todo esta ok
                let $validacion = validarInput($campos).exito == true
                if(   $validacion.exito == true ){
                    let form = $(this);
                    let datos = form.serialize();
                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: {accion: 'registrar', datos : datos },
                        dataType: 'json',
                        beforeSend: function (){
                            $('#guardarCambios span').text('Registrando datos...');
                        }, 
                        success: function($respuesta){
                            // quitar las clases valido-invalido a los input
                            $($campos).removeClass('is-invalid').removeClass('is-valid');
                            if ($respuesta.exito == true ){ // todo ok, registro insertado
                                $formulario[0].reset(); // resetear form
                                // quitar bandera del pais registrado: para paises
                                if($("#bandera").length > 0){
                                    let $bandera_actual = $("#bandera").attr('class').substring(13);
                                    $('#bandera').removeClass($bandera_actual).addClass('flag-country-xx');
                                }
                                $tabla.ajax.reload(null, false); // recargar tabla de datos
                                $('#guardarCambios span').text('Sin datos que guardar');
                                $('#guardarCambios').attr('disabled', true); //boton desahabilitado
        
                                mostrarNotificaciones({
                                    tipo: 'exito',
                                    titulo: '¡ Operación exitosa !',
                                    mensaje: $respuesta.mensaje
                                });
                                
                            } else {
                                $('#guardarCambios span').text('Volver a intertar');
                                $('#guardarCambios').attr('disabled', false);
                                mostrarNotificaciones({
                                    tipo: 'error',
                                    titulo: '¡ Algo ha salido mal !',
                                    mensaje: $respuesta.mensaje
                                });
                            }
        
                        },
                        error: function(request, error){ // error ajax
                            $('#guardarCambios span').text('Volver a intertar');
                            $('#guardarCambios').attr('disabled', false);
                            mostrarNotificaciones({
                                tipo: 'error',
                                titulo: '¡ Error de Servidor !',
                                mensaje: `Ha ocurrido un error en la petición al servidor para insertar el registro, vuelve a intentarlo. ${error}`
                            });
                        
                        } // fin ajax error
                    }); // ajax request insertar resgistro
                } else{ // if else validarInputs formulario
                    mostrarNotificaciones({
                        tipo: 'error',
                        titulo: '¡ Hay campos con errores !',
                        mensaje: $validacion.mensajes
                    });
                } // fin if else validarInput formulario

            }); // fin on submit formulario
      
        } else{ // if else campos
            mostrarNotificaciones({
                tipo: 'error',
                titulo: '¡ Formulario sin campos !',
                mensaje: 'No se ha encontrado campos para el formulario proporcionado.'
            });
        } // fin if else campos
        
    } else{
        mostrarNotificaciones({
            tipo: 'aviso',
            titulo: '¡ Paramatros no válidos!',
            mensaje: 'Los párametros pasados a la función insertarRegistro tienen que ser objetos.'
        })
    }
    
}

/**
 * actualizar la información de un registro
 * @param {object} $formulario 
 * @param {object} $tabla 
 */
export function editarRegistro($formulario, $tabla){
    if (typeof $formulario == 'object' && typeof $tabla == 'object'){
        var $campos = $formulario.find('input');
        if ($campos.length > 0){
            // validacion de inputs del formulario
            let $validacion = validarInput($campos);

            if($validacion.exito == true){
                let datos = $formulario.serialize();
                $.ajax({
                    action: $formulario.attr('action'),
                    method: 'POST',
                    data: {accion: 'actualizar', datos: datos},
                    dataType: 'json',
                    beforeSend: function(){
                        //$('#guardarCambios span').attr('disab');
                        $('#guardarCambios span').text('Actualizando pais...');
                        $('#guardarCambios').attr('disabled', true);
                    },
                    success: function($respuesta){
                        // quitar las clases valido-invalido a los input
                        $($campos).removeClass('is-invalid').removeClass('is-valid');

                        if($respuesta.exito == true ){ // todo ok
                            $('#guardarCambios span').text('Sin cambios...');
                            $tabla.ajax.reload(null, false);
                        }else { // ha habido al algun error
                            $('#guardarCambios span').text('Volver a intentar...');
                            $('#guardarCambios').attr('disabled', false);

                        }

                        // notificacion
                        mostrarNotificaciones({
                            tipo: ($respuesta.exito) ? 'exito': 'error',
                            titulo: ($respuesta.exito) ? '¡ Operación exitosa !': '¡ Algo ha salio mal !',
                            mensaje: $respuesta.mensaje
                        });

                    },
                    error: function(request, error){ // error ajax request
                        $('#guardarCambios span').text('Volver a intertar');
                        $('#guardarCambios').attr('disabled', false);
                        mostrarNotificaciones({
                            tipo: 'error',
                            titulo: '¡ Error de Servidor !',
                            mensaje: `Ha ocurrido un error en la petición al servidor para editar el registro, vuelve a intentarlo. ${error}`
                        });
                        
                    }
                }); // fin ajax request form

            
            }else{ // validación no exitosa
                mostrarNotificaciones({
                    tipo: 'error',
                    titulo: '¡ Formulario sin campos !',
                    mensaje: 'No se ha encontrado campos para el formulario proporcionado.'
                }); 
            } // fin if else validacion.exito == true
            
        }else{ // no hay campos de formulario
            mostrarNotificaciones({
                tipo: 'error',
                titulo: '¡ Formulario sin campos !',
                mensaje: 'No se ha encontrado campos para el formulario proporcionado.'
            });
        } // fin if else campos.lengt > 0

    }else{ // los parametros no son objetos 
        mostrarNotificaciones({
            tipo: 'aviso',
            titulo: '¡ Paramatros no válidos!',
            mensaje: 'Los párametros pasados a la función editarRegistro tienen que ser objetos.'
        })
    } // fin if else type of param == object
    
    
}


/**
 * Elimina registros de la base datos
 * @param {String | array} $registro 
 * @param {String} $url 
 * @param { ajaxtable } $tabla 
 * 
 */
export function eliminarRegistro($registro = null, $url = null, $tabla = null){
    if($registro && !isNaN(($registro)) && sanitizarInputs($url) ) {
        //asgurarse de que existe el registro que se quiere eliminar
        var solicitud = {
            tablaActualizar: $tabla, // tabla de datos en el html a actualizar
            verificarRegistro: function ( $registro, $url ) {
                var self = this; // << this es bindeado a la variable self
                $.ajax({
                    type: "POST",
                    url: $url,
                    data: {accion:'traer', registro: $registro},
                    dataType: 'json',
                    success: function($respuesta) { // hay data del backend
                        //console.log($respuesta)
                        const $expresion = 'data-id="'+$registro+'"';
                        //asegurarse de que exite el id del pais en la respuesta recibida del servidor - backend
                       if( $respuesta['data'] && $respuesta['data'][0][6].match($expresion) ){
                        // todo ok - continuar: next esperar confirmacion para eliminar
                            self.confirmar( $respuesta, $registro ); // mostrar ventana confirm    
                       } else { // error data recibida de la peticion traer info del registro
                        
                            //mostrarNotificacion
                            self.mostrarNotificacion(
                                'aviso', // tipo notificacion
                                '¡ Algo ha salido mal !', // titulo notificacion
                                'La respuesta recibida del servidor no incluye el id del pais seleccionado, vuelve a intentarlo.' // mesnaje
                            );
       
                       } // fin if respuesta data[0]

                    },// fin funcion success traer datos del registro
                        
                    error: function(result) { // funcion error ajax peticion traer datos del registro
                        self.mostrarNotificacion(
                            'aviso', // tipo notificacion
                            '¡ Algo ha salido mal !', //titulo
                            'La respuesta recibida del servidor no incluye el id del pais seleccionado, vuelve a intentarlo.' // mensaje
                        ) 
                        
                    }
                }); // fin ajax verificar y traer info registro

                
            }, // fin verificar id del registro
            confirmar: function($data, $registro){ // confirmar la eliminación del registro
                var $self = this; // hara referencia al objeto

                let $nombre_registro = $data['data'][0][3]; // nombre del pais
                $('#modalEliminar').modal('show'); // mostrar ventan confirmacion
                // mensaje de advertensia
                $('#smsAdvertencia').text('¿ Quieres eliminar la información de '+ capitalizarPrimeraLetra($nombre_registro) +' ?. Si eliminas este registro, los cambios realizados no se podrán deshacer.')
                $('#confirmarEliminacion').attr('data-bs-original-title',`Borrar ${capitalizarPrimeraLetra($nombre_registro)}` );
                $('#confirmarEliminacion').tooltip();
                $('#footerModalEliminar').after('<input type="hidden" name="id_registro" id="id_registro" value="'+$registro+'" /> ');

                // click boton confirmacion 
                $('#confirmarEliminacion').on('click', function (event){
                    event.preventDefault();
                    let $id = $('#id_registro').val(); // id o key del registro
                    $.ajax({
                        url: $url,
                        method: 'POST',
                        data: {accion: 'eliminar', registro : $id },
                        dataType: 'json',
                        beforeSend: function(){
                            console.log(`eliminando registro ${$id}`)
                            console.log($url);
                        },
                        success: function($respuesta){

                            // respuesta del backend -- 
                            if ($respuesta.exito == true){ // todo ha ido bien, registro eliminado
                                // tabla de datos: recargar 
                                let tabla = $self.tablaActualizar;
                                // recargar tabla de datos si existe
                                (tabla) ? tabla.ajax.reload(null, false): console.log('no table to reload');
                                $('#modalEliminar').modal('hide'); // ocultar ventana confirmacion
                                $('#id_registro').remove(); // quitar input id registro del html

                            }
                            // mostrar notificacion - exito o error
                            $self.mostrarNotificacion(
                                ($respuesta.exito) ? 'exito': 'error', // tipo notificacion
                                ($respuesta.exito) ? '¡ Registro eliminado !': '¡ Registro no eliminado !', // titulo 
                                $respuesta.mensaje // mesnaje 
                            )
                            
                        }, // fin success ajax peticion eliminar
                        error: function(request, error){ // error ajax peticion para eliminar registro
                            $self.mostrarNotificacion(
                                'error', // tipo notificacion
                                '¡ Error en la petición AJAX !', //titulo
                                `Ha ocurrido un error en la petición ajax mientras se intentaba eliminar el registro: ${error}` // mensaje
                            );


                        } // error: ajax peticicion eliminar
                    }); // fin ajax eliminar registro
                }); // click boton confirmar

            }, // fin confirmar eliminacion del registro
            mostrarNotificacion: function($tipo, $titulo, $mensaje){
               mostrarNotificaciones({
                tipo: $tipo, // tipo notificacion
                titulo: $titulo, // titulo notificacion
                mensaje: $mensaje // mensajes o sms a mostrar
               });
            }
        };
        
        // iniciar proceso de eliminacion primero verificando el id de registro
        solicitud.verificarRegistro($registro, $url);
            
    } else { // id o url con formato no ok
        mostrarNotificaciones({
           tipo: 'aviso',
           titulo: '¡ Error de parametros !',
           mensaje: 'Para eliminar un registro, se necesita un ID numerico y una URL. Vuelve a intentarlo' 
        })

    } // fin if else url id numerico
  
}