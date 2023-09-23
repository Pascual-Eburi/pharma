<?php

class TransportistasController extends FileGeneratorController{
    private $model; private $validacion; private $userController;


    public function __construct(){
        $this->model = new TransportistasModel();
        $this->validacion = new InputsValidacionController();
        $this->userController = new UsuariosController();
    }

    # traer registros de transportistas
    public function getTransportistas($id = null){
        $transportistas = $this->model->getTransportistas($id);
        if(!$transportistas['exito']) return false;
        
        $registros = $transportistas['data'];
        return $registros;
    }

    #traer info de un registro
    public function getInfoTrasportista($id = null){
        $info = array('exito' => false, 'respuesta' => '');
        if(!$id || !is_numeric($id)){
            $info['respuesta'] = 'ID de transportista no valido';
            return $info;
        }

        $datos = $this->getTransportistas($id);
        if(!$datos){
            $info['respuesta'] = 'No hay datos para el transportista seleccionado';
            return $info;
        }
        $info['exito'] = true; $info['respuesta'] = $datos;
        return $info;
        
    }

    public function registrar($transportista){
        $insert= array('exito' => false, 'mensaje' => '');
        $transportista = $transportista;
        if(! $transportista || !is_array($transportista)){
            $insert['mensaje'] = 'Se esperaba un array con la información del transportista.';
            return $insert;
        }
        # validar data del transportista
        $provincia = ($transportista['id_provincia']) ? $transportista['id_provincia'] : NULL;
        # id pronvicia - opcional
        if($provincia){
            $provincia_v = $this->validacion->validarNumero('provincia', $provincia, 1, 6);
            if(!$provincia_v['valido']){
                $insert['mensaje'] = $provincia_v['error'];
                return $insert;
            }
        }

        # telefono
        $telefono = sanitizarInput($transportista['telefono']);
        $tel_v = $this->validacion->validarNumero('Telefono', $telefono, 1, 12);
      
        if(!$tel_v['valido']) {
            $insert['mensaje'] = $tel_v['error']; 
            return $insert;
        }
        
        #fax - opcional
        $fax = ($transportista['fax'])? sanitizarInput($transportista['fax']) : NULL;
        if($fax){
            $fax_v = $this->validacion->validarNumero('Fax', $fax, 1, 12);
            if(!$fax_v['valido']) {$insert['mensaje'] = $fax_v['error']; return $insert;}
        }

        # todo ok - transportista
        $data_tranportista = array(
            'tipo' => 'transportista',
            'data' => array(
                'telefono' => $telefono,
                'fax' => $fax,
                'provincia' => $provincia,
                'id_usuario' => NULL 
            )
        );

        #-------    check campos campos obligatorios usuario ---------------------
        $userDataInsert = $this->userController->generateInsertUserData($transportista);
        if(!$userDataInsert['exito']){
            return $userDataInsert;
        }

        //todo ok -- insertar usuario
        $data_usuario = $userDataInsert['mensaje'];
        $insert_usuario = $this->model->insertUser($data_usuario, $data_tranportista);
        # si no hay exito en insersion y se ha subido foto, elimianr esa foto de servidor
        if( !$insert_usuario['exito']){
            if(isset($data_usuario) && ($data_usuario['foto'] != NULL || !$data_usuario['foto']) ){
                $directorio = "./public/avatars/usuarios/";
                $delete_avatar = $this->eliminarArchivo($directorio, $data_usuario['foto']);

            }   
        }

        if(isset($delete_avatar) && !$delete_avatar){
            $insert_usuario['mensaje'] = $insert_usuario['mensaje'].'. FOTO no eliminada del servidor';
        }

        return $insert_usuario;
    }

