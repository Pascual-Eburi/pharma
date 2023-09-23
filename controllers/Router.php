<?php
/**
 * 
 * 
 */
class Router {
    public $ruta; #ruta pasada como param del ruoter
    
    #ruta de la aplicacion
    private static $ruta_app = 'localhost/catalogoFarmacia/';

    public function __construct($ruta){

        #configuracion de las sesiones

        if(!isset($_SESSION)){ session_start();}

        if( !isset($_SESSION['autenticado']) ){
            $_SESSION['autenticado'] = false;
        }

        if ( $_SESSION['autenticado'] == true ){
            #el user se ha logueado - logica app
            
            //ruta solicitada            
            $this->ruta = isset($_GET['r']) ? $_GET['r'] : 'dashboard';
            //echo $this->ruta;
            //instaciar el controlador de vistas
            $controller = new ViewController();

            //
            switch ( $this->ruta ) {
                //bashboard                   
                case 'dashboard':
                    # mostrar vista
                    $controller->load_view('dashboard');
                break;

                case 'paises':
                    $paises = new PaisesController();
                    
                    if (!isset($_POST['accion'])){
                        $controller->load_view('paises');
                    }else{

                        if (isset($_POST['accion'])){
                            $accion = $_POST['accion'];

                            if( $accion == 'registrar' || $accion == 'actualizar'){
                                
                                $datos = array(
                                    'nombre' => $_POST['nombre'],
                                    'abreviatura' => $_POST['abreviatura'],
                                    'codigo' => $_POST['codigo']
                                );

                                if ($accion == 'registrar'){
                                    $registrado = $paises->registrar($datos);
                                    echo json_encode( $registrado );
                                    
                                } else {
                                    $datos['id_pais'] = $_POST['id_pais'];
                                    $actualizado = $paises->actualizar($datos);
                                    echo json_encode($actualizado);
                                }
                                

                            }else if($accion == 'eliminar'){ // eliminar pais
                                $resultado = $paises->eliminar($_POST['registro']);
                                echo json_encode($resultado);

                            } else if($accion == 'traer'){
                                $registros = $paises->read($_POST['registro']);
                                echo json_encode($registros);

                            }else if($accion == 'traerInfo'){
                                $registro = $paises->traerPorId($_POST['registro']);
                                echo json_encode($registro);
                            }

                        }
                        
                    }
                    
                break;

                case 'ccaa': //comunidades autonomas
                    $ccaa = new CcaaController();
                    
                    if (!isset($_POST['accion'])){
                        $controller->load_view('ccaa');
                    }else{

                        if (isset($_POST['accion'])){
                            $accion = $_POST['accion'];
                            if( $accion == 'registrar' || $accion == 'actualizar'){
                                $datos = array('nombre' => $_POST['nombre'], '_token' => $_POST['_token']);
                                if ($accion == 'registrar'){
                                    $registrado = $ccaa->registrar($datos);
                                    echo json_encode( $registrado );                                   
                                } else {
                                    $datos['id_ca'] = $_POST['id_ca'];
                                    $actualizado = $ccaa->actualizar($datos);
                                    echo json_encode($actualizado);
                                }
                                
                            }else if($accion == 'eliminar'){ // eliminar simple
                                $resultado = $ccaa->eliminar($_POST['id_registro'], $_POST['_token']);
                                echo json_encode($resultado);

                            }else if($accion == 'eliminar_multiple'){ // eliminar multiple
                            // convertir en array entendible para php
                            #$reg = array_map('intval', explode(',', $_POST['registros']));
                            $registros = json_decode('[' . $_POST['registros'] . ']', true);
                            $resultado = $ccaa->eliminarMultiple($registros, $_POST['_token']); /*, $_POST['_token']*/

                            # $reg = json_decode('[' . $_POST['registros'] . ']', true);
                            
                            //$respuesta = array('exito' => false, 'mensaje' => $reg);    
                            echo json_encode($resultado);
                            //echo json_encode($respuesta);

                            }else if($accion == 'traer'){
                                $registros = $ccaa->read($_POST['registro']);
                                echo json_encode($registros);

                            }else if($accion == 'traerInfo'){
                                $registro = $ccaa->traerPorId($_POST['registro']);
                                echo json_encode($registro);
                            }else if($accion == 'importar-registros'){
                                $resultado = (isset($_FILES['archivo'])) ? $ccaa->leerArchivo($_FILES['archivo']) : array('exito' => false , 'mensaje' => 'Ningun archivo recibido');;
                                echo json_encode($resultado);
                            }else if($accion == 'exportar-datos'){
                                $documento = $ccaa->exportarDatos($_POST['formato']);
                                echo json_encode($documento);
  
                            }

                        }
                        
                    }
                    
                break;
                
                #----------------------------------------------
                #       PROVINCIAS
                #-------------------------------------------------
                case 'provincias': 
                    $provincia = new ProvinciasController();
                    
                    if (!isset($_POST['accion'])){
                        $controller->load_view('provincias');
                    }else{

                        if (isset($_POST['accion'])){

                            $accion = $_POST['accion'];

                            if( $accion == 'registrar' || $accion == 'actualizar'){

                                $datos = array('nombre' => $_POST['nombre'],'id_ca' =>$_POST['id_ca'], '_token' => $_POST['_token']);

                                if ($accion == 'registrar'){
                                    $registrado = $provincia->registrar($datos);
                                    echo json_encode( $registrado );                                   
                                } else {
                                    $datos['id_provincia'] = $_POST['id_provincia'];
                                    $actualizado = $provincia->actualizar($datos);
                                    echo json_encode($actualizado);
                                }
                                
                            }else if($accion == 'eliminar'){ // eliminar simple
                                $resultado = $provincia->eliminar($_POST['id_registro'], $_POST['_token']);
                                echo json_encode($resultado);

                            }else if($accion == 'eliminar_multiple'){ // eliminar multiple
                            // convertir en array entendible para php
                            #$reg = array_map('intval', explode(',', $_POST['registros']));
                            $registros = json_decode('[' . $_POST['registros'] . ']', true);
                            $resultado = $provincia->eliminarMultiple($registros, $_POST['_token']); /*, $_POST['_token']*/

                            # $reg = json_decode('[' . $_POST['registros'] . ']', true);
                            
                            //$respuesta = array('exito' => false, 'mensaje' => $reg);    
                            echo json_encode($resultado);
                            //echo json_encode($respuesta);

                            }else if($accion == 'traer'){
                                $registros = $provincia->read($_POST['registro']);
                                echo json_encode($registros);

                            }else if($accion == 'traerInfo'){
                                $registro = $provincia->traerPorId($_POST['registro']);
                                echo json_encode($registro);
                            }else if($accion == 'importar-registros'){
                                $resultado = (isset($_FILES['archivo'])) ? $provincia->importarDatos($_FILES['archivo']) : array('exito' => false , 'mensaje' => 'Ningun archivo recibido');;
                                echo json_encode($resultado);
                            }else if($accion == 'exportar-datos'){
                                $documento = $provincia->exportarDatos($_POST['formato']);
                                echo json_encode($documento);
  
                            }

                        }
                        
                    }
                    
                break;
                #-----------------------------
                #   USUARIOS
                #-----------------------------
                case 'usuarios':
                    $usuarios = new UsuariosController();
                    
                    if (!isset($_POST['accion'])){
                        $controller->load_view('usuarios');
                    }else{
                        $accion = $_POST['accion'];
                        if($accion == 'avatar-info'){
                           $avatar = $usuarios->traerInfoAvatar();
                           echo ($avatar); 
                        }
                    }
                break;
                /*-------------------------------------------------
                 *  FABRICANTES
                 -------------------------------------------------*/
                case 'fabricantes':
                    $fabricantes = new FabricantesController();
                    if(!isset($_POST['accion'])){
                        $controller->load_view('fabricantes');
                    }else{
                        $accion = $_POST['accion'];
                        if($accion == 'traer-registros'){
                            $datos = $fabricantes->getFabricantes($_POST['registro']);
                            $registros = $fabricantes->generarDatosDataTable($datos);
                            echo json_encode($registros);
                            return;
                        }
                        if($accion == 'traerInfo'){
                            $datos = $fabricantes->getInfoFabricante($_POST['registro']);
                            echo json_encode($datos);
                            return;
                        }

                        if($accion == 'registrar' || $accion == 'actualizar'){
                            if(isset($_FILES['foto']) && isset($_POST['foto'])){
                                $_POST['foto'] = $_FILES['foto'];
                            }
                            // insertar
                            if( $accion == 'registrar'){
                                $insert = $fabricantes->registrar($_POST);
                                echo json_encode($insert);
                                return;
                            }
                            if($accion == 'actualizar'){
                                $update = $fabricantes->actualizar($_POST);
                                echo json_encode($update);
                                return ;
                            }
                         
                        }

                        // eliminar
                        if($accion == 'eliminar'){
                            $eliminar = $fabricantes->eliminar($_POST);
                            echo json_encode($eliminar);
                            return;
                        }

                        if($accion == 'eliminar_multiple'){
                            $registros = json_decode('['.$_POST['registros'].']', true);
                            $delete = $fabricantes->eliminarMultiple($registros, $_POST['_token']);

                            echo json_encode($delete);
                            return;
                        }

                        // exportar datos
                        if($accion == 'exportar-datos'){
                            $documento = $fabricantes->exportarDatos($_POST['formato']);

                            echo json_encode($documento);
                            return;
                        }

                        // importar registros
                        if($accion == 'importar-registros'){
                            $import = $fabricantes->importarDatos($_FILES['archivo']);
                            echo json_encode($import);
                            return ;
                        }

                    }
                break;
                                
                /*-------------------------------------------------
                 *  TRANSPORTISTAS
                 -------------------------------------------------*/
                 case 'transportistas':
                    $transportistas = new TransportistasController();
                    if(!isset($_POST['accion'])){
                        $controller->load_view('transportistas');
                    }else{
                        $accion = $_POST['accion'];
                        # traer registros para mostrar en tabla de datos
                        if($accion == 'traer-registros'){
                            $datos = $transportistas->getTransportistas($_POST['registro']);
                            $registros = $transportistas->generarDatosDataTable($datos);
                            echo json_encode($registros);
                            return;
                        }
                        # traer info del registro seleccionado
                        if($accion == 'traerInfo'){
                            $datos = $transportistas->getInfoTrasportista($_POST['registro']);
                            echo json_encode($datos);
                            return;
                        }

                        # registrar o editar
                        if($accion == 'registrar' || $accion == 'actualizar'){
                            if(isset($_FILES['foto']) && isset($_POST['foto'])){
                                $_POST['foto'] = $_FILES['foto'];
                            }
                            // insertar
                            if( $accion == 'registrar'){
                                $insert = $transportistas->registrar($_POST);
                                echo json_encode($insert);
                                return;
                            }
                            if($accion == 'actualizar'){
                                $update = $transportistas->actualizar($_POST);
                                echo json_encode($update);
                                return ;
                            }
                         
                        }

                        // eliminar
                        if($accion == 'eliminar'){
                            $eliminar = $transportistas->eliminar($_POST);
                            echo json_encode($eliminar);
                            return;
                        }

                        if($accion == 'eliminar_multiple'){
                            $registros = json_decode('['.$_POST['registros'].']', true);
                            $delete = $transportistas->eliminarMultiple($registros, $_POST['_token']);

                            echo json_encode($delete);
                            return;
                        }

                        // exportar datos
                        if($accion == 'exportar-datos'){
                            $documento = $transportistas->exportarDatos($_POST['formato']);

                            echo json_encode($documento);
                            return;
                        }

                        // importar registros
                        if($accion == 'importar-registros'){
                            $import = $transportistas->importarDatos($_FILES['archivo']);
                            echo json_encode($import);
                            return ;
                        }

                    }
                break;
                /*-------------------------------------------------
                 *  CLIENTES
                 -------------------------------------------------*/
                case 'clientes':
                    $clientes = new ClientesController();
                    if(!isset($_POST['accion'])){
                        $controller->load_view('clientes');
                    }else{
                        $accion = $_POST['accion'];
                        # traer registros para mostrar en tabla de datos
                        if($accion == 'traer-registros'){
                            $datos = $clientes->getClientes($_POST['registro']);
                            $registros = $clientes->generarDatosDataTable($datos);
                            echo json_encode($registros);
                            return;
                        }
                        # traer info del registro seleccionado
                        if($accion == 'traerInfo'){
                            $datos = $clientes->getInfoCliente($_POST['registro']);
                            echo json_encode($datos);
                            return;
                        }

                        # registrar o editar
                        if($accion == 'registrar' || $accion == 'actualizar'){
                            if(isset($_FILES['foto']) && isset($_POST['foto'])){
                                $_POST['foto'] = $_FILES['foto'];
                            }
                            // insertar
                            if( $accion == 'registrar'){
                                $insert = $clientes->registrar($_POST);
                               /* $date = new DateTime($_POST['fechaNacimiento']);
                                $_POST['fechaNacimiento'] = $date->format('Y-m-d');*/
                                
                                echo json_encode($insert);
                                return;
                            }
                            if($accion == 'actualizar'){
                                $update = $clientes->actualizar($_POST);
                                echo json_encode($update);
                                return ;
                            }
                         
                        }

                        // eliminar
                        if($accion == 'eliminar'){
                            $eliminar = $clientes->eliminar($_POST);
                            echo json_encode($eliminar);
                            return;
                        }

                        if($accion == 'eliminar_multiple'){
                            $registros = json_decode('['.$_POST['registros'].']', true);
                            $delete = $clientes->eliminarMultiple($registros, $_POST['_token']);

                            echo json_encode($delete);
                            return;
                        }

                        // exportar datos
                        if($accion == 'exportar-datos'){
                            $documento = $clientes->exportarDatos($_POST['formato']);

                            echo json_encode($documento);
                            return;
                        }

                        // importar registros
                        if($accion == 'importar-registros'){
                            $import = $clientes->importarDatos($_FILES['archivo']);
                            echo json_encode($import);
                            return ;
                        }

                    }
                break;

                /*-------------------------------
                 * Roles
                 ---------------------------*/
                case 'roles':
                    $roles = new RolesController();
                    if(!isset($_POST['accion'])){

                    }else{
                        $accion = $_POST['accion'];
                        if( $accion == 'traerInfo'){
                            $info = $roles->getRoles($_POST['registro']);
                            echo json_encode($info);  
                        }
                    }
                break;

                case 'salir':
                    $sesion_usuario = new SessionController();
                    $sesion_usuario->logout();
                break;

                case 'generarToken': // genera token de seguridad
                    echo NoCSRF::generate('_token');
                break;

                default:
                    # mostrar error
                    $controller->load_view('error404');
                break;
                
            }

           



        }else{ #mostrar form login, el usuario no se ha logeado
         
            
            if( !isset($_POST['email']) && !isset($_POST['clave']) ) {
                
                //mostrar formulario de autenticación
                $formularioLogin = new ViewController();
                $formularioLogin-> load_view('login');
            } else {
                

                $sesion_usuario = new SessionController();
                $login = $sesion_usuario->login($_POST);

                $respuesta['notificacion'] = array();
                /*
                if( $session['exito'] ) { #establecer sesion
                    
                    #establecer sesion del usuario y sus variables
                    $_SESSION['autenticado'] = true;
                    $_SESSION['cod_empleado'] = $session['mensaje'];

                    //output -> notificacion de exito
                    $respuesta['exito'] = true;
                    $respuesta['mensajes'] = 'Datos Correctos';
                    $respuesta['redireccionar'] = true;
                    $respuesta['url_redireccion'] = 'dashboard';

                   #header('Location: ./');

                } else {
                    # error en los datos
                    $respuesta['exito'] = false;
                    $respuesta['mensajes'] = $session['mensaje'];
                    $respuesta['redireccionar'] = false;
                    $respuesta['url_redireccion'] = '';
                    
                } */
                
                
                echo json_encode($login);
                

               

            }
        
        }


    }

    #devuelve la ruta de la app
    public function getRutaApp(){
        return self::$ruta_app;
    }



    public function __destruct(){
        #unset($this);
    }


}


?>