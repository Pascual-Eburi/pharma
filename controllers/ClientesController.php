<?php

class ClientesController extends FileGeneratorController{
    private $model; private $validacion; private $userController; public $helper;


    public function __construct(){
        $this->model = new ClientesModel();
        $this->validacion = new InputsValidacionController();
        $this->userController = new UsuariosController();
        $this->helper = new Helpers();
    }

    # traer registros de clientes
    public function getClientes($id = 0){
        $clientes = $this->model->getClientes($id);
        if(!$clientes['exito']) return false;
        
        $registros = $clientes['data'];
        return $registros;
    }

    #traer info de un registro
    public function getInfoCliente($id = null){
        $info = array('exito' => false, 'respuesta' => '');
        if(!is_numeric($id)){
            $info['respuesta'] = 'ID de cliente no valido';
            return $info;
        }

            $datos = $this->getClientes($id);
            if(!$datos){
                $info['respuesta'] = 'No hay datos para el cliente seleccionado';
                return $info;
            }
            $info['exito'] = true; $info['respuesta'] = $datos;


        return $info;
        
    }

    public function registrar($cliente){
        $insert= array('exito' => false, 'mensaje' => '');
        $cliente = $cliente;
        if(! $cliente || !is_array($cliente)){
            $insert['mensaje'] = 'Se esperaba un array con la información del cliente.';
            return $insert;
        }
        # validar data del cliente
        $provincia = ($cliente['id_provincia']) ? $cliente['id_provincia'] : NULL;
        # id pronvicia - opcional
        if($provincia){
            $provincia_v = $this->validacion->validarNumero('provincia', $provincia, 1, 2);
            if(!$provincia_v['valido']){
                $insert['mensaje'] = $provincia_v['error'];
                return $insert;
            }
        }

        # telefono - opcional
        
        $telefono = ($cliente['telefono']) ? sanitizarInput($cliente['telefono']) : NULL;
        if($telefono){
            $tel_v = $this->validacion->validarNumero('Telefono', $telefono, 1, 15);
            if(!$tel_v['valido']) {
                $insert['mensaje'] = $tel_v['error']; 
                return $insert;
            }

        }
        
        #fecha nacimiento - opcional
        $fechaNac = ($cliente['fechaNacimiento'])? sanitizarInput($cliente['fechaNacimiento']) : NULL;
        if($fechaNac){
            $fechaNac_v = $this->validacion->validarFecha('Fecha Nacimiento', $fechaNac, 1900, 2021);
            if(!$fechaNac_v['valido']) {$insert['mensaje'] = $fechaNac_v['error']; return $insert;}
            # convertir fecha a formato ingles
            $fechaNac = $this->helper->formatDate($fechaNac, 'Y-m-d');
            if(!$fechaNac){
                $insert['mensaje'] = 'NO se ha podido convertir la fecha al formato solicitado'; 
                return $insert; 
            }
        }

        # todo ok - cliente
        $data_cliente = array(
            'tipo' => 'cliente',
            'data' => array(
                'tlfno' => $telefono,
                'f_nac' => $fechaNac,
                'provincia' => $provincia,
                'id_usuario' => NULL 
            )
        );

        #-------    check campos campos obligatorios usuario ---------------------
        $userDataInsert = $this->userController->generateInsertUserData($cliente);
        if(!$userDataInsert['exito']){
            return $userDataInsert;
        }

        //todo ok -- insertar usuario
        $data_usuario = $userDataInsert['mensaje'];
        //return array('usuario' => $data_usuario, 'cliente' => $data_cliente);

        $insert_usuario = $this->model->insertUser($data_usuario, $data_cliente);

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

    public function actualizar($cliente = null){
        $update = array('exito' => false, 'mensaje' => '');
        $data_cliente = array('tipo' => 'cliente','id'=> '', 'data' => array());

        if(! $cliente || !is_array($cliente)){
            $update['mensaje'] = 'Se esperaba un array con la información del cliente.';
            return $update;
        }
         #-------    campos obligatorios ---------------------
        #-- id_cliente
        if(!key_exists('id_cliente', $cliente) || !$cliente['id_cliente']){
            $update['mensaje'] = 'Se esperaba un id de cliente.';
            return $update; 
        }
        # asiginacion
        $id_cliente = $this->validacion->sanitizarInput($cliente['id_cliente']);
        #validacion
        $cliente_v = $this->validacion->validarNumero('ID cliente', $id_cliente, 1, 6);
        if(!$cliente_v['valido']){
            $update['mensaje'] = $cliente_v['error'];
            return $update;
        }
        
        # ok, insertar al array de cliente
        $data_cliente['id'] = $id_cliente;        
        #- id provincia
        if( key_exists('id_provincia', $cliente) ){
            $provincia = ($cliente['id_provincia']) ? $cliente['id_provincia'] : NULL;
            # id_provincia
            if($provincia){
                $provincia_v = $this->validacion->validarNumero('Pais', $provincia, 1, 3);
                if(!$provincia_v['valido']){
                    $update['mensaje'] = $provincia_v['error'];
                    return $update;
                }
            }
            # ok, insertar al array de cliente
            $data_cliente['data']['provincia'] = $provincia;
        }

        # telefono
        if( key_exists('telefono', $cliente)){
            $telefono = sanitizarInput($cliente['telefono']);
            $tel_v = $this->validacion->validarNumero('Telefono', $telefono, 1, 15);
            
            if(!$tel_v['valido']) {$update['mensaje'] = $tel_v['error']; return $update;}

            $data_cliente['data']['tlfno'] = $telefono;
        }

        if( key_exists('fechaNacimiento', $cliente)){
            #fecha nacimiento - opcional
            $fechaNac = ($cliente['fechaNacimiento'])? sanitizarInput($cliente['fechaNacimiento']) : NULL;
            if($fechaNac){
                $fechaNac_v = $this->validacion->validarFecha('Fecha Nacimiento', $fechaNac, 1900, 2021);
                if(!$fechaNac_v['valido']) {
                    $update['mensaje'] = $fechaNac_v['error']; return $update;
                }
                # convertir fecha a formato ingles
                $fechaNac = $this->helper->formatDate($fechaNac, 'Y-m-d');
                if(!$fechaNac){
                    $update['mensaje'] = 'NO se ha podido convertir la fecha al formato solicitado'; 
                    return $update; 
                }
            }
            $data_cliente['data']['f_nac'] = $fechaNac;
        }

        # data del usuario validado y preparado par actualizacion
      
        $data_usuario = $this->userController->validateUserUpdateData($cliente);
        
        if(!$data_usuario['exito']) return $data_usuario;
        $data_user = $data_usuario['mensaje'];

        # enviar data para actualizar
        $actualizar = $this->model->updateUsert($data_user, $data_cliente);
        if($actualizar['exito']){
            // eliminar la foto antiguda del servidor
            if(key_exists('foto', $actualizar) && $actualizar['foto']){
                $directorio = "./public/avatars/usuarios/";
                $delete_avatar = $this->eliminarArchivo($directorio, $actualizar['foto']);
            }
        }
        if(isset($delete_avatar) && !$delete_avatar){
            $actualizar['mensaje'] = $actualizar['mensaje'].'. Foto no eliminada del servidor';
        }

        $update['exito'] = $actualizar['exito'];
        $update['mensaje'] = $actualizar['mensaje'];


        return $update;

    }

    /**
     * Recibe un cliente a eliminar de la base de datos
     * @param array @cliente: array con el id_usuario e id_cliente y la foto del cliente a eliminar y un token de seguridad
     * @return array @delete: exito(true si todo va bien, false si hay algun error) 
     *                      mensaje: mensaje de error o exito de la operciaon
     */
    public function eliminar($cliente = null){
        $delete = array('exito' => false, 'mensaje' => '');
        # cliente tiene que ser un array
        if(!$cliente || !is_array($cliente)){
            $delete['mensaje'] = 'Se esperaba un array con la información del cliente';
            return $delete;
        }

        # tiene que existir un id de cliente (id registro)
        if(!key_exists('id_registro', $cliente) || !$cliente['id_registro']){
            $delete['mensaje'] = 'El ID de cliente es obligatorio';
            return $delete;  
        }
        $id_cliente = $this->validacion->sanitizarInput($cliente['id_registro']);
        # tiene que existir un id de usuario
        if(!key_exists('id_usuario', $cliente) || !$cliente['id_usuario']){
            $delete['mensaje'] = 'El ID de usuario es obligatorio';
            return $delete;  
        }
        $id_usuario = $this->validacion->sanitizarInput($cliente['id_usuario']);

        # tiene que existir un token de seguridad
        if(!key_exists('_token', $cliente) || !$cliente['_token']){
            $delete['mensaje'] = 'Se esperaba un token de seguridad';
            return $delete;  
        }

        #ok, hay token... validarlo
        if (!(NoCSRF::check('_token', $cliente, false, 60*10, false)) ){
            $delete['mensaje'] = 'Token de seguridad invalido o expirado';
            return $delete; 
        }

        #validaciones
        $cliente_v = $this->validacion->validarNumero('Id cliente', $id_cliente, 1, 6);
        if(!$cliente_v['valido']){
            $delete['mensaje'] = $cliente_v['error'];
            return $delete;
        }

        $user_v = $this->validacion->validarNumero('Id usuario', $id_usuario, 1, 11);
        if(!$user_v['valido']){
            $delete['mensaje'] = $user_v['error'];
            return $delete;
        }
        # subtipo - cliente
        $userType = array('tipo' => 'cliente', 'data' => array('id_cliente' =>$id_cliente));
        #ok - todo ok, eliminar 
        $delete_db = $this->model->deleteUser($id_usuario, $userType);
        #eliminar foto si hay
        if($delete_db['exito']){
            if(key_exists('foto', $cliente) && !empty($cliente['foto'])  ){
                $foto = $cliente['foto'];
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
            $val = $this->validacion->validarNumero('Id cliente', $registro, 1, 6);
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
            $id_cliente = $data[$i]['id_cliente'];
            $id_usuario = $data[$i]['id_usuario'];
            $edad = $data[$i]['edad'];
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
            // telefono 
                            /*<div class="d-flex py-1 align-items-center">
                    <div class="flex-fill">
                        <div class="font-weight-medium">
                        
                                                </div>
                    </div>
                </div>*/
            if(!$data[$i]['telefono']){
                $contacto = '<span class="badge bg-red me-1"></span> Sin contacto';
            }else{
                $contacto = '
                    <small>
                        <span class="badge bg-secondary-lt">Telf:</span>  
                        '.$data[$i]['telefono'].' 
                    </small> 
                ';

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
            $checkbox = '<input class="form-check-input m-0 align-middle single-select" type="checkbox" aria-label="Seleccionar registro" data-id="'.$id_cliente.'" title="Seleccionar este registro">';

            // botones de opciones
            $botonOpciones ='
            <td class="d-print-none">
                    <span class="dropdown opciones-registro">
                        <button class="btn btn-sm dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Opciones</button>
                        <div class="dropdown-menu">
                        <a class="dropdown-item btn botonEditar" href="#" title="Editar información de '.$nombre.'" data-id="'.$id_usuario.'" data-cliente = "'.$id_cliente.'">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" /><line x1="13.5" y1="6.5" x2="17.5" y2="10.5" /></svg>
                            </span>
                            <span>Editar</span>
                        </a>
                        <a class="btn btn-white dropdown-item botonEliminar" title="Eliminar información de '.$nombre.'" data-id="'.$id_usuario.'" data-cliente = "'.$id_cliente.'" href="#">
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
                $edad, // edad
                $contacto, // telefono
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
        $registros = $this->model->getClientes();
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
        $excluidos = ['id_usuario', 'id_cliente', 'id_roll', 'id_provincia', 'ccaa', 'foto','roll', 'fecha_registro', 'fechaNacimiento'];
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
            'titulo' => 'LISTA DE CLIENTES' 
            
        );

        switch ($formato) {
            case 'xlsx':
                # generar excel
                $informacion['tipo_documento'] = 'Xlsx';
                $informacion['color_titulo'] = 'FABF8F';
                $informacion['color_encabezados'] = 'EBF1DE';
                $informacion['nombre_documento'] = 'clientes';
                $archivo = $this->generarExcel($informacion);
            break;
            case 'csv':
                $informacion['tipo_documento'] = 'Csv';
                $informacion['color_titulo'] = 'FABF8F';
                $informacion['color_encabezados'] = 'EBF1DE';
                $informacion['nombre_documento'] = 'clientes';
                $archivo = $this->generarExcel($informacion);
            break;
            case 'pdf':
                //$informacion['tipo_documento'] = 'pdf';
                $informacion['nombre_documento'] = 'clientes';
                $archivo = $this->generarPDF($informacion);
            break;
            case 'print':
                $informacion['descripcion'] = 'Esta es la lista de clientes registrados hasta la fecha';
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
     * @param file $arcchivo: archivo que contiene los registros a importar
     * @return array $respuesta : exito(true|false), array mensaje: mensaje de error o de exito
     * 
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

        $encabezados_permitidos = array('nombre', 'email', 'telefono', 'fechaNacimiento'); // encabezados permitidos
        $encabezados = array_shift($registros); // array encabezados

        // verificar los encabezados
        foreach ($encabezados as $encabezado) {
            if(!in_array($encabezado, $encabezados_permitidos)){
                // check encabezados
                $respuesta['mensaje'] = 'Los encabezados solo pueden ser: nombre, email, telefono, fechaNacimiento';
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
        $data_cliente = array(
            'tipo' => 'cliente', 'data' => array('provincia' => NULL,'id_usuario' => NULL )
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
                    if($registros[$i][$j] ){
                        $val_tel = $this->validacion->validarNumero('Telefono', $registros[$i][$j], 0, 15);
                        if(!$val_tel['valido']){
                            $errores++;
                            $sms_error[] = $val_tel['error'];
                        }

                    }
                }

                // si encabezado del item actual es fechaNacimiento: 
                if($encabezados[$j] == 'fechaNacimiento') {
                    if($registros[$i][$j] && $registros[$i][$j] != NULL){
                        $fecha_v = $this->validacion->validarFecha('Fecha Nacimiento', $registros[$i][$j], 1900, 2021);
                        if(!$fecha_v['valido']){ $errores++; $sms_error[] = $fecha_v['error']; }

                    }


                }
                
            } // end loop validacion

            // checkear si hay errores
            if ($errores == 0){
                $key = ''; $cols_usuario = ['email', 'nombre']; $cols_subTipo = ['telefono', 'fechaNacimiento'];
                for($k= 0; $k < count($registros[$i]); $k++){
                    $key = $encabezados[$k];
                    $val = $registros[$i][$k];
                    # cols supertipo
                    if(in_array($key, $cols_usuario) ){
                        $data_usuario[$key] = $this->validacion->sanitizarInput($val) ;
                    }
                    # cols subtipo
                    if(in_array($key, $cols_subTipo) ){
                        if($key == 'telefono'){
                            $data_cliente['data']['tlfno'] = $this->validacion->sanitizarInput($val) ;
                        }
                        if($key == 'fechaNacimiento'){
                            # convertir fecha a formato ingles
                            $data_cliente['data']['f_nac'] = ($val) ? $this->helper->formatDate($val, 'Y-m-d') : NULL;
 
                        }
                    }
                }
                $registros_validos[] = [$data_usuario, $data_cliente];
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
        if(!$insert['exito']){ $respuesta['mensaje'] = $insert['mensaje'];  return $respuesta; }

        $respuesta['exito'] = true;
        $respuesta['mensaje'][] = $insert['mensaje'];
        if(isset($excel)){
            $respuesta['mensaje'][] = 'Se ha generado un documento con los registros invalidos.';
            $respuesta['registros_invalidos'] = true;
            $respuesta['documento'] = array('nombre' => $excel['nombre'], 'url' => $excel['url']);
        }
        
        return $respuesta;
        


        





        
        
        //return $respuesta;
    } #-------------------------------- subir archivo
}






?>