    public function actualizar($transportista = null){
        $update = array('exito' => false, 'mensaje' => '');
        $data_transportista = array('tipo' => 'transportista','id'=> '', 'data' => array());

        if(! $transportista || !is_array($transportista)){
            $update['mensaje'] = 'Se esperaba un array con la información del fabricante.';
            return $update;
        }
         #-------    campos obligatorios ---------------------
        #-- id_trans
        if(!key_exists('id_trans', $transportista) || !$transportista['id_trans']){
            $update['mensaje'] = 'Se esperaba un id de transportista.';
            return $update; 
        }
        # asiginacion
        $id_trans = $this->validacion->sanitizarInput($transportista['id_trans']);
        #validacion
        $trans_v = $this->validacion->validarNumero('ID transportista', $id_trans, 1, 6);
        if(!$trans_v['valido']){
            $update['mensaje'] = $trans_v['error'];
            return $update;
        }
        
        # ok, insertar al array de fabricante
        $data_transportista['id'] = $id_trans;        
        #- id provincia
        if( key_exists('id_provincia', $transportista) ){
            $provincia = ($transportista['id_provincia']) ? $transportista['id_provincia'] : NULL;
            # id_provincia
            if($provincia){
                $provincia_v = $this->validacion->validarNumero('Pais', $provincia, 1, 3);
                if(!$provincia_v['valido']){
                    $update['mensaje'] = $provincia_v['error'];
                    return $update;
                }
            }
            # ok, insertar al array de fabricante
            $data_transportista['data']['provincia'] = $provincia;
        }

        # telefono
        if( key_exists('telefono', $transportista)){
            $telefono = sanitizarInput($transportista['telefono']);
            $tel_v = $this->validacion->validarNumero('Telefono', $telefono, 1, 12);
            
            if(!$tel_v['valido']) {$update['mensaje'] = $tel_v['error']; return $update;}

            $data_transportista['data']['telefono'] = $telefono;
        }

        if( key_exists('fax', $transportista)){
            #fax - opcional
            $fax = ($transportista['fax'])? sanitizarInput($transportista['fax']) : NULL;
            if($fax){
                $fax_v = $this->validacion->validarNumero('Fax', $fax, 1, 12);
                
                if(!$fax_v['valido']) {$update['mensaje'] = $fax_v['error']; return $update;}
            }
            $data_transportista['data']['fax'] = $fax;
        }

        # data del usuario validado y preparado par actualizacion
      
        $data_usuario = $this->userController->validateUserUpdateData($transportista);
        
        if(!$data_usuario['exito']) return $data_usuario;
        $data_user = $data_usuario['mensaje'];
        $actualizar = $this->model->updateUsert($data_user, $data_transportista);

        if($actualizar['exito']){
            // eliminar la foto antiguda del servidor
            if(key_exists('foto', $actualizar) && $actualizar['foto']){
                $directorio = "./public/avatars/usuarios/";
                $delete_avatar = $this->eliminarArchivo($directorio, $actualizar['foto']);
            }
        }
        if(isset($delete_avatar) && !$delete_avatar){
            $actualizar['mensaje'] = $actualizar['mensaje'].'. Y no se ha podido eliminar la foto del servidor';
        }

        $update['exito'] = $actualizar['exito'];
        $update['mensaje'] = $actualizar['mensaje'];


        return $update;

    }

