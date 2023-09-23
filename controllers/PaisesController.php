<?php
/**
 * controlador de paises, toda la logica de negocio del modulo de paises
 */

class PaisesController {
    //su modelo
    private $model;

    //constructor -> instacio su modelo
    public function __construct(){
        $this->model = new PaisesModel();
    }
    //leer
    public function read( $pais){
        ($pais > 0 ) ? $pais = sanitizarInput($pais) : $pais = 0;
        $paises = $this->model->read($pais);

        $output = array('data' => array());

        if (count($paises)){
            for ($i= 0; $i < count($paises) ; $i++) { 
                # code...
                $id = $paises[$i]['id_pais']; // id del pais
                $nombre = $paises[$i]['nombre']; // nombre
                $codigo = $paises[$i]['cod_pais']; //codigo del pais
                $abreviatura = $paises[$i]['abreviatura']; // abreviatura
                // bandera del pais
                $bandera = '<span class="flag flag-sm flag-country-'.strtolower($codigo).'"></span>';

                //checkbox seleccionar: para eliminacion multiple
                $checkbox = '<input class="form-check-input m-0 align-middle single-select" type="checkbox" aria-label="Seleccionar registro" data-id="'.$id.'" title="Seleccionar este registristro">';

                /*
                    botones de opciones: editar y eliminar pais
                */
                $botonOpciones ='
                    <td>
                            <span class="dropdown opciones-registro">
                              <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown">Opciones</button>
                              <div class="dropdown-menu">
                                <a class="dropdown-item btn botonEditar" href="#" title="Editar información de '.$nombre.'" data-id="'.$id.'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" /><line x1="13.5" y1="6.5" x2="17.5" y2="10.5" /></svg>
                                    Editar
                                </a>
                                <a class="btn btn-white dropdown-item botonEliminar" title="Eliminar información de '.$nombre.'" data-id="'.$id.'" href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    Eliminar
                                </a>
                              </div>
                            </span>
                    </td>
                ';

                $output['data'][] = array(
                    $checkbox, // checkbox seleccionar
                    $i+1, // numeracion
                    $bandera, // bandera del pais
                    $nombre, // nombre del pais
                    $abreviatura, // abviatura
                    $codigo, // codigo
                    $botonOpciones // botones editar, eliminar 
                );
            }


        } else{
            $output = [];
        }
        
        return $output;
    }
    // traer por ID
    public function traerPorId($id = 0){
        $respuesta = array('exito' => false, 'respuesta' =>'');
        if($id >= 0 && is_numeric($id)){
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
            $respuesta['respuesta'] = 'El ID del pais tiene que ser numérico';
        }

        return $respuesta;

    }
    //crear
    public function registrar($datos){
        $respuesta = array('exito' => false, 'mensaje' => array());
        if(count($datos) > 0 ){
            #rawurldecode decodifica los spacios en blanco
            $nombre = sanitizarInput(rawurldecode($datos['nombre']));
            $codigo = sanitizarInput($datos['codigo']);
            $abreviatura = sanitizarInput($datos['abreviatura']);
            

            // validaciones
            //nombre: obligatorio -> letras y espacios, max 30 chars
            // validaciones
            //nombre: obligatorio -> letras y espacios, max 30 chars
            $nombre_valido = $this->validarNombrePais($nombre)['nombre_valido'];
            if(!$nombre_valido){
                $respuesta['exito'] = false;
                $respuesta['mensaje'] [] = $this->validarNombrePais($nombre)['error'];
            }else {
                $nombre = $nombre;
            }
            
            //codigo: opcional -> solo letras, max 2 chars
            $codigo_valido = $this->validarCodigoPais($codigo)['codigo_valido'];
            if(!$codigo_valido){
                $respuesta['exito'] = false;
                $respuesta['mensaje'] [] = $this->validarCodigoPais($codigo)['error'];
            }else {
                $codigo = $codigo;
            }
            //abreviatura: opcional -> solo letras, max 3 chars
            $abreviatura_valida = $this->validarAbvPais($abreviatura)['abv_valida'];
            if(!$abreviatura_valida){
                $respuesta['exito'] = false;
                $respuesta['mensaje'] [] = $this->validarAbvPais($abreviatura)['error'];
            }else {
                $abreviatura = $abreviatura;
            }

            // paso de datos al modelo
            if ($nombre_valido && $codigo_valido && $abreviatura_valida){
                $resultado = $this->model->registrar($nombre, $codigo, $abreviatura);
                if ( $resultado != null ){
                    //convertir a entero
                    if ((int)$resultado['id'] < 1 ){ // ya existe pais con los mismo datos
                        $respuesta['exito'] = false;
                        $respuesta['mensaje'][] = 'Ya existe un pais con los mismos datos que has intentado registrar.';

                    }else {
                        $respuesta['exito'] = true;
                        $respuesta['mensaje'][] = 'Datos del pais registrados correctamente. el ID del pais es: '. $resultado['id'];
                    }


                } else{
                    $respuesta['exito'] = false;
                    $respuesta['mensaje'][] = 'No se ha podido registrar el pais, vuelve a intentar';
                }
            }else{

                return $respuesta;
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
            $id = sanitizarInput($datos['id_pais']);
            $nombre = sanitizarInput(rawurldecode($datos['nombre']));
            $codigo = sanitizarInput($datos['codigo']);
            $abreviatura = sanitizarInput($datos['abreviatura']);
            

            // validaciones
            // id: obligatorio -> numerico, mayor que 0 y menor que 1000
            $id_valido = $this->validarIdPais($id)['id_valido'];
            if(!$id_valido){
                $respuesta['exito'] = false;
                $respuesta['mensaje'] [] = $this->validarIdPais($id)['error'];
            } else {
                $id = $id;
            }
            //nombre: obligatorio -> letras y espacios, max 30 chars
            $nombre_valido = $this->validarNombrePais($nombre)['nombre_valido'];
            if(!$nombre_valido){
                $respuesta['exito'] = false;
                $respuesta['mensaje'] [] = $this->validarNombrePais($nombre)['error'];
            }else {
                $nombre = $nombre;
            }
            
            //codigo: opcional -> solo letras, max 2 chars
            $codigo_valido = $this->validarCodigoPais($codigo)['codigo_valido'];
            if(!$codigo_valido){
                $respuesta['exito'] = false;
                $respuesta['mensaje'] [] = $this->validarCodigoPais($codigo)['error'];
            }else {
                $codigo = $codigo;
            }
            //abreviatura: opcional -> solo letras, max 3 chars
            $abreviatura_valida = $this->validarAbvPais($abreviatura)['abv_valida'];
            if(!$abreviatura_valida){
                $respuesta['exito'] = false;
                $respuesta['mensaje'] [] = $this->validarAbvPais($abreviatura)['error'];
            }else {
                $abreviatura = $abreviatura;
            }

            // si datos ok ==> paso de datos al modelo
            if ($id_valido && $nombre_valido && $codigo_valido && $abreviatura_valida){
                $resultado = $this->model->actualizar($id, $nombre, $codigo, $abreviatura);
                if ( $resultado != null ){
                    //convertir a entero
                    if ((int)$resultado['mensaje'] < 0 ){ // ya existe pais con los mismo datos
                        $respuesta['exito'] = false;
                        $respuesta['mensaje'][] = 'Ya existe un pais con los mismos datos que has intentado registrar.';

                    }if ((int)$resultado['mensaje'] == 0 ){ // consulta no ejecuta en el stored procedure
                        $respuesta['exito'] = false;
                        $respuesta['mensaje'][] = 'Se ha intentado actualizar el pais, pero algo ha salido mal.';

                    }else if ((int)$resultado['mensaje'] == 1 ) { // toodo okkkk
                        $respuesta['exito'] = true;
                        $respuesta['mensaje'][] = 'Datos del pais actualizados correctamente';
                    }

                } else{
                    $respuesta['exito'] = false;
                    $respuesta['mensaje'][] = 'No se ha podido actualizar la información del pais, vuelve a intentar';
                }
            }else{
                return $respuesta;
            }

        } else {
            $respuesta['exito'] = false;
            $respuesta['mensaje'][] = 'No se ha recibido datos, asegurate de que hayas enviado datos';
        }

        return $respuesta;
    }

    //eliminar
    public function eliminar($pais){
        $respuesta = array('exito' => false, 'mensaje' => array());
        if( ($pais > 0) && is_numeric($pais) ){
            $resultado = $this->model->eliminar($pais);
            if ( $resultado ){

                if( array_key_exists('mensaje', $resultado)){
                    if ($resultado['mensaje'] == 1 ){
                        $respuesta['exito'] = true;
                        $respuesta['mensaje'] = 'Pais elimianado de la base de datos correctamente';
                    }else if($resultado['mensaje'] == 0){
                        $respuesta['exito'] = false;
                        $respuesta['mensaje'] = 'El pais no se ha podido eliminar, vuelve a intentarlo';
                    }
                }else {
                    $respuesta['exito'] = false;
                    $respuesta['mensaje'] = $resultado;
                }


            }else {
                $respuesta['exito'] = false;
                $respuesta['mensaje'] = 'Ha ocurrido un error al intentar eliminar el pais: '.$pais.' '.$resultado.' !!';
            }

        }else {
            $respuesta['exito'] = false;
            $respuesta['mensaje'] = 'El id del pais tiene que ser numerico y mayor que 0';
        }
         


        return $respuesta;
    }

    // validar datos del formulario
    // id: obligatorio -> numerico, mayor que 0 y menor que 1000
    public function validarIdPais($id){
        $validez = array('id_valido' => false, 'error' => '');
        if ($id && validarNumero($id)){
            if ($id <= 0 || $id > 999 ){
                if($id <= 0){
                    $validez['error'] = 'El id de pais tiene que ser mayor que 0';
                } else {
                    $validez['error'] = 'El id de pais tiene que ser menor que 1000';
                }
                $validez['id_valido'] = false;
            } else {
                $validez['id_valido'] = true;
            }
        }else {
            $validez['error'] = 'El id de pais no tiene un formato correcto';
        }

        return $validez;
    }
    //nombre: obligatorio -> letras y espacios, max 30 chars
    public function validarNombrePais($nombre){
        $validez = array('nombre_valido' => false, 'error'=> 'El nombre de país es obligatorio');
        if($nombre){
            if( strlen($nombre) > 30 || strlen($nombre) <= 0 ){
                if(strlen($nombre) > 30 ){  
                    $validez['error'] = 'El nombre del pais debe tener como máximo 30 carracteres';
                }else {
                    $validez['error'] = 'El nombre del pais no debe estar vacío';
                }
                $validez['nombre_valido'] = false;

            } else {

                if( validarTexto($nombre) ){
                    $validez['nombre_valido'] = true;
                }else {
                    $validez['nombre_valido'] = false;
                    $validez['error'] = 'El nombre debe contener solo letras y espacios'; 
                }

            }
        }
        return $validez;
    }
    
    //codigo: opcional -> solo letras, max 2 chars
    public function validarCodigoPais($codigo){
        $validez = array('codigo_valido' => false, 'error'=> '');
        if($codigo){
            if ( strlen($codigo) > 2 || (strlen($codigo) > 0 && !(validarLetras($codigo))) ){
                if (strlen($codigo) > 2){
                    $validez['error'] = 'El codigo de pais debe tener como máximo 2 carracteres'; 
                }else {
                    $validez['error'] = 'El codigo de pais tiene que contener solo letras'; 
                }
                $validez['codigo_valido'] = false;
            }else {
                $validez['codigo_valido'] = true;

            }
        }else{
            $validez['codigo_valido'] = true;
        }

        return $validez;

    }

    //abreviatura: opcional -> solo letras, max 3 chars
    public function validarAbvPais($abv){
        $validez = array('abv_valida' => false, 'error'=> '');
        if($abv){
            if ( strlen($abv) > 3 || (strlen($abv) > 0 && !(validarLetras($abv))) ){
                if (strlen($abv) > 3){
                    $validez['error'] = 'La abreviatura de pais debe tener como máximo 3 carracteres'; 
                }else {
                    $validez['error'] = 'La abreviatura de pais tiene que contener solo letras'; 
                }
                $validez['abv_valida'] = false;
            }else {
                $validez['abv_valida'] = true;

            }
        }else{
            $validez['abv_valida'] = true;
        }

        return $validez;
    }


}


?>