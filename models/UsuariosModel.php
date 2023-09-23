<?php

class UsuariosModel extends Database{
    // nombre tabla 
    protected $tabla = 'usuario';

    // verificar login
    public function login ($email = null, $clave = null){
        # verificar datos 
        $resultado = array('exito' => false, 'mensaje' => '');
        if (!$email && !$clave) {
            $resultado['mensaje'] = 'Se esperaba un email y una clave';
            return $resultado;
        }

        try{
           // verificar datos login
           $sql = 'CALL loginUsuario(:email, :clave, @id_usuario, @id_roll)';
           
           $sentencia = $this->conexion->prepare($sql);
           $sentencia->bindParam(':email', $email, PDO::PARAM_STR, 50);
           $sentencia->bindParam(':clave', $clave, PDO::PARAM_STR, 255);
           $sentencia->execute();
            $usuario = $sentencia->rowCount();
           if(!$usuario){
                $resultado['mensaje'] = 'Email o contraseña incorrecta, vuelve a intentarlo...';
                return $resultado;
           }
           if( $sentencia->rowCount() == 1 ){
                $resultado['exito'] = true;
                $resultado['mensaje'] =  $this->conexion->query("SELECT @id_usuario as id_usuario, @id_roll as id_roll")->fetch(PDO::FETCH_ASSOC);
                return $resultado;
            }

            if($sentencia->rowCount() > 1){
                $resultado['mensaje'] = 'En estos momentos no puedes iniciar sesión, vuelve a intentarlo más tarde.';
                return  $resultado;
            }
            

        }catch(PDOException $error){
            $resultado['mensaje'] =  $error->getMessage();
            return $resultado;
        }


    } // fin login

    // info usuario
    public function getUsers($id_usuario){
        if($id_usuario){
            // obtner el subtipo del usuario
            #todas los subtipos de usuario
            $sql = "SELECT TABLE_NAME as tabla FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = ? AND column_name LIKE 'id_usuario' AND TABLE_NAME != 'usuario'" ;
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->execute([$this->getDBName()]);
            if($sentencia->rowCount() > 0){
                $resultados = $sentencia->fetchAll(PDO::FETCH_COLUMN);
                $subtipos = array();
                #recorrer todos los subtipos para encontrar en cual esta el usuario
                foreach ($resultados as $tabla){
                    $sql_subtipo = $this->conexion->prepare("SELECT id_usuario FROM {$tabla} WHERE id_usuario = ?");
                    $sql_subtipo->execute([$id_usuario]);
                    if($sql_subtipo->rowCount() > 0){
                        $subtipos[] = $tabla;
                    }
                }
                $resultado = $subtipos;
            }else{
                $resultado = 'no data';
            }
        }
        return $resultado;
    }

    //obtener info avatar usuario
    public function getUserInfoAvatar($id_usuario = null){
        $resultado = array('exito'=>false, 'data' => array());
        if(!$id_usuario || $id_usuario <= 0){
            $resultado['data'] ='ID de usuario no valido';
            return $resultado;
        }
        try{
            $sql = "SELECT foto, usuario.nombre,roles.nombre as roll FROM usuario INNER JOIN roles ON usuario.id_roll = roles.id_roll WHERE usuario.id_usuario = ?";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->execute([$id_usuario]);
            if($sentencia->rowCount() > 0){
                $resultado['exito'] = true;
                $resultado['data'] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
                return $resultado;
            }else{
                $resultado['data'] ='No se ha encotrado información para este usuario';
                return $resultado;
            }

        } catch(PDOException $error){
            $resultado['data'] ='ERROR: '.$error->getMessage();
            return $resultado;
        }
    }

