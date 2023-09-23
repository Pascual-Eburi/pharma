<?php

class FabricantesController extends FileGeneratorController{
    private $model;
    private $validacion;


    public function __construct(){
        $this->model = new FabricantesModel();
        $this->validacion = new InputsValidacionController();
    }

    public function getFabricantes($id = 0){
        if($id < 0 || !is_numeric($id)){
            return false;
        }
        $fabricantes = $this->model->getFabricantes($id);
        if(!$fabricantes['exito']){
            return false;
        }

        $registros = $fabricantes['data'];
        return $registros;
    }
    public function getInfoFabricante($id = null){
        $info = array('exito' => false, 'respuesta' => '');
        if(!$id || !is_numeric($id)){
            $info['respuesta'] = 'ID de fabricante no valido';
            return $info;
        }
        $datos = $this->getFabricantes($id);
        if(!$datos){
            $info['respuesta'] = 'No hay datos para el fabricante seleccionado';
            return $info;
        }
        $info['exito'] = true;
        $info['respuesta'] = $datos;
        return $info;
        
    }

    public function registrar($fabricante){
        $insert= array('exito' => false, 'mensaje' => '');

        if(! $fabricante || !is_array($fabricante)){
            $insert['mensaje'] = 'Se esperaba un array con la información del fabricante.';
            return $insert;
        }

        #-------    campos obligatorios ---------------------
        #-- nombre
        if(!key_exists('nombre', $fabricante) || !$fabricante['nombre']){
            $insert['mensaje'] = 'Se esperaba un nombre de fabricante.';
            return $insert;
        }
        
        #- email
        if(!key_exists('email', $fabricante) || !$fabricante['email']){
            $insert['mensaje'] = 'El email del fabricante es obligatorio.';
            return $insert;
        }
        # clave
        if(!key_exists('clave', $fabricante) || !$fabricante['clave']){
            $insert['mensaje'] = 'Se esperaba una clave temporal.';
            return $insert;
        }
        # calve repetida
        if(!key_exists('clave_repetida', $fabricante) || !$fabricante['clave_repetida']){
            $insert['mensaje'] = 'Se esperaba una clave temporal repetida.';
            return $insert;
        }
        # asegurarse de que las claves coincidan
        $clav_iguales = $this->validacion->comparDosClaves($fabricante['clave'],$fabricante['clave_repetida']);
        if( !$clav_iguales){
            $insert['mensaje'] = 'Las claves no son iguales, revisalas..';
            return $insert;
        }

        #------- asignaciones -------------------
        $nombre = $this->validacion->sanitizarInput($fabricante['nombre']);
        $email = $this->validacion->sanitizarInput($fabricante['email']);
        $clave = $this->validacion->sanitizarInput($fabricante['clave']);
        $foto = ($fabricante['foto']) ? $fabricante['foto'] : NULL;
        $id_roll = ($fabricante['id_roll'])? $this->validacion->sanitizarInput($fabricante['id_roll']) : NULL;
        
        $pais = ($fabricante['id_pais']) ? $fabricante['id_pais'] : NULL;

        # --------- validaciones de datos --------------------
        # nombre
        $nom_v = $this->validacion->validarNombre('Nombre', $nombre, 1, 50);
        if(!$nom_v['valido']){
            $insert['mensaje'] = $nom_v['error'];
            return $insert;
        }

        # email
        $em_v = $this->validacion->validarEmail('Email', $email, 1, 100);
        if(!$em_v['valido']){
            $insert['mensaje'] = $em_v['error'];
            return $insert;
        }

        # foto
        if($foto){
            $extensiones_validas = array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG');
            $foto_v = $this->validacion->validarExtensionArchivo($foto, $extensiones_validas);
            if(!$foto_v['valido']){
                $insert['mensaje'] = $foto_v['error'];
                return $insert;
            }

            # subir img al servidor
        }

        # id_roll
        if($id_roll){
            $roll_v = $this->validacion->validarNumero('Roll', $id_roll, 1, 11);
            if(!$roll_v['valido']){
                $insert['mensaje'] = $roll_v['error'];
                return $insert;
            }

        }

        # id_pais
        if($pais){
            $pais_v = $this->validacion->validarNumero('Pais', $pais, 1, 3);
            if(!$pais_v['valido']){
                $insert['mensaje'] = $pais_v['error'];
                return $insert;
            }
        }

        //todo ok --
        if($foto){
            $carpeta = 'usuarios';
            $avatar = $this->subirArchivo( $carpeta, $foto);
            if(!$avatar){
                $insert['mensaje'] = 'No se ha podido subir la foto al servidor';
                return $insert;
            }
        }


        $data_usuario = array(
            'foto' => (isset($avatar)) ? $avatar: NULL,
            'nombre' => $nombre,
            'email' => $email,
            'clave' => MD5($clave),
            'id_roll' => $id_roll
        );
        $data_fabricante = array(
            'tipo' => 'fabricante',
            'data' => array(
                'pais' => $pais,
                'id_usuario' => NULL 
            )
        ); 

        $insert_usuario = $this->model->insertUser($data_usuario, $data_fabricante);
        if( !$insert_usuario['exito']){
            if(isset($avatar)){
                $directorio = "./public/avatars/usuarios/";
                $delete_avatar = $this->eliminarArchivo($directorio, $avatar);
                if(!$delete_avatar){
                    $insert_usuario['mensaje'] = $insert_usuario['mensaje'].'. Y no se ha podido eliminar la foto del servidor';
                }
            }
            
        }

        return $insert_usuario;


    }