    public function eliminar($transportista = null){
        $delete = array('exito' => false, 'mensaje' => '');
        # fabricante tiene que ser un array
        if(!$transportista || !is_array($transportista)){
            $delete['mensaje'] = 'Se esperaba un array con la información del transportista';
            return $delete;
        }

        # tiene que existir un id de fabricante (id registro)
        if(!key_exists('id_registro', $transportista) || !$transportista['id_registro']){
            $delete['mensaje'] = 'El ID de transportista es obligatorio';
            return $delete;  
        }
        $id_trans = $this->validacion->sanitizarInput($transportista['id_registro']);
        # tiene que existir un id de usuario
        if(!key_exists('id_usuario', $transportista) || !$transportista['id_usuario']){
            $delete['mensaje'] = 'El ID de usuario es obligatorio';
            return $delete;  
        }
        $id_usuario = $this->validacion->sanitizarInput($transportista['id_usuario']);

        # tiene que existir un token de seguridad
        if(!key_exists('_token', $transportista) || !$transportista['_token']){
            $delete['mensaje'] = 'Se esperaba un token de seguridad';
            return $delete;  
        }

        #ok, hay token... validarlo
        if (!(NoCSRF::check('_token', $transportista, false, 60*10, false)) ){
            $delete['mensaje'] = 'Token de seguridad invalido o expirado';
            return $delete; 
        }

        #validaciones
        $trans_v = $this->validacion->validarNumero('Id transportista', $id_trans, 1, 6);
        if(!$trans_v['valido']){
            $delete['mensaje'] = $trans_v['error'];
            return $delete;
        }

        $user_v = $this->validacion->validarNumero('Id usuario', $id_usuario, 1, 11);
        if(!$user_v['valido']){
            $delete['mensaje'] = $user_v['error'];
            return $delete;
        }
        $userType = array('tipo' => 'transportista', 'data' => array('id_trans' =>$id_trans));
        #ok - todo ok, eliminar 
        $delete_db = $this->model->deleteUser($id_usuario, $userType);
        #eliminar foto si hay
        if($delete_db['exito']){
            if(key_exists('foto', $transportista) && !empty($transportista['foto'])  ){
                $foto = $transportista['foto'];
                $directorio = "./public/avatars/usuarios/";
                $delete_avatar = $this->eliminarArchivo($directorio, $foto);
                if(!$delete_avatar){
                    $delete_db['mensaje'] = $delete_db['mensaje'].'. Y no se ha podido eliminar la foto del servidor';
                }
            }
        }

        $delete['exito'] = $delete_db['exito'];
        $delete['mensaje'] = $delete_db['mensaje'];
        return $delete;

    }

    public function eliminarMultiple( $registros = null, $token = null){
        $respuesta = array('exito' => false, 'mensaje' => array());
        
        if (!$registros || !is_array($registros)  || !count($registros)){
            $respuesta['mensaje'] = "Es necesario un array de registros...";
            return $respuesta;
        }
        $token = $token;
        if( !$token || empty($token)){
            $respuesta['mensaje'] = "Token de seguridad vacío, vuelve a intentarlo";
            return $respuesta;
        }

        $token_key = array('_token' => $token);
        // vericar el token
        if(!(NoCSRF::check('_token', $token_key, false, 60*10, false))){
            $respuesta['mensaje'] = "Token de seguridad no valido, vuelve a intentarlo";
            return $respuesta;
        }
        
        $total_registros = count($registros);
        foreach($registros as $registro){
            #validaciones
            $val = $this->validacion->validarNumero('Id transportista', $registro, 1, 6);
            if(!$val['valido']) {
                $respuesta['mensaje'] = $val['error'];
                return $respuesta;

            }
            
        }
        
        // check for error
        $eliminacion = $this->model->eliminarMultiple($registros);
        if (!$eliminacion['exito']) {
            $respuesta['mensaje'] = $eliminacion['mensaje']; 
            return $respuesta;
        }

        $respuesta['exito'] = true;
        $respuesta['mensaje'][] = "Se ha eliminado los registros, total: ($total_registros)";

        if(key_exists('fotos', $eliminacion) && count($eliminacion['fotos'])){
            $fotos = $eliminacion['fotos'];
            $directorio = "./public/avatars/usuarios/";
            foreach($fotos as $key => $foto){
                if($foto){
                    $delete_avatar = $this->eliminarArchivo($directorio, $foto);
                    if(!$delete_avatar){
                        $respuesta['mensaje'][] = "Foto: $foto no eliminada del servidor";
                    }
                }
            }
        }

        return $respuesta;
        
        
    }

    /**
     * formatear data para tabla de datos
     */
    public function generarDatosDataTable($data){
        $datos = array('data' => array());
        if(!$data || !is_array($data) || !count($data)) return $datos;
        
        // recorrer los registros
        for( $i = 0; $i < count($data); $i++){
            $id_transportista = $data[$i]['id_trans'];
            $id_usuario = $data[$i]['id_usuario'];
            $nombre = $data[$i]['nombre'];
            $roll = ucfirst($data[$i]['roll']);
            $tot_pedidos = $data[$i]['pedidos'];
            

            // generar avatar
            $url_foto = $data[$i]['foto'];
            $ruta = './public/avatars/usuarios/';
            $foto = $this->generarAvatar($ruta, $url_foto, $nombre);

            // generar div foto, email, nombre
            $div_nombre = '
            <div class="d-flex py-1 align-items-center">
                '.$foto.'
                <div class="flex-fill">
                <div class="font-weight-medium">
                '.$nombre.'</div>
                <div class="text-muted"><a href="#" class="text-reset">'.$data[$i]['email'].'</a></div>
                </div>
            </div>';

            // div ubicacion
            if($data[$i]['provincia']){
                $prv = '
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-muted text-reset" width="16" height="16" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <circle cx="12" cy="11" r="3"></circle>
                        <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                    </svg>
                    '.ucwords(strtolower($data[$i]['provincia'])).'
                ';
            }else{
                $prv = '';
            }

            if($data[$i]['ccaa']){
                $ccaa = ', <span class="text-muted text-reset">'.ucwords(strtolower($data[$i]['ccaa'])).'</span>';
            }else{
                $ccaa = '';
            }
            if(empty($prv) && empty($ccaa)){
                $localizacion = '<span class="badge bg-red me-1"></span> Sin registrar';
            }else{
                $localizacion = '
                <div class="d-flex py-1 align-items-center">
                    <div class="flex-fill">
                        <div class="font-weight-medium">
                        '.$prv.''.$ccaa.' 
                        </div>
                </div>';

            }
            // telefono y fax
            if(!$data[$i]['telefono']){
                $telefono = '';
            }else{
                $telefono = '
                <div class="font-weight-medium">
                    <small><span class="badge bg-secondary-lt">Telf:</span>  '.$data[$i]['telefono'].' </small> 
                </div>';
            }

            if(!$data[$i]['fax']){
                $fax = '';
            }else{
                $fax = '                        
                <div class="text-muted">
                    <small class="text-reset">
                        <span class="badge bg-purple-lt">Fax:</span>  
                    '.$data[$i]['fax'].'
                 </small>
                </div>';
            }
            if(!$data[$i]['telefono'] && !$data[$i]['fax']){
                $contacto = '<span class="badge bg-red me-1"></span> Sin contacto';
            }else{
                $contacto = '
                <div class="d-flex py-1 align-items-center">
                    <div class="flex-fill">
                        '.$telefono.'
                        '.$fax.'
                    </div>
                </div>';

            }

            // farmacos
            if($tot_pedidos >= 30){ // verde
                $color = 'bg-success';
            }else if($tot_pedidos >= 10){ // amarillo
                $color = 'bg-warning';
            }else if($tot_pedidos >= 1){ // rojo
                $color = 'bg-danger';
            }else{// gris
                $color = 'bg-secondary';
            }

            $pedidos = '
                <span class="badge  '.$color.' me-1"></span>
                '.$tot_pedidos.'
            ';

            // checkbox seleccion
            $checkbox = '<input class="form-check-input m-0 align-middle single-select" type="checkbox" aria-label="Seleccionar registro" data-id="'.$id_transportista.'" title="Seleccionar este registro">';

            // botones de opciones
            $botonOpciones ='
            <td class="d-print-none">
                    <span class="dropdown opciones-registro">
                        <button class="btn btn-sm dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Opciones</button>
                        <div class="dropdown-menu">
                        <a class="dropdown-item btn botonEditar" href="#" title="Editar información de '.$nombre.'" data-id="'.$id_usuario.'" data-transportista = "'.$id_transportista.'">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" /><line x1="13.5" y1="6.5" x2="17.5" y2="10.5" /></svg>
                            </span>
                            <span>Editar</span>
                        </a>
                        <a class="btn btn-white dropdown-item botonEliminar" title="Eliminar información de '.$nombre.'" data-id="'.$id_usuario.'" data-transportista = "'.$id_transportista.'" href="#">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </span>
                            <span> Eliminar</span>
                        </a>
                        </div>
                    </span>
                </td>
            ';

            // salida: informacion formateada para ser mostrada
            $datos['data'][] = array(
                $checkbox, // checkbox seleccionar
                $i+1, // numeracion
                $div_nombre, // nombre, email, foto
                $contacto, // telefono, fax
                $localizacion, // provincia, ccaa
                ($roll)? $roll: '<span class="badge bg-warning me-1"></span> No asignado
                ',
                $pedidos,
                $botonOpciones // botones editar, eliminar 
            );
            

        } # for


        return $datos;
    }
    /**
     * Generar datos para exportar a un documento
     */
    public function generarDatosExport($cols_excluidas = null){
        $registros = $this->model->getTransportistas();
        if(!$registros['exito'] || !count($registros['data'])){return false;}
        if($cols_excluidas && !is_array($cols_excluidas)){
            return false;
        }else{
            $exc = $cols_excluidas;
        }
        $data = $registros['data'];

        #ordenar los registros alfabeticamente pork vienen ordenados por id
        $campo_ordenacion = array_column($data, 'nombre');
        array_multisort($campo_ordenacion, SORT_ASC, $data);

        # si no se desea omitir ninguna columna
        if(!isset($exc) || !count($exc)) {
            #encabezados, columnas
            $encabezados = array_keys($data[0]);
            # añadir numeracion: numeracion,
            array_unshift($encabezados, 'numeracion'); 
            $valores = []; $numeracion = 0;
            for ($i = 0; $i < count($data); $i++){
                # num ++,                
                $numeracion++; $valores_fila = array_values($data[$i]); 
                //recorrer valores de cada fila y añadirlos despues al array glogar de valores
                for ($j = 0; $j < count($valores_fila); $j++){
                    $valores[$i][] = ($valores_fila[$j] === NULL) ? 'No asignado': ucwords(strtolower($valores_fila[$j]));
                }
                array_unshift($valores[$i], $numeracion);
            } 
            if(count($encabezados) && count($valores)){
                return array('encabezados' => $encabezados, 'valores' => $valores);
            }
            return false;
        }

        # ok, hay que eliminar u omitir algunas columnas
        $export = array(); $active = [];
        for($i = 0; $i < count($data); $i++){
            foreach($data[$i] as $key => $valor){
                if( in_array($key, $exc) ) continue;
                $active[$key] = $valor ;  
            }
            $export[$i] = $active;
        }
        if(!count($export)) return false;
        # todo ok, ahora generar estructura de datos para exportacion

        #encabezados, columnas
        $encabezados = array_keys($export[0]);
        # añadir numeracion: numeracion,
        array_unshift($encabezados, 'nº'); 
        $valores = []; $numeracion = 0;
        for ($i = 0; $i < count($export); $i++){
            # num ++,                
            $numeracion++; $valores_fila = array_values($export[$i]); 
            //recorrer valores de cada fila y añadirlos despues al array glogar de valores
            for ($j = 0; $j < count($valores_fila); $j++){
                $valores[$i][] = ($valores_fila[$j] === NULL) ? 'No asignado': ucwords(strtolower($valores_fila[$j]));
            }
            array_unshift($valores[$i], $numeracion);
        } 
        if(count($encabezados) && count($valores)){
            return array('encabezados' => $encabezados, 'valores' => $valores);
        }

        return false;

    }

    /**
     * Exportar datos a un documento
     */
    public function exportarDatos($formato = null){
        $respuesta = array('exito' => false, 'mensaje' => array());
        if (!$formato || empty($formato) ){
            $respuesta['mensaje'] = 'Se esperaba un formato de documento';
            return $respuesta;
        }

        $formato = sanitizarInput($formato); 
        $excluidos = ['id_usuario', 'id_trans', 'id_roll', 'id_provincia', 'ccaa', 'foto','roll'];
        $registros = $this->generarDatosExport($excluidos); 

        if( !$registros || !count($registros)){
            $respuesta['mensaje'] = 'No se ha obtenido datos';
            return $respuesta;
        }
        if( !count($registros['encabezados']) || !count($registros['valores'])){
            $respuesta['mensaje'] = 'No se puede generar el documento porque faltan datos.';
            return $respuesta;  
        }
        $encabezados = $registros['encabezados']; $valores= $registros['valores'];
        
        $informacion = array(
            'tipo_documento' => '',
            'encabezados' => $encabezados, 
            'registros' => $valores, 
            'titulo' => 'LISTA DE TRANSPORTISTAS' 
            
        );

        switch ($formato) {
            case 'xlsx':
                # generar excel
                $informacion['tipo_documento'] = 'Xlsx';
                $informacion['color_titulo'] = 'FABF8F';
                $informacion['color_encabezados'] = 'EBF1DE';
                $informacion['nombre_documento'] = 'transportistas';
                $archivo = $this->generarExcel($informacion);
            break;
            case 'csv':
                $informacion['tipo_documento'] = 'Csv';
                $informacion['color_titulo'] = 'FABF8F';
                $informacion['color_encabezados'] = 'EBF1DE';
                $informacion['nombre_documento'] = 'transportistas';
                $archivo = $this->generarExcel($informacion);
            break;
            case 'pdf':
                //$informacion['tipo_documento'] = 'pdf';
                $informacion['nombre_documento'] = 'transportistas';
                $archivo = $this->generarPDF($informacion);
            break;
            case 'print':
                $informacion['descripcion'] = 'Esta es la lista de transportistas registrados hasta la fecha';
                $archivo = $this->generarHTML($informacion);
            break;
            case 'prueba':
                $archivo = $this->anchoCols($encabezados, $valores);
                return $archivo;
            break;
            default:
                $archivo = false;
            break;
        }

        if(!$archivo){
            $respuesta['mensaje'] = 'No se ha podido generar el documento...';
            return $respuesta;
        }

        $respuesta['exito'] = true;
        $respuesta['mensaje'] = 'Documento generado';
        if($formato != 'print'){
            $respuesta['documento'] = array('nombre' => $archivo['nombre'], 'url' => $archivo['url']);
            return $respuesta;
        }else{
            $respuesta['html'] = array('tabla' =>$archivo, 'titulo' => $informacion['titulo']);
            return $respuesta;
        }


    
                
    }

    /**
     *  Importar datos de un archivo
     */
    public function importarDatos($archivo){
        $respuesta = array('exito' => false, 'mensaje' => array());
        #check al archivo 
        if(!$archivo || !is_array($archivo) || !isset($archivo['name']) || empty($archivo['name'])){
            $respuesta['mensaje'] = 'Se esperaba un array de datos, vuelve a intertarlo...';
            return $respuesta;
        }

        #extensiones validas
        $ext_validas = array('xls', 'csv', 'xlsx');
        # desestructuracion del nombre
        $nombre_dividido = explode(".", $archivo['name']);
        #extesion del arch
        $ext_archivo = end($nombre_dividido);
            
        //--------validar extesion
        if(!in_array(strtolower($ext_archivo), $ext_validas)){
            $respuesta['mensaje'] = 'Solo se admite archivos con extension .xls, .csv, o .xlsx.';
            return $respuesta;
        }
           
        $documento = time().'.'.$ext_archivo;
        move_uploaded_file($archivo['tmp_name'], $documento);
        $tipo = \PhpOffice\PhpSpreadsheet\IOFactory::identify($documento);
        $lector = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($tipo);

        $doc = $lector->load($documento); unlink($documento);
        $registros = $doc->getActiveSheet()->toArray();
               
        #check for el numero de registro, limite establecido 50 (encabezados => 1)
        if(!$registros || count($registros) >= 51){
            $respuesta['mensaje']  = (count($registros) > 51 ) ? 'Por ahora solo se admiten 50 registros a ser importados.' : 'No hay registros para importar..';
            return $respuesta;
        }

        $encabezados_permitidos = array('nombre', 'email', 'telefono', 'fax'); // encabezados permitidos
        $encabezados = array_shift($registros); // array encabezados

        // verificar los encabezados
        foreach ($encabezados as $encabezado) {
            if(!in_array($encabezado, $encabezados_permitidos)){
                // check encabezados
                $respuesta['mensaje'] = 'Los encabezados solo pueden ser: nombre, email, telefono, fax';
                return $respuesta;

            }
        }
 
        #registros validos(seran insertados)  
        $registros_validos = array(); 
        #registros no validos(no se insertaran)
        $registros_invalidos = array('encabezados' => $encabezados);
        $data_usuario = array(
            'foto' =>  NULL,'nombre' => '',
            'email' => '','clave' => MD5('clave'),'id_roll' => NULL
        );
        $data_transportista = array(
            'tipo' => 'transportista', 'data' => array('provincia' => NULL,'id_usuario' => NULL )
        ); 
        // recorrer los registros y validarlos
        for ($i=0; $i < count($registros) ; $i++) {
            // si hay errores de validacion     #errores de validacion
            $errores = 0;                       $sms_error = [];
            // el encabezado del item actual indicará el tipo de validacion
            for ($j=0; $j < count($encabezados); $j++){
                // si encabezado del item actual es email: 
                if($encabezados[$j] == 'email'){
                    $val = $this->validacion->validarEmail('Email', $registros[$i][$j], 1,100); 
                    if(!$val['valido']){
                        $errores++;
                        $sms_error[] = $val['error'];
                    }

                }
                
                // si encabezado del item actual es nombre: 
                if($encabezados[$j] == 'nombre') {
                    $val = $this->validacion->validarNombre('Nombre', $registros[$i][$j], 1, 50);
                    // $validacion = $this->validarNombre( $registros[$i][$j] );
                    if(!$val['valido']){
                        $errores++;
                        $sms_error[] = $val['error'];
                    }
                }
                // si encabezado del item actual es telefono: 
                if($encabezados[$j] == 'telefono') {
                    $val_tel = $this->validacion->validarNumero('Telefono', $registros[$i][$j], 1, 12);
                    if(!$val_tel['valido']){
                        $errores++;
                        $sms_error[] = $val_tel['error'];
                    }
                }

                // si encabezado del item actual es fax: 
                if($encabezados[$j] == 'fax') {
                    if($registros[$i][$j] && $registros[$i][$j] != NULL){
                        $val_tel = $this->validacion->validarNumero('fax', $registros[$i][$j], 0, 12);
                        if(!$val_tel['valido']){
                            $errores++;
                            $sms_error[] = $val_tel['error'];
                        }

                    }
                }
                
            } // end loop validacion

            // checkear si hay errores
            if ($errores == 0){
                $key = ''; $cols_usuario = ['email', 'nombre']; $cols_subTipo = ['telefono', 'fax'];
                for($k= 0; $k < count($registros[$i]); $k++){
                    $key = $encabezados[$k];
                    # cols supertipo
                    if(in_array($key, $cols_usuario) ){
                        $data_usuario[$key] = $this->validacion->sanitizarInput($registros[$i][$k]) ;
                    }
                    # cols subtipo
                    if(in_array($key, $cols_subTipo) ){
                        $data_transportista['data'][$key] = $this->validacion->sanitizarInput($registros[$i][$k]) ;
                    }
                }
                $registros_validos[] = [$data_usuario, $data_transportista];
            }else{
                $registros_invalidos['campos'][] = array ('registros'=>$registros[$i], 'errores' => $sms_error);
            } //check errores
        } // loop a los registros para validaciones return $registros_validos;
        
        // generar excel con los registros invalidos
        if(key_exists('campos', $registros_invalidos) && count($registros_invalidos['campos']) > 0){
            $excel = $this->generarExcelRegistrosInvalidos($registros_invalidos);
        }

        if( !count($registros_validos) ){
            $respuesta['mensaje'][]= 'El documento no contiene registros validos.';
            if($excel){
                $respuesta['mensaje'][] = 'Se ha generado un documento con los registros invalidos.';
                $respuesta['registros_invalidos'] = true;
                $respuesta['documento'] = array('nombre' => $excel['nombre'], 'url' => $excel['url']);
            }
            return $respuesta;
        }

        # todo okey -- insertar registros
        $insert = $this->model->insertBulkUser($registros_validos);
        if($insert && $insert['exito'] == true){
            $respuesta['exito'] = true;
            $respuesta['mensaje'][] = $insert['mensaje'];
            if(isset($excel)){
                $respuesta['mensaje'][] = 'Se ha generado un documento con los registros invalidos.';
                $respuesta['registros_invalidos'] = true;
                $respuesta['documento'] = array('nombre' => $excel['nombre'], 'url' => $excel['url']);
            }
        }else{ 
            $respuesta['mensaje'] = $insert['mensaje']; 
        }

        return $respuesta;
        


        





        
        
        //return $respuesta;
    } #-------------------------------- subir archivo
}



?>