    //insertar usuario
    public function insertUser($user = null, $usertType = null){
        $resultado = array('exito' => false, 'mensaje' => '');
        # la informacion del usuario tiene que venir en un array
        if(!$user || !is_array($user)){
            $resultado['mensaje'] = 'Información de usuario no valida';
            return $resultado;
        }

        # si tipo de usuario, entonces se tiene que hacer la insercion de los datos en la tabla de su subtipo
        if($usertType && !is_array($usertType)){
            $resultado['mensaje'] = 'El subtipo de usuario no esta en un array';
            return $resultado; 
        }
        #check que exista las keys correctas en la info del subtipo
        if($usertType && (!key_exists('tipo', $usertType) || !key_exists('data', $usertType))){
            $resultado['mensaje'] = 'Revisa la informacion del subtipo de usuario...';
            return $resultado;
        }

        #validar el subtipo elegido, si existe subtipo
        if($usertType){
            $subtipos_validos = ['fabricante', 'empleado', 'transportista', 'cliente'];
            $subtipo = $tabla = $usertType['tipo'];
            if(!in_array($subtipo, $subtipos_validos)){
                $resultado['mensaje'] = 'El subtipo elegido no es valido';
                return $resultado; 
            }

        }

        // generar params para cada columna de usuario
        $params_usuario = array();
        foreach(array_keys($user) as $param){
            $params_usuario [] = ":{$param}";
        }
        $values_usuario = array_values($user);
        try{
            $this->conexion->beginTransaction();
            $sql = "INSERT INTO {$this->tabla} (".implode(',', array_keys($user)).") VALUES (".implode(',', $params_usuario).")";
            $sentencia = $this->conexion->prepare($sql);
            #bind todos los valores
            for($i = 0; $i < count($values_usuario); $i++){
                $sentencia->bindParam("$params_usuario[$i]", $values_usuario[$i]);
            }
            $sentencia->execute();
            #-- guardar id_usuario en la info del subtipo, si subtipo
            if($usertType){
                if($sentencia->rowCount() > 0){
                    $usertType['data']['id_usuario'] = $this->conexion->lastInsertId();
                }

            }
            #--- usuario insertado --------

            #-- insertar subtipo si se ha especificado o existe --------
            # --- si no se indica subtipo, solo se inserta datos en la tabla del super tipo: usuario
            if($usertType){
                $params_subT = array();
                foreach(array_keys($usertType['data']) as $params){
                    $params_subT [] = ":{$params}";
                }
                #valores a insertar
                $values_subT = array_values($usertType['data']);
                $sql_subtipo = "INSERT INTO {$tabla} (".implode(',', array_keys($usertType['data'])).") VALUES (".implode(',', $params_subT).")";
    
                $sentencia_subT = $this->conexion->prepare($sql_subtipo);
                for($i = 0; $i < count($values_subT); $i++){
                    $sentencia_subT->bindParam("$params_subT[$i]", $values_subT[$i]);
                }
                $sentencia_subT->execute();

            }

            $this->conexion->commit();

            $resultado['exito'] = true;
            $resultado['mensaje'] = 'Usuario registrado correctamente...';

            return $resultado;
        }catch(PDOException $error){
            $this->conexion->rollBack();
            $resultado['mensaje'] = 'Error: '. $error->getMessage();
            return $resultado;
        }

    } # --------------- INSERT ------------------------

    public function updateUsert($user = null, $usertType = null){
        $resultado = array('exito' => false, 'mensaje' => '');
        # supertipo
        if($user && !is_array($user)){
            $resultado['mensaje'] = 'Información de usuario no valida';
            return $resultado;
        }

        # subtipo del usuario
        if($usertType && !is_array($usertType)){
            $resultado['mensaje'] = 'El subtipo de usuario no esta en un array';
            return $resultado; 
        }

        #check que exista las keys correctas en la info del subtipo
        if($usertType && (!key_exists('tipo', $usertType) || !key_exists('data', $usertType) || !key_exists('id', $usertType) )){
            $resultado['mensaje'] = 'Revisa la informacion del subtipo de usuario...';
            return $resultado;
        }

        # el id de usuario es necesario
        if(!$user['id']){
            $resultado['mensaje'] = 'Es necesario un ID de usuario';
            return $resultado;  
        }
        if($user){
            # id de usuario
            $id_usuario = $user['id'];

            # si data > 0, hay data que actualizar
            if( count($user['data']) > 0 ){
                $info_usuario = $user['data']; # data del usuario
            }
        }
        #validar el subtipo elegido, si existe subtipo
        if($usertType){
            $subtipos_validos = ['fabricante', 'empleado', 'transportista', 'cliente'];
            $subtipo =  $usertType['tipo'];
            if(!in_array($subtipo, $subtipos_validos)){
                $resultado['mensaje'] = 'El subtipo elegido no es valido';
                return $resultado; 
            }
            # tabla del subtipo
            $tabla_subtipo = $usertType['tipo'];

            // tiene que haber id
            if(!$usertType['id']){
                $resultado['mensaje'] = 'Es necesario un ID de '.$usertType['tipo'];
                return $resultado;    
            }

            # pk del subtipo
            $id_subTipo = $usertType['id'];

            # si longitud de data es mayor que 0 es que hay data que actualizar dl subtipo
            if( count($usertType['data']) > 0){
                $info_subTipo = $usertType['data']; # data del subtipo

            }

        }

        try {

            
            $this->conexion->beginTransaction();

            if(isset($info_usuario)){
                #recuperar foto del usuario para eliminarla del servidor
                if(key_exists('foto', $info_usuario)){
                    $sql_foto = $this->conexion->prepare("SELECT foto FROM {$this->tabla} WHERE id_usuario = ? ");
                    $sql_foto->execute([$id_usuario]);
                    $foto = $sql_foto->fetchColumn();
                    $resultado['foto'] = $foto;
                }

                // generar params para cada columna de usuario
                $params_usuario = array();
                foreach(array_keys($info_usuario) as $clave){
                    $params_usuario [] = "$clave =:{$clave}";
                }
                // $values_usuario = array_values($info_usuario);
                $columnas = implode(',', $params_usuario);
                $info_usuario['id_usuario'] = $id_usuario;
                $sql_usuario = "UPDATE {$this->tabla} SET {$columnas} WHERE id_usuario =:id_usuario";
                    $sentencia_usuario= $this->conexion->prepare($sql_usuario);
                    $sentencia_usuario->execute($info_usuario);
            }
            

            if(isset($info_subTipo)){
                // generar params para cada columna de subtipo
                $params_subtipo = array();
                foreach(array_keys($info_subTipo) as $clave){
                    $params_subtipo [] = "$clave =:{$clave}";
                }
                // $values_usuario = array_values($info_subTipo);
                $columnas = implode(',', $params_subtipo);
                $info_subTipo['id_usuario'] = $id_usuario;

                $sql_subtipo = "UPDATE {$tabla_subtipo} SET {$columnas} WHERE id_usuario =:id_usuario";
                $sentencia_usuario= $this->conexion->prepare($sql_subtipo);
                $sentencia_usuario->execute($info_subTipo);  
            }

            $this->conexion->commit();
            $resultado['exito'] = true;
            $resultado['mensaje'] = 'Datos actualizados correctamente';

            return $resultado;


        } catch(PDOException $error){
            $this->conexion->rollBack();
            $resultado['mensaje'] = 'ERROR: '.$error->getMessage();
            return $resultado;
        }

    } #---- UPDATE--------------------

    public function insertBulkUser($users = null){
        $resultado = array('exito' => false, 'mensaje' => array());
        if(!$users || !is_array($users) || !count($users)){
            $resultado['mensaje'] = 'Se esperaba un array con registros..';
            return $resultado;
        }
        if(count($users[0]) !== 2){
            $resultado['mensaje'] = 'Revisa el array de usuarios, no esta formado bien';
            return $resultado;
        }
        try{
            # iniciar transaccion
            $this->conexion->beginTransaction();
            $user = ''; $subtipo = '';
            for($i = 0; $i < count($users); $i++){
                $user = $users[$i][0]; # ahi se guarda la info del usuario
                $subtipo = $users[$i][1]; # info subtipo

                // generar params para cada columna de usuario
                $params_usuario = array();
                foreach(array_keys($user) as $param){
                    $params_usuario [] = ":{$param}";
                }
                #valores a insertar
                $values_usuario = array_values($user);
                $sql = "INSERT INTO {$this->tabla} (".implode(',', array_keys($user)).") VALUES (".implode(',', $params_usuario).")";
                $sentencia = $this->conexion->prepare($sql);
                
                #bind todos los valores
                for($k = 0; $k < count($values_usuario); $k++){
                    $sentencia->bindParam("$params_usuario[$k]", $values_usuario[$k]);
                }
                $sentencia->execute();
                #recuperar id de usuario insertado
                if($sentencia->rowCount() > 0){
                    $subtipo['data']['id_usuario'] = $this->conexion->lastInsertId();
                }

                #------ insertar en la tabla del subtipo --------------
                $params_subT = array();
                foreach(array_keys($subtipo['data']) as $params){
                    $params_subT [] = ":{$params}";
                }
                #valores a insertar
                $values_subT = array_values($subtipo['data']);
                $tabla = $subtipo['tipo'];

                $sql_subtipo = "INSERT INTO {$tabla} (".implode(',', array_keys($subtipo['data'])).") VALUES (".implode(',', $params_subT).")";
    
                $sentencia_subT = $this->conexion->prepare($sql_subtipo);
                for($j = 0; $j < count($values_subT); $j++){
                    $sentencia_subT->bindParam("$params_subT[$j]", $values_subT[$j]);
                }

                $sentencia_subT->execute();


            }
            # guardar cambios
            $this->conexion->commit();
            $resultado['exito'] = true;
            $resultado['mensaje'] = "Registros importados correctamente, total: ".count($users);

            return $resultado;
        }catch(PDOException $error){
            $this->conexion->rollBack();
            $resultado['mensaje'] = 'Error: '. $error->getMessage();
            return $resultado;
        }

    }

    public function deleteUser($user = null, $userType = null){
        $resultado = array('exito' => false, 'mensaje' => '');
        
        # usuario y subtipo son requeridos
        if(!$user || !$userType){
            $resultado['mensaje'] = 'La informacion del usuario no es valida';
            return $resultado; 
        }

        #la info del subtipo tiene que venir en un array
        if(!is_array($userType)){
            $resultado['mensaje'] = 'La información de subtipo tiene que estar en un array';
            return $resultado;
        }

        #check que exista las keys correctas en la info del subtipo
        if(!key_exists('tipo', $userType) || !key_exists('data', $userType)){
            $resultado['mensaje'] = 'Revisa la informacion del subtipo de usuario...';
            return $resultado;
        }

        #validar el subtipo elegido, si existe subtipo
        $subtipos_validos = ['fabricante', 'empleado', 'transportista', 'cliente'];
        $subtipo = $tabla_subtipo = $userType['tipo'];
        if(!in_array($subtipo, $subtipos_validos)){
            $resultado['mensaje'] = 'El subtipo elegido no es valido';
            return $resultado; 
        }

        # -- ok
        try {
            $this->conexion->beginTransaction();
            $id_usuario = $user;
            $pk_subtipo = implode(',', array_keys($userType['data']));
            $id_subTipo = implode(',', array_values($userType['data']));
            #eliminar en tabla supertipo
            $sql = "DELETE FROM {$this->tabla} WHERE id_usuario = ?";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->execute([$id_usuario]);

            #eliminar en subtipo
            $query = "DELETE FROM {$tabla_subtipo} WHERE {$pk_subtipo} = ? ";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([$id_subTipo]);
            
            #guardar cambios
            $this->conexion->commit();
            $resultado['exito'] = true;
            $resultado['mensaje'] = ucfirst($subtipo).' eliminado correctamente..';
            return $resultado;

        }catch(PDOException $error){
            $this->conexion->rollBack(); # deshacer los cambios hechos
            $resultado['mensaje'] = 'ERROR: '.$error->getMessage();
            return $resultado; 
        }


    }

    // traer usuarios transportistas
    public function getTransportistas($id = null){
        $resultado = array('exito'=>false, 'data' => array());
        try{
            $sql = "SELECT transportista.id_trans, usuario.id_usuario, usuario.foto, usuario.nombre, usuario.email,transportista.telefono, transportista.fax, transportista.provincia as id_provincia,roles.nombre as roll, usuario.id_roll,provincia.nombre as provincia, provincia.id_provincia, ccaa.nombre as ccaa, count(pedido.transporte) as pedidos, usuario.fecha_registro from usuario left join roles on usuario.id_roll = roles.id_roll inner join transportista on usuario.id_usuario = transportista.id_usuario left join provincia on transportista.provincia = provincia.id_provincia left join ccaa on provincia.id_ca = ccaa.id_ca left join pedido on transportista.id_trans = pedido.transporte";
            if($id) $sql .= " WHERE transportista.id_trans = ?";
            $sql .=" GROUP BY transportista.id_trans ORDER BY usuario.fecha_registro DESC, usuario.id_usuario DESC ";
            $sentencia = $this->conexion->prepare($sql);
            ($id) ? $sentencia->execute([$id]) :  $sentencia->execute();
           
            if($sentencia->rowCount() > 0){
                $resultado['exito'] = true;
                $resultado['data'] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
                return $resultado;
            }else{
                $resultado['data'] = ' No se ha encotrado información para este usuario';
                return $resultado;
            }

        }catch(PDOException $error){
            $resultado['data'] ='Error: '.$error->getMessage();
            return $resultado;
        }
    }


    // traer usuarios transportistas
    public function getClientes($id = 0){
        $resultado = array('exito'=>false, 'data' => array());
        try{

            $sql = "SELECT cliente.id_cliente, usuario.id_usuario, usuario.foto, usuario.nombre, usuario.email, cliente.provincia as id_provincia, cliente.tlfno as telefono, cliente.f_nac as fechaNacimiento, FLOOR(DATEDIFF(CURDATE(),cliente.f_nac)/365) AS edad, roles.nombre as roll, usuario.id_roll,LOWER(provincia.nombre) as provincia, provincia.id_provincia, LOWER(ccaa.nombre) as ccaa, count(pedido.id_cliente) as pedidos, usuario.fecha_registro from usuario LEFT JOIN roles on usuario.id_roll = roles.id_roll inner join cliente on usuario.id_usuario = cliente.id_usuario LEFT JOIN provincia on cliente.provincia = provincia.id_provincia LEFT JOIN ccaa on provincia.id_ca = ccaa.id_ca LEFT JOIN pedido on cliente.id_cliente = pedido.id_cliente";
            if($id) $sql .= " WHERE cliente.id_cliente = ?";
            $sql .=" GROUP BY cliente.id_cliente ORDER BY usuario.fecha_registro DESC, usuario.id_usuario DESC";
            $sentencia = $this->conexion->prepare($sql);
            ($id) ? $sentencia->execute([$id]) :  $sentencia->execute();
            
            if($sentencia->rowCount() > 0){
                $resultado['exito'] = true;
                $resultado['data'] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
                return $resultado;
            }else{
                $resultado['data'] = ' No se ha encotrado información para este usuario';
                return $resultado;
            }

        }catch(PDOException $error){
            $resultado['data'] ='Error: '.$error->getMessage();
            return $resultado;
        }
    }

    // usuarios empleados
    public function getEmpleados($id = null){
        $resultado = array('exito'=>false, 'data' => array());
        try{
            $sql = "SELECT empleados.id_usuario, empleados.email, empleados.nombre, empleados.id_roll, empleados.fecha_registro,roles.nombre as roll, empleado.cod_vendedor, calle, fecha_nacimiento, FLOOR(DATEDIFF(CURDATE(),fecha_nacimiento)/365) AS edad, empleado.id_turno, pais as id_pais, categoria as id_categoria, id_jefe,jefes.nombre as jefe, pais.cod_pais, pais.nombre as pais, categoria.nombre, turno.nombre as turno from empleado 
            inner join usuario empleados on empleado.id_usuario = empleados.id_usuario 
            left join turno on empleado.id_turno = turno.id_turno 
            left join pais on empleado.pais = pais.id_pais 
            inner join categoria on empleado.categoria = categoria.id_cat 
            left join usuario jefes on empleado.id_jefe = jefes.id_usuario
            left join roles on empleados.id_roll = roles.id_roll";

        }catch(PDOException $error){

        }
    }


  
}


?>