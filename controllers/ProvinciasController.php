
<?php

class ProvinciasController extends FileGeneratorController{
//su modelo
private $model;
private $validacion;

//constructor -> instacio su modelo
public function __construct(){
    $this->model = new ProvinciasModel();
    $this->validacion = new InputsValidacionController();
}
//leer
public function read( $id){
    ($id > 0 ) ? $id = sanitizarInput($id) : $id = 0;
    $provincia = $this->model->read($id);
    $output = array('data' => array());
    if (count($provincia)){
        for ($i= 0; $i < count($provincia) ; $i++) { 
            # code...
            $id = $provincia[$i]['id_provincia']; // id 
            $nombre = ucwords(strtolower($provincia[$i]['nombre'])); // nombre
            $ccaa = ($provincia[$i]['nombre_ccaa']) ? ucwords(strtolower($provincia[$i]['nombre_ccaa']))  : '<span class="badge bg-warning me-1"></span> No asignado';

            //checkbox seleccionar: para eliminacion multiple
            $checkbox = '<input class="form-check-input m-0 align-middle single-select" type="checkbox" aria-label="Seleccionar registro" data-id="'.$id.'" title="Seleccionar este registristro">';

            /*
                botones de opciones: editar y eliminar
            */
            $botonOpciones ='
                <td class="d-print-none">
                        <span class="dropdown opciones-registro">
                          <button class="btn btn-sm dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Opciones</button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item btn botonEditar" href="#" title="Editar información de '.$nombre.'" data-id="'.$id.'">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" /><line x1="13.5" y1="6.5" x2="17.5" y2="10.5" /></svg>
                                </span>
                                <span>Editar</span>
                            </a>
                            <a class="btn btn-white dropdown-item botonEliminar" title="Eliminar información de '.$nombre.'" data-id="'.$id.'" href="#">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                </span>
                                <span> Eliminar</span>
                            </a>
                          </div>
                        </span>
                </td>
            ';

            $output['data'][] = array(
                $checkbox, // checkbox seleccionar
                $i+1, // numeracion
                $nombre, // nombre
                $ccaa, // nombre comunidad autonoma
                $botonOpciones // botones editar, eliminar 
            );
        }


    } else{
        $output = [];
    }
    
    return $output;
}


// traer solo info por id
public function traerPorId($id = null){
    $respuesta = array('exito' => false, 'respuesta' =>'');
    if(is_numeric($id)){
        $info = $this->model->read($id);
        if(count($info) > 0 ){
            $respuesta['exito'] = true;
            $respuesta['respuesta'] = $info;
        }else{
            $respuesta['exito'] = false;
            $respuesta['respuesta'] = 'No se ha encontrado datos para el ID "'.$id.'"';
        }
    }else{
        $respuesta['exito'] = false;
        $respuesta['respuesta'] = 'El ID de la provincia tiene que ser numérico';
    }

    return $respuesta;

}

/**
 * registrar 
 */
public function registrar($datos){
    $respuesta = array('exito' => false, 'mensaje' => array());
    if(count($datos) > 0 ){
        #rawurldecode decodifica los spacios en blanco
        $nombre = sanitizarInput(rawurldecode($datos['nombre']));
        $id_ca = (int)sanitizarInput(rawurldecode($datos['id_ca']));
        $token = sanitizarInput($datos['_token']);
        if ($token && !empty($token)){
            $key_token = array('_token' => $token);
            if(NoCSRF::check('_token', $key_token, false, 60*10, false)){
                // validaciones
                //nombre: obligatorio -> letras y espacios, max 30 chars
                $nombre_valido = $this->validarNombre($nombre)['valido']; //true o false
                if(!$nombre_valido){
                    $respuesta['exito'] = false;
                    $respuesta['mensaje'] [] = $this->validarNombre($nombre)['error'];
                }else {
                    $nombre = $nombre;
                }

                $id_ca_valido = $this->validarIdCcaa($id_ca)['valido']; //true o false
                if(!$id_ca_valido){
                    $respuesta['exito'] = false;
                    $respuesta['mensaje'] [] = $this->validarIdCcaa($id_ca)['error'];
                }else {
                    $id_ca = $id_ca;
                }
                
                // paso de datos al modelo
                if ($nombre_valido && $id_ca_valido){
                    $resultado = $this->model->registrar($nombre, $id_ca); // registrar datos
                    if ( $resultado != null ){
                        //convertir a entero
                        if ((int)$resultado['id'] == 0 ){ // ya existe reg con los mismo datos
                            $respuesta['exito'] = false;
                            $respuesta['mensaje'][] = 'Ya existe una provincia con los mismos datos que has intentado registrar.';
        
                        }else if($resultado['id'] < 0 ){
                            $respuesta['exito'] = false;
                            $respuesta['mensaje'][] = 'No se se registrada la provincia porque no hay mas id disponibles';
                        }else {
                            $respuesta['exito'] = true;
                            $respuesta['mensaje'][] = 'Datos registrados correctamente. el ID de la provincia es: '. $resultado['id'];
                        }
        
        
                    } else{
                        $respuesta['exito'] = false;
                        $respuesta['mensaje'][] = 'No se ha podido registrar los datos , vuelve a intentarlo';
                    }
                }else{
        
                    return $respuesta;
                }

            }else{
                $respuesta['exito'] = false;
                $respuesta['mensaje'][] = 'Token de seguridad no valido, vuelve a intentarlo...'; 
            }
        }else { // no token de seguridad
            $respuesta['exito'] = false;
            $respuesta['mensaje'][] = 'Token de seguridad vacío, vuelve a intentarlo...';
        }
        

    } else {
        $respuesta['exito'] = false;
        $respuesta['mensaje'][] = 'No se ha recibido datos: ';
    }

    return $respuesta;
}


//actualizar
public function actualizar($datos){
    $respuesta = array('exito' => false, 'mensaje' => array());
    if(count($datos) > 0 ){
        #rawurldecode decodifica los spacios en blanco
        $id = (int)sanitizarInput($datos['id_provincia']); // id
        $nombre = sanitizarInput(rawurldecode($datos['nombre'])); // nombre
        $id_ca = (int)sanitizarInput(rawurldecode($datos['id_ca']));
        $token = sanitizarInput($datos['_token']);

        if($token && !empty($token)){
            $token_key = array('_token' => $token); // origen token
            // verificar validez token
            if(NoCSRF::check('_token', $token_key, false, 60*10, false)){
                // validaciones

                // id: obligatorio -> numerico, mayor que 0 y menor que 1000
                $id_valido = $this->validacion->validarNumeros('ID provincia',$id, 1, 99);
                if(!$id_valido['valido']){
                    $respuesta['exito'] = false;
                    $respuesta['mensaje'] [] = $id_valido['error'];
                } else {
                    $id = $id;
                }
                //nombre: obligatorio -> letras y espacios, max 20 chars
                $nombre_valido = $this->validacion->validarNombre('nombre provincia',$nombre, 1, 16);
                if(!$nombre_valido['valido']){
                    $respuesta['exito'] = false;
                    $respuesta['mensaje'] [] = $nombre_valido['error'];
                }else {
                    $nombre = $nombre;
                }

                //id_ca : no obligarrio -> numeros 
                $id_ca_valido = $this->validacion->validarNumeros('ID ccaa',$id_ca, 0, 99);;
                if(!$id_ca_valido['valido']){
                    $respuesta['exito'] = false;
                    $respuesta['mensaje'] [] = $id_ca_valido['error'];
                } else {
                    $id_ca = $id_ca;
                }
                
        
        
                // si datos ok ==> paso de datos al modelo
                if ($id_valido['valido'] && $id_ca_valido['valido'] && $nombre_valido['valido']){
                    $resultado = $this->model->actualizar($id, $nombre, $id_ca);// actualizar
                    if ( $resultado != null ){
                        //convertir a entero
                        if ((int)$resultado['mensaje'] < 0 ){ // ya existe cccaa con los mismos  datos
                            $respuesta['exito'] = false;
                            $respuesta['mensaje'][] = 'Ya existe una provincia con los mismos datos que has intentado registrar.';
        
                        }if ((int)$resultado['mensaje'] == 0 ){ // consulta no ejecuta en el stored procedure
                            $respuesta['exito'] = false;
                            $respuesta['mensaje'][] = 'Se ha intentado actualizar la información, pero algo ha salido mal.';
        
                        }else if ((int)$resultado['mensaje'] == 1 ) { // toodo okkkk
                            $respuesta['exito'] = true;
                            $respuesta['mensaje'][] = 'Datos de la provincia actualizados correctamente';
                        }
        
                    } else{
                        $respuesta['exito'] = false;
                        $respuesta['mensaje'][] = 'No se ha podido actualizar la información de la provincia, vuelve a intentar';
                    }
                }else{
                    return $respuesta;
                }

            }else {
                $respuesta['exito'] = false;
                $respuesta['mensaje'][] = 'Token de seguridad no valido, vuelve a intentarlo...'; 
            }
        }else{
            $respuesta['exito'] = false;
            $respuesta['mensaje'][] = 'Token de seguridad vacío, vuelve a intentarlo...';
        }

        

    } else {
        $respuesta['exito'] = false;
        $respuesta['mensaje'][] = 'No se ha recibido datos, asegurate de que hayas enviado datos';
    }

    return $respuesta;
}
/** ===================================================================
 *  Solicita la elinimacion de un registro al modelo 
 * @param $id: number
 * @param $token: string
 * @return array ('exito' => false || true, 'mensaje' => 'mensajes)
 =====================================================================*/
public function eliminar($id = null, $token = null){
    $respuesta = array('exito' => false, 'mensaje' => array());
    if( ($id > 0) && is_numeric($id) ){
        // verificar que exita token
        $token = sanitizarInput($token);
        if($token && !empty($token)){
            $key_token = array('_token' => $token);
            //vericar el token
            if(NoCSRF::check('_token', $key_token, false, 60*10, false)){
                $resultado = $this->model->eliminar($id);
                if ( $resultado){
                    if( array_key_exists('mensaje', $resultado)){
                        if ($resultado['mensaje'] == 1 ){
                            $respuesta['exito'] = true;
                            $respuesta['mensaje'] = 'Provincia elimianada correctamente';
                        }else if($resultado['mensaje'] == 0){
                
                            $respuesta['mensaje'] = 'La provincia no se ha podido eliminar, vuelve a intentarlo';
                        }
                    }else {
            
                        $respuesta['mensaje'] = $resultado;
                    }
        
                }else {
        
                    $respuesta['mensaje'] = 'Ha ocurrido un error al intentar eliminar la provincia: '.$id.' !!';
                }

            }else{
    
                $respuesta['mensaje'] = "Token de seguridad no valido, vuelve a intentarlo: $token";
            }
        }else{

            $respuesta['mensaje'] = 'Token de seguridad vacío, vuelve a intentarlo';
        }

    }else {
        $respuesta['mensaje'] = 'El id de la provincia tiene que ser numerico y mayor que 0';
    }
     
    return $respuesta;
}

public function eliminarMultiple( $registros = null, $token = null){
    $respuesta = array('exito' => false, 'mensaje' => array());
    if ($registros && is_array($registros) && count($registros) > 0){
        $token = sanitizarInput($token);
        if( $token && !empty($token)){
            $token_key = array('_token' => $token);
            // vericar el token
            if(NoCSRF::check('_token', $token_key, false, 60*10, false)){
                $errores = 0; 
                $total_registros = count($registros);
                foreach($registros as $registro){
                    if(!($this->validacion->validarNumeros('ID provincia',$registro, 1, 99)['valido'])){
                            $errores++;
                            $respuesta['mensaje'] = "El valor $registro no es valido";
                    }
                }
                // check for error
                if($errores <= 0){
                    $eliminacion = $this->model->eliminarMultiple($registros);
                    if ($eliminacion['exito'] == true){
                        $respuesta['exito'] = true;
                        $respuesta['mensaje'] = "Se ha eliminado los registros, total: ($total_registros)";
                    }else{
                        $respuesta['mensaje'] = $eliminacion['mensaje']; 
                    }
                }
            }else{ // token invalido
                $respuesta['mensaje'] = "Token de seguridad no valido, vuelve a intentarlo";
            } // verificar token
        }else{ // no token
            $respuesta['mensaje'] = "Token de seguridad vacío, vuelve a intentarlo";
        } // if else token
 
    } else{
        $respuesta['mensaje'] = "Es necesario un array de registros...";
    }
    return $respuesta;
}


public function importarDatos($archivo){
    $respuesta = array('exito' => false, 'mensaje' => array());
    if($archivo && is_array($archivo) && isset($archivo['name']) && !empty($archivo['name'])){
        
        #extensiones validas
        $ext_validas = array('xls', 'csv', 'xlsx');
        # desestructuracion del nombre
        $nombre_dividido = explode(".", $archivo['name']);
        #extesion del arch
        $ext_archivo = end($nombre_dividido);

         //--------validar extesion
         if (in_array(strtolower($ext_archivo), $ext_validas)){
            $documento = time().'.'.$ext_archivo;
            move_uploaded_file($archivo['tmp_name'], $documento);
            $tipo = \PhpOffice\PhpSpreadsheet\IOFactory::identify($documento);
            $lector = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($tipo);

            $doc = $lector->load($documento); unlink($documento);
            $registros = $doc->getActiveSheet()->toArray();
           
            #check for el numero de registro, limite establecido 50 (encabezados => 1)
            if($registros && count($registros) <= 51 ){
                $encabezados_permitidos = array('nombre', 'id_ca'); // encabezados permitidos
                $encabezados = array_shift($registros); // array encabezados
                $errores_encabezados = 0; // check encabezados
                foreach ($encabezados as $encabezado) {
                    if(!in_array($encabezado, $encabezados_permitidos)){
                        $errores_encabezados ++;
                    }
                }
                // verificar los encabezados
                if ($errores_encabezados == 0 ){
                    #registros validos(seran insertados)  
                    $registros_validos = array('encabezados' => $encabezados); 
                    #registros no validos(no se insertaran)
                    $registros_invalidos = array('encabezados' => $encabezados);
                    // recorrer los registros y validarlos
                    for ($i=0; $i < count($registros) ; $i++) {
                        // si hay errores de validacion     #errores de validacion
                        $errores = 0;                       $sms_error = [];
                        // el encabezado del item actual indicará el tipo de validacion
                        for ($j=0; $j < count($registros[$i]); $j++){
                            // si encabezado del item actual es id_ca: 
                            if($encabezados[$j] == 'id_ca'){
                                $validacion = $this->validacion->validarNumeros('ID CCAA',$registros[$i][$j], 0, 99);
                                //$validacion = $this->validarIdCcaa($registros[$i][$j]);
                                if(!$validacion['valido']){
                                    $errores++;
                                    $sms_error[] = $validacion['error'];
                                }
                                if($registros[$i][$j] == 0 || $registros[$i][$j] == '0'){
                                    $registros[$i][$j] = NULL;
                                }
                            }
                            
                            // si encabezado del item actual es nombre: 
                            if($encabezados[$j] == 'nombre') {
                                $validacion = $this->validacion->validarNombre('Nombre',$registros[$i][$j], 1, 16);
                               // $validacion = $this->validarNombre( $registros[$i][$j] );
                                if(!$validacion['valido']){
                                    $errores++;
                                    $sms_error[] = $validacion['error'];
                                }
                            }
                            
                        } // end loop encabezados 

                        // checkear si hay errores
                        if ($errores == 0){
                            $registros_validos['registros'][] = $registros[$i];
                        }else{
                            $registros_invalidos['campos'][] = array ('registros'=>$registros[$i], 'errores' => $sms_error);
                        } //check errores
                    } // loop a los registros para validaciones
                    
                    // generar excel con los registros invalidos
                    if(key_exists('campos', $registros_invalidos) && count($registros_invalidos['campos']) > 0){
                        //
                        $excel = $this->generarExcelRegistrosInvalidos($registros_invalidos);
                    }


                    if( key_exists('registros', $registros_validos) && count($registros_validos['registros']) > 0 ){
                        $insert = $this->model->insertarMultiple($registros_validos);
                        if($insert && $insert['exito'] == true){
                            $respuesta['exito'] = true;
                            $respuesta['mensaje'][] = $insert['mensaje'];
                            if(isset($excel)){
                                $respuesta['mensaje'][] = 'Se ha generado un documento con los registros invalidos.';
                                $respuesta['registros_invalidos'] = true;
                                $respuesta['documento'] = array('nombre' => $excel['nombre'], 'url' => $excel['url']);
                            }
                        }else{ $respuesta['mensaje'] = $insert['mensaje']; }

                        return $respuesta;
                    }else{
                        $respuesta['mensaje'][]= 'El documento no contiene registros validos.';
                        if($excel){
                            $respuesta['mensaje'][] = 'Se ha generado un documento con los registros invalidos.';
                            $respuesta['registros_invalidos'] = true;
                            $respuesta['documento'] = array('nombre' => $excel['nombre'], 'url' => $excel['url']);
                        }

                        return $respuesta;
                    }


                }else{ // check encabezados
                    $respuesta['mensaje'] = 'Los encabezados del documento solo pueden ser: id_ca y nombre';
                    return $respuesta;
                }


            }else{ // check if registros y el limite < 51
                (count($registros) > 51 ) ? $respuesta['mensaje'] = 'Por ahora solo se admiten 50 registros a ser importados.' : $respuesta['mensaje'] = 'Ha ocurrido un error mienstras se leia el archivo.';
                return $respuesta;
                
            }
         }else{
            $respuesta['mensaje'] = 'Solo se admite archivos con extension .xls, .csv, o .xlsx.';
            return $respuesta;
         }
        //
    }else{
        $respuesta['mensaje'] = 'Se esperaba un array de datos, vuelve a intertarlo...';
        return $respuesta;
    }
    
    //return $respuesta;
} #-------------------------------- subir archivo

public function exportarDatos($formato = null){
    $respuesta = array('exito' => false, 'mensaje' => array());
    if ($formato && !empty($formato) ){
        $formato = sanitizarInput($formato); $registros = $this->model->read();        
        if($registros && count($registros) > 0 ){
            #ordenar los registros alfabeticamente pork vienen ordenados por id
            $campo_ordenacion = array_column($registros, 'nombre');
            array_multisort($campo_ordenacion, SORT_ASC, $registros);

            #encabezados, columnas
            #   0               1    2      3
            # id_provincia, nombre, id_ca, nombre_ccaa
            $encabezados = array_keys($registros[0]);
            array_shift($encabezados) ; #quitar la columna de id_rovincia: nombre, id_ca, nombre_ccaa
            array_splice($encabezados, 1,1); // kitar ahora id_ca : nombre, nombre_ccaa
            array_unshift($encabezados, 'numeracion'); # añadir numeracion: numeracion, nombre, nombre_ccaa
            
            $valores = []; $numeracion = 0;
            for ($i = 0; $i < count($registros); $i++){
                # num ++,       array de valores de esa fila          quitar id_provincia   quitar id_ca
                $numeracion++; $fila = array_values($registros[$i]); array_shift($fila); array_splice($fila, 1,1);
                //
                for ($j = 0; $j < count($fila); $j++){$valores[$i][] = ucwords(strtolower($fila[$j]));}
                array_unshift($valores[$i], $numeracion);
            }

            $informacion = array(
                'tipo_documento' => '',
                'encabezados' => $encabezados, 
                'registros' => $valores, 
                'titulo' => 'LISTA DE PROVINCIAS' 
                
            );

            switch ($formato) {
                case 'xlsx':
                    # generar excel
                    $informacion['tipo_documento'] = 'Xlsx';
                    $informacion['color_titulo'] = 'FABF8F';
                    $informacion['color_encabezados'] = 'EBF1DE';
                    $informacion['nombre_documento'] = 'Provincias';
                    $archivo = $this->generarExcel($informacion);
                break;
                case 'csv':
                    $informacion['tipo_documento'] = 'Csv';
                    $informacion['color_titulo'] = 'FABF8F';
                    $informacion['color_encabezados'] = 'EBF1DE';
                    $informacion['nombre_documento'] = 'Provincias';
                    $archivo = $this->generarExcel($informacion);
                break;
                case 'pdf':
                    //$informacion['tipo_documento'] = 'pdf';
                    $informacion['nombre_documento'] = 'Provincias';
                    $archivo = $this->generarPDF($informacion);
                break;
                case 'print':
                    $informacion['descripcion'] = 'Esta es la lista de provincias registras hasta la fecha';
                    $archivo = $this->generarHTML($informacion);
                break;
                default:
                    $archivo = false;
                break;
            }
            if($archivo){
                $respuesta['exito'] = true;
                //$respuesta['mensaje'] = $archivo;
                $respuesta['mensaje'] = 'Documento generado';
                if($formato != 'print'){
                    $respuesta['documento'] = array('nombre' => $archivo['nombre'], 'url' => $archivo['url']);
                }else{
                    $respuesta['html'] = array('tabla' =>$archivo, 'titulo' => $informacion['titulo']);
                }

            }else{
                $respuesta['mensaje'] = 'No se ha podido generar el documento...';
            }
        }else{
            $respuesta['mensaje'] = 'No se ha obtenido datos';
        }
    }else{
        $respuesta['mensaje'] = 'Se esperaba un formato de documento';
    }
    return $respuesta;
}



// validar datos del formulario
public function validarIdProvincia($id){
    $validez = array('valido' => false, 'error' => '');
    if (validarNumero($id)){
        if ($id <= 0 || $id > 99 ){
            if($id <= 0){
                $validez['error'] = 'El id de la provincia tiene que ser mayor que 0';
            } else {
                $validez['error'] = 'El id de la provincia tiene que ser menor que 100';
            }
            $validez['valido'] = false;
        } else {
            $validez['valido'] = true;
        }
    }else {
        $validez['error'] = 'El id: '.$id.' de la provincia no tiene un formato correcto......';
    }

    return $validez;
}

// id: obligatorio -> numerico, mayor que 0 y menor que 1000
public function validarIdCcaa($id){
    $validez = array('valido' => false, 'error' => '');
    if (validarNumero($id)){
        if ($id < 0 || $id > 99 ){
            if($id < 0){
                $validez['error'] = 'El id comunidad autonoma tiene que ser mayor o igual que 0';
            } else {
                $validez['error'] = 'El id comunidad autonoma tiene que ser menor que 100';
            }
            $validez['valido'] = false;
        } else {
            $validez['valido'] = true;
        }
    }else {
        $validez['error'] = 'El id: '.$id.' de la comunidad autonoma no tiene un formato correcto......';
    }

    return $validez;
}
//nombre: obligatorio -> letras y espacios, max 20 chars
public function validarNombre($nombre){
    $validez = array('valido' => false, 'error'=> '');
    if($nombre && $nombre != null && $nombre != 'null'){
        if( strlen($nombre) > 16 || strlen($nombre) <= 0 ){
            if(strlen($nombre) > 16 ){  
                $validez['error'] = 'El nombre debe tener como máximo 16 carracteres';
            }else {
                $validez['error'] = 'El nombre no debe estar vacío';
            }
            $validez['valido'] = false;

        } else {
            if( validarTexto($nombre) ){
                $validez['valido'] = true;
            }else {
                $validez['valido'] = false;
                $validez['error'] = 'El nombre debe contener solo letras y espacios'; 
            }

        }
    }else{
        $validez['error'] = 'El nombre es obligatorio';
    }
    return $validez;
}


}
?>