<?php

class SessionController{
    private $session;

    public function __construct() {
        # instaciando en controlador de empleados
        $this->session = new UsuariosController();
    }

	/**
	 * solicita y retorna los datos de login del usuario si los hay
	 */
	public function login($datos) {
		$login = $this->session->login($datos);
		if(!$login['exito']){
			return $login;
		}

		$id_roll = $login['mensaje']['id_roll'];
		// obtener los permisos de su roll
		if(!$id_roll){
			$login['exito'] = false;
			$login['mensaje'] = 'No tienes asignado un roll, no puedes iniciar sesion.';
			return $login;
		}

		// iniciar la sesion
		#establecer sesion del usuario
		$_SESSION['autenticado'] = true;
		$_SESSION['id_usuario'] = $login['mensaje']['id_usuario'];

		$login['mensaje'] = 'Datos correctos, configurante tu sessión...';
		$login['redirigir'] = true;
		$login['url'] = 'dashboard';
		return $login;
		
	}


	/**
	 * destruye la sesion
	 */
	public function logout() {
		session_start();
		session_destroy();
		header('Location: ./');
	}
}

?>