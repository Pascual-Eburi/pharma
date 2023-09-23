<?php 

class UsuariosController extends FileGeneratorController{
    private $model;
    private $modulo;
    private $vista;
    private $validacion;

	public function __construct() {
		$this->model = new UsuariosModel();
        $this->validacion = new InputsValidacionController();
        $this->modulo = 'crm';
        $this->vista = 'usuarios';

	}
    // establecer sesion del usuario
    public function login($datos_login) {
        #respuesta
    $respuesta = array('exito' => false, 'mensaje' => array());
        if (!key_exists('email', $datos_login) || !key_exists('clave', $datos_login)){
            $respueta['mensaje'] = 'Proporciona un email y una contraseña';
            return $respuesta;
        }
        //todo okey: token valido
        $email = sanitizarInput($datos_login['email']); #input sanitizado
        $clave = sanitizarInput($datos_login['clave']); #input sanitizado
        if(!$email || !$clave){
            $respueta['mensaje'] = 'El email y la contraseña son obligatorias';
            return $respuesta;
        }

        $login = $this->model->login($email, $clave);

        if(!$login['exito']){
            $respuesta['mensaje'] = $login['mensaje'];
            return $respuesta;
        }

        $respuesta['exito'] = true;
        $respuesta['mensaje'] = $login['mensaje'];

        return $respuesta;
        
    }

    // traer avatar e info del usuario
    public function traerInfoAvatar(){
        $usuario = $_SESSION['id_usuario'];
        $avatar_info = '';
        if(!$usuario){
            return 'No tienes acceso';
        }

        $info = $this->model->getUserInfoAvatar($usuario);
        if(!$info['exito']){

        }

        $url_img = $info['data'][0]['foto'];
        $nombre = explode(" ",$info['data'][0]['nombre']);
        $nombre = $nombre[0].' '.substr($nombre[1],0,1);
        $iniciales = explode(" ", $nombre);
        $iniciales = substr($iniciales[0], 0, 1).$iniciales[1];
        $roll = ucfirst($info['data'][0]['roll']);
        if ($roll == 'Root'){
            $roll = 'Administrador';
        }

        if( !$url_img){ 
            $foto = '<span class="avatar avatar-sm">
                        <span class="status-dot status-dot-animated badge bg-success"></span>
                        '.$iniciales.'
                    </span>';
        }else{
            $foto = '<span class="avatar avatar-sm" style="background-image: url(./public/avatars/usuarios/'.$url_img.')"></span>';
        }


        $avatar_info = $foto.'
        <div class="d-none d-xl-block ps-2">
          <div>'.$nombre.'</div>
          <div class="mt-1 small text-muted">
          <span class="badge bg-azure-lt">'.$roll.'</span>
          </div>
        </div>';

        return $avatar_info;
    }

    # validar y generar datos del usuario a registrar 
    public function generateInsertUserData($user){
        $insert = array('exito' => false, 'mensaje' => '');
        #-- nombre
        if(!key_exists('nombre', $user) || !$user['nombre']){
            $insert['mensaje'] = 'No se ha recibido un nombre...';
            return $insert;
        }
        
        #- email
        if(!key_exists('email', $user) || !$user['email']){
            $insert['mensaje'] = 'El email es estrictamente obligatorio.';
            return $insert;
        }
        # clave
        if(!key_exists('clave', $user) || !$user['clave']){
            $insert['mensaje'] = 'Se esperaba una clave temporal.';
            return $insert;
        }
        # calve repetida
        if(!key_exists('clave_repetida', $user) || !$user['clave_repetida']){
            $insert['mensaje'] = 'Se esperaba una clave temporal repetida.';
            return $insert;
        }
        # asegurarse de que las claves coincidan
        $clav_iguales = $this->validacion->comparDosClaves($user['clave'],$user['clave_repetida']);
        if( !$clav_iguales){
            $insert['mensaje'] = 'Las claves no son iguales, revisalas..';
            return $insert;
        }

        
        # --- validar y generar data de usuario para insertar ---
       
        #------- asignaciones -------------------
        $nombre = $this->validacion->sanitizarInput($user['nombre']);
        $email = $this->validacion->sanitizarInput($user['email']);
        $clave = $this->validacion->sanitizarInput($user['clave']);
        $foto = ($user['foto']) ? $user['foto'] : NULL;
        $id_roll = ($user['id_roll'])? $this->validacion->sanitizarInput($user['id_roll']) : NULL;
        
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

        //todo ok --
        if($foto){
            $carpeta = 'usuarios';
            $avatar = $this->subirArchivo( $carpeta, $foto);
            if(!$avatar){
                $insert['mensaje'] = 'No se ha podido subir la foto al servidor';
                return $insert;
            }
        }

        $data_user = array(
            'foto' => (isset($avatar)) ? $avatar: NULL,
            'nombre' => $nombre,
            'email' => $email,
            'clave' => MD5($clave),
            'id_roll' => $id_roll
        );

        # todo ok 
        $insert['exito'] = true;
        $insert['mensaje'] = $data_user;
        return $insert;
    }
    #validar info usuario a actualizar
    public function validateUserUpdateData($user){
        $valid = array('exito' => false, 'mensaje' => array());
        $data_usuario = array('id' => '', 'data' =>array());
        if(! $user || !is_array($user)){
            $valid['mensaje'] = 'Se esperaba un array con la información del usuario.';
            return $valid;
        }
        
        #-------    campos obligatorios ---------------------
        #-- id_usuario
        if(!key_exists('id_usuario', $user) || !$user['id_usuario']){
            $valid['mensaje'] = 'Se esperaba un id de usuario.';
            return $valid; 
        }
        # asiginacion
        $id_usuario = $this->validacion->sanitizarInput($user['id_usuario']);
        #validacion
        $usuario_v = $this->validacion->validarNumero('ID usuario', $id_usuario, 1, 11);
        if(!$usuario_v['valido']){
            $valid['mensaje'] = $usuario_v['error'];
            return $valid;
        }

        # ok, insertar al array de usuario
        $data_usuario['id'] = $id_usuario;
        #-- nombre
        if(key_exists('nombre', $user)){
            if(!$user['nombre']){
                $valid['mensaje'] = 'Se esperaba un nombre de fabricante.';
                return $valid;
            }
            # asiginacion
            $nombre = $this->validacion->sanitizarInput($user['nombre']);
            # validacion
            $nom_v = $this->validacion->validarNombre('Nombre', $nombre, 1, 50);
            if(!$nom_v['valido']){
                $valid['mensaje'] = $nom_v['error'];
                return $valid;
            }
            # ok, insertar al array de usuario
            $data_usuario['data']['nombre'] = $nombre;
        }
        
        #- email
        if(key_exists('email', $user)){
            if(!$user['email']){
                $valid['mensaje'] = 'El email del fabricante es obligatorio.';
                return $valid;
            }
            $email = $this->validacion->sanitizarInput($user['email']);
                    
            # email
            $em_v = $this->validacion->validarEmail('Email', $email, 1, 100);
            if(!$em_v['valido']){
                $valid['mensaje'] = $em_v['error'];
                return $valid;
            }
            # ok, insertar al array de usuario
            $data_usuario['data']['email'] = $email;
        }

        #- roll
        if(key_exists('id_roll', $user)){
            $id_roll = ($user['id_roll']) ? $this->validacion->sanitizarInput($user['id_roll']) : NULL;
            # id_roll
            if($id_roll){
                $roll_v = $this->validacion->validarNumero('Roll', $id_roll, 1, 11);
                if(!$roll_v['valido']){
                    $valid['mensaje'] = $roll_v['error'];
                    return $valid;
                }

            }
            # ok, insertar al array de usuario
            $data_usuario['data']['id_roll'] = $id_roll;
        }

        #- foto
        if(key_exists('foto', $user)){
            $foto = ($user['foto']) ? $user['foto'] : NULL;
            # foto
            if($foto){
                $extensiones_validas = array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG');
                $foto_v = $this->validacion->validarExtensionArchivo($foto, $extensiones_validas);
                if(!$foto_v['valido']){
                    $valid['mensaje'] = $foto_v['error'];
                    return $valid;
                }
                    // todo ok, subir al servidor
                $carpeta = 'usuarios';
                $avatar = $this->subirArchivo( $carpeta, $foto);
                if(!$avatar){
                    $valid['mensaje'] = 'No se ha podido subir la foto al servidor';
                    return $valid;
                }

            }

            # ok, insertar al array de usuario
            $data_usuario['data']['foto'] = (isset($avatar)) ? $avatar : $foto;
        }

        $valid['exito'] = true;
        $valid['mensaje'] = $data_usuario;

        return $valid;
    }

    // informacion del usuario
    public function informacionUsuario($id_usuario = 0){
        $resultado = array('exito' => false, 'mensaje' => array());
        if($id_usuario < 0 ){
            $resultado['mensaje'] = 'Se esperaba un id de usuario mayor o igual a 0';
            return $resultado;
        }
    }


}

?>