    public function actualizar($fabricante){
        $update = array('exito' => false, 'mensaje' => '');
        $data_usuario = array('id' => '', 'data' =>array());
        $data_fabricante = array('tipo' => 'fabricante','id'=> '', 'data' => array());


        if(! $fabricante || !is_array($fabricante)){
            $update['mensaje'] = 'Se esperaba un array con la información del fabricante.';
            return $update;
        }
         #-------    campos obligatorios ---------------------
        #-- id_usuario
        if(!key_exists('id_usuario', $fabricante) || !$fabricante['id_usuario']){
            $update['mensaje'] = 'Se esperaba un id de usuario.';
            return $update; 
        }
        # asiginacion
        $id_usuario = $this->validacion->sanitizarInput($fabricante['id_usuario']);
        #validacion
        $usuario_v = $this->validacion->validarNumero('ID usuario', $id_usuario, 1, 11);
        if(!$usuario_v['valido']){
            $update['mensaje'] = $usuario_v['error'];
            return $update;
        }

        # ok, insertar al array de usuario
        $data_usuario['id'] = $id_usuario;

        #-- id_fabricante
        if(!key_exists('id_fabricante', $fabricante) || !$fabricante['id_fabricante']){
            $update['mensaje'] = 'Se esperaba un id de fabricante.';
            return $update; 
        }
        # asiginacion
        $id_fabricante = $this->validacion->sanitizarInput($fabricante['id_fabricante']);
        #validacion
        $fabr_v = $this->validacion->validarNumero('ID fabricante', $id_fabricante, 1, 3);
        if(!$fabr_v['valido']){
            $update['mensaje'] = $fabr_v['error'];
            return $update;
        }

        # ok, insertar al array de fabricante
        $data_fabricante['id'] = $id_fabricante;


        #-- nombre
        if(key_exists('nombre', $fabricante)){
            if(!$fabricante['nombre']){
                $update['mensaje'] = 'Se esperaba un nombre de fabricante.';
                return $update;
            }
            # asiginacion
            $nombre = $this->validacion->sanitizarInput($fabricante['nombre']);
            # validacion
            $nom_v = $this->validacion->validarNombre('Nombre', $nombre, 1, 50);
            if(!$nom_v['valido']){
                $update['mensaje'] = $nom_v['error'];
                return $update;
            }
            # ok, insertar al array de usuario
            $data_usuario['data']['nombre'] = $nombre;
        }
        
        #- email
        if(key_exists('email', $fabricante)){
            if(!$fabricante['email']){
                $update['mensaje'] = 'El email del fabricante es obligatorio.';
                return $update;
            }
            $email = $this->validacion->sanitizarInput($fabricante['email']);
                    
            # email
            $em_v = $this->validacion->validarEmail('Email', $email, 1, 100);
            if(!$em_v['valido']){
                $update['mensaje'] = $em_v['error'];
                return $update;
            }
            # ok, insertar al array de usuario
            $data_usuario['data']['email'] = $email;
        }

        #- roll
        if(key_exists('id_roll', $fabricante)){
            $id_roll = ($fabricante['id_roll']) ? $this->validacion->sanitizarInput($fabricante['id_roll']) : NULL;
            # id_roll
            if($id_roll){
                $roll_v = $this->validacion->validarNumero('Roll', $id_roll, 1, 11);
                if(!$roll_v['valido']){
                    $update['mensaje'] = $roll_v['error'];
                    return $update;
                }

            }
            # ok, insertar al array de usuario
            $data_usuario['data']['id_roll'] = $id_roll;
        }
        #- id pais
        if( key_exists('id_pais', $fabricante) ){
            $pais = ($fabricante['id_pais']) ? $fabricante['id_pais'] : NULL;
            # id_pais
            if($pais){
                $pais_v = $this->validacion->validarNumero('Pais', $pais, 1, 3);
                if(!$pais_v['valido']){
                    $update['mensaje'] = $pais_v['error'];
                    return $update;
                }
            }
            # ok, insertar al array de fabricante
            $data_fabricante['data']['pais'] = $pais;
        }

        #- foto
        if(key_exists('foto', $fabricante)){
            $foto = ($fabricante['foto']) ? $fabricante['foto'] : NULL;
            # foto
            if($foto){
                $extensiones_validas = array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG');
                $foto_v = $this->validacion->validarExtensionArchivo($foto, $extensiones_validas);
                if(!$foto_v['valido']){
                    $update['mensaje'] = $foto_v['error'];
                    return $update;
                }
                 // todo ok, subir al servidor
                $carpeta = 'usuarios';
                $avatar = $this->subirArchivo( $carpeta, $foto);
                if(!$avatar){
                    $update['mensaje'] = 'No se ha podido subir la foto al servidor';
                    return $update;
                }

            }

            # ok, insertar al array de usuario
            $data_usuario['data']['foto'] = (isset($avatar)) ? $avatar : $foto;

        }
        

        $actualizar = $this->model->updateUsert($data_usuario, $data_fabricante);

        if($actualizar['exito']){
            // eliminar la foto antiguda del servidor
            if(key_exists('foto', $actualizar) && $actualizar['foto']){
                $directorio = "./public/avatars/usuarios/";
                $delete_avatar = $this->eliminarArchivo($directorio, $actualizar['foto']);
                if(!$delete_avatar){
                    $actualizar['mensaje'] = $actualizar['mensaje'].'. Y no se ha podido eliminar la foto del servidor';
                }
            }
        }
        $update['exito'] = $actualizar['exito'];
        $update['mensaje'] = $actualizar['mensaje'];


        return $update;

    }

    public function eliminar($fabricante = null){
        $delete = array('exito' => false, 'mensaje' => '');
        # fabricante tiene que ser un array
        if(!$fabricante || !is_array($fabricante)){
            $delete['mensaje'] = 'Se esperaba un array con la información del fabricante';
            return $delete;
        }

        # tiene que existir un id de fabricante (id registro)
        if(!key_exists('id_registro', $fabricante) || !$fabricante['id_registro']){
            $delete['mensaje'] = 'El ID de fabricante es obligatorio';
            return $delete;  
        }
        $id_fabricante = $this->validacion->sanitizarInput($fabricante['id_registro']);
        # tiene que existir un id de usuario
        if(!key_exists('id_usuario', $fabricante) || !$fabricante['id_usuario']){
            $delete['mensaje'] = 'El ID de usuario es obligatorio';
            return $delete;  
        }
        $id_usuario = $this->validacion->sanitizarInput($fabricante['id_usuario']);

        # tiene que existir un token de seguridad
        if(!key_exists('_token', $fabricante) || !$fabricante['_token']){
            $delete['mensaje'] = 'Se esperaba un token de seguridad';
            return $delete;  
        }

        #ok, hay token... validarlo
        if (!(NoCSRF::check('_token', $fabricante, false, 60*10, false)) ){
            $delete['mensaje'] = 'Token de seguridad invalido o expirado';
            return $delete; 
        }

        #validaciones
        $fab_v = $this->validacion->validarNumero('Id fabricante', $id_fabricante, 1, 3);
        if(!$fab_v['valido']){
            $delete['mensaje'] = $fab_v['error'];
            return $delete;
        }

        $user_v = $this->validacion->validarNumero('Id usuario', $id_usuario, 1, 11);
        if(!$user_v['valido']){
            $delete['mensaje'] = $user_v['error'];
            return $delete;
        }
        $userType = array('tipo' => 'fabricante', 'data' => array('id_fabricante' =>$id_fabricante));
        #ok - todo ok, eliminar 
        $delete_db = $this->model->deleteUser($id_usuario, $userType);
        #eliminar foto si hay
        if($delete_db['exito']){
            if(key_exists('foto', $fabricante) && !empty($fabricante['foto'])  ){
                $foto = $fabricante['foto'];
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
            $val = $this->validacion->validarNumero('Id fabricante', $registro, 1, 3);
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
        if( count($data) > 0 ){
            // recorrer los registros
            for( $i = 0; $i < count($data); $i++){
                $id_fabricante = $data[$i]['id_fabricante'];
                $id_usuario = $data[$i]['id_usuario'];
                $nombre = $data[$i]['nombre'];
                $roll = ucfirst($data[$i]['roll']);
                $tot_farmacos = $data[$i]['farmacos'];
                

                // generar avatar
                $url_foto = $data[$i]['foto'];
                if(!$url_foto){
                    $arr_nombre = explode(' ', $nombre);
                    $iniciales = '';
                    for($j = 0; $j < count($arr_nombre); $j++){
                        $iniciales .= substr( $arr_nombre[$j], 0, 1);
                    }
                    if(strlen($iniciales) <= 1){
                        $color_avatar = 'bg-green-lt';
                    }else if(strlen($iniciales) == 2){
                        $color_avatar = 'bg-blue-lt';
                    }else if(strlen($iniciales) == 3){
                        $color_avatar = 'bg-yellow-lt';
                    }else{
                        $color_avatar = '';
                    }

                    $foto = '<span class="avatar rounded-circle '.$color_avatar.' me-2">'.$iniciales.'</span>';
                }else{

                    $foto = '<span class="avatar rounded-circle me-2 bg-transparent" style="background-image: url(./public/avatars/usuarios/'.$url_foto.')"></span>';
                }
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

                // div pais y bandera del fabricante
                if($data[$i]['pais']){
                    $div_pais = '
                        <span class="flag flag-country-'.strtolower( $data[$i]['cod_pais']).'"></span>
                        '.ucwords(strtolower( $data[$i]['pais'])).'
                    ';
                }else{
                    $div_pais = '<span class="badge bg-warning me-1"></span> No asignado';
                }

                // farmacos
                if($tot_farmacos >= 30){ // verde
                    $color = 'bg-success';
                }else if($tot_farmacos >= 10){ // amarillo
                    $color = 'bg-warning';
                }else if($tot_farmacos >= 1){ // rojo
                    $color = 'bg-danger';
                }else{// gris
                    $color = 'bg-secondary';
                }

                $farmacos = '
                    <span class="badge  '.$color.' me-1"></span>
                    '.$tot_farmacos.'
                ';

                // checkbox seleccion
                $checkbox = '<input class="form-check-input m-0 align-middle single-select" type="checkbox" aria-label="Seleccionar registro" data-id="'.$id_fabricante.'" title="Seleccionar este registristro">';

                // botones de opciones
                $botonOpciones ='
                <td class="d-print-none">
                        <span class="dropdown opciones-registro">
                          <button class="btn btn-sm dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Opciones</button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item btn botonEditar" href="#" title="Editar información de '.$nombre.'" data-id="'.$id_usuario.'" data-fabricante = "'.$id_fabricante.'">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" /><line x1="13.5" y1="6.5" x2="17.5" y2="10.5" /></svg>
                                </span>
                                <span>Editar</span>
                            </a>
                            <a class="btn btn-white dropdown-item botonEliminar" title="Eliminar información de '.$nombre.'" data-id="'.$id_usuario.'" data-fabricante = "'.$id_fabricante.'" href="#">
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
                    $div_pais, // pais,bandera
                    ($roll)? $roll: '<span class="badge bg-warning me-1"></span> No asignado
                    ',
                    $farmacos,
                    $botonOpciones // botones editar, eliminar 
                );
                

            } # for
        } # if

        return $datos;
    }
    /**
     * Generar datos para exportar a un documento
     */
    public function generarDatosExport($cols_excluidas = null){
        $registros = $this->model->getFabricantes();
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
        $excluidos = ['id_usuario', 'id_fabricante', 'id_roll', 'id_pais', 'cod_pais', 'foto'];
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
            'titulo' => 'LISTA DE FABRICANTES' 
            
        );

        switch ($formato) {
            case 'xlsx':
                # generar excel
                $informacion['tipo_documento'] = 'Xlsx';
                $informacion['color_titulo'] = 'FABF8F';
                $informacion['color_encabezados'] = 'EBF1DE';
                $informacion['nombre_documento'] = 'fabricantes';
                $archivo = $this->generarExcel($informacion);
            break;
            case 'csv':
                $informacion['tipo_documento'] = 'Csv';
                $informacion['color_titulo'] = 'FABF8F';
                $informacion['color_encabezados'] = 'EBF1DE';
                $informacion['nombre_documento'] = 'fabricantes';
                $archivo = $this->generarExcel($informacion);
            break;
            case 'pdf':
                //$informacion['tipo_documento'] = 'pdf';
                $informacion['nombre_documento'] = 'fabricantes';
                $archivo = $this->generarPDF($informacion);
            break;
            case 'print':
                $informacion['descripcion'] = 'Esta es la lista de fabricantes registrados hasta la fecha';
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

        $encabezados_permitidos = array('nombre', 'email'); // encabezados permitidos
        $encabezados = array_shift($registros); // array encabezados

        // verificar los encabezados
        foreach ($encabezados as $encabezado) {
            if(!in_array($encabezado, $encabezados_permitidos)){
                // check encabezados
                $respuesta['mensaje'] = 'Los encabezados solo pueden ser: nombre y email';
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
        $data_fabricante = array(
            'tipo' => 'fabricante', 'data' => array('pais' => NULL,'id_usuario' => NULL )
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
                    /*if($registros[$i][$j] == 0 || $registros[$i][$j] == '0'){
                        $registros[$i][$j] = NULL;
                    }*/
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
                
            } // end loop validacion

            // checkear si hay errores
            if ($errores == 0){
                $key = '';
                for($k= 0; $k < count($registros[$i]); $k++){
                    $key = $encabezados[$k];
                    $data_usuario[$key] = $registros[$i][$k];
                }
                $registros_validos[] = [$data_usuario, $data_fabricante];
            }else{
                $registros_invalidos['campos'][] = array ('registros'=>$registros[$i], 'errores' => $sms_error);
            } //check errores
        } // loop a los registros para validaciones
        
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