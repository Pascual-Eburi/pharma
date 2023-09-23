
<?php
# require_once('../models/EmployesModel.php');
class EmployesController {
	private $model; 

	public function __construct() {
		$this->model = new EmployesModel();
	}
    //crear - create
    public function create($empleado) {
        if(!empty($empleado) && $empleado > 0){
            $empleado = sanitizarInput($empleado);
            $datos = $this->model->create($empleado);
        }else{
            $datos = 'empleado vacio o mayor que 0';
        }

        return count($datos);
    }

    //leer - read
    public function get($cod_vendedor = '', $campos = []) {
        # limpira las variables
        if ((!empty($cod_vendedor)) && $cod_vendedor != '' ){
           $cod_vendedor = sanitizarInput($cod_vendedor); 
        }

        $camposSanitizados = array();
        if (count($campos) > 0){
            for ( $i = 0; $i < count($campos); $i++ ){
                $camposSanitizados[$i] = sanitizarInput($campos[$i]);
            }
            
        }
        return $resultado = $this->model->get( $cod_vendedor, $camposSanitizados );

    }

    // leer datos de login del empleado
    public function getLoginVendedor($email, $clave, $token = null) {
        #respuesta
        $respuestaLogin = array('exito' => false, 'mensaje' => array() );

        if ($token && !empty($token) ) {
            // guardar el token de seguridad en un array para ser procesado
            $key_token = array('_token' => $token);
            // verificar token: campo, origen, return bool, tiempo vida, reusable token
			if (NoCSRF::check('_token', $key_token, false, 60*10, true)) {
				//todo okey: token valido
                $email = sanitizarInput($email);
                $clave = MD5(sanitizarInput($clave));
                $login = $this->model->getLoginVendedor($email,$clave );
                if (count($login)){
                    if (($login['email'] == $email) && $login['clave'] == $clave ){
                        #datos correctos
                        $respuestaLogin['exito'] = true;
                        $respuestaLogin['mensaje'] = $login['codigo'];
                        return $respuestaLogin;
                    }else{
                        #email corecto pero clave incorrecta
                        if( ($login['email'] == $email) && $login['clave'] != $clave ){
        
                            # la contraseña es incorrecta
                            $respuestaLogin['exito'] = false;
                            $respuestaLogin['mensaje'] = 'La contraseña no es correcta';
                            return $respuestaLogin;
        
                        }else {
                            # error de datos
                            $respuestaLogin['exito'] = false;
                            $respuestaLogin['mensaje'] = 'Datos incorrectos, vuelve a intentar';
                            return $respuestaLogin;
                        }
                    }
                } else{
                    #el email no exite
                    $respuestaLogin['exito'] = false;
                    $respuestaLogin['mensaje'] = 'Email incorecto, pruebalo de nuevo';
                    return $respuestaLogin;
                }

    		}else{ // token invalido

                $respuestaLogin['exito'] = false;
                $respuestaLogin['mensaje'] = 'Token de seguridad incorrecto o expirado, recarge la pagina y vuelve a intentarlo';
                return $respuestaLogin;
            } // fin if else check token

        }else{ // token vacio
            $respuestaLogin['exito'] = false;
            $respuestaLogin['mensaje'] = 'Token de seguridad vacio, vuelve a intentarlo';
			return $respuestaLogin;
		}


        
    }

    //actualizar - update
    public function update($empleado){
        if($empleado > 0){
            $empleado = sanitizarInput($empleado);
        }else{
            $empleado = 0;
        }

        $datos = $this->model->update($empleado);

        return $datos;
    }

    //eliminar - delete
    public function delete($cod_vendedor = ''){
        # code...
    }
    
}



?>