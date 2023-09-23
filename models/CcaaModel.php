<?php

class CcaaModel extends Database {
    //nombre de la tabla
    protected $nombre_tabla = 'ccaa';
    
    // leer
    public function read($id = 0){
        if ( $id >= 0 ){
            $sql = 'CALL infoComunidadesAutonomas(:id)'; # call procedure
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(':id',$id, PDO::PARAM_STR, 4);
            $sentencia->execute();

            if( $sentencia->rowCount() > 0 ){
                do{
                    $ccaa[] = $sentencia->fetch(PDO::FETCH_ASSOC);

                } while( $sentencia->nextRowset() && $sentencia->columnCount() );

            }else{
                $ccaa = []; // no hay resultados
            }

        }else {
            $ccaa = []; // se ha recibido un id < 0
        }

        return $ccaa;
    }

    // crear - registrar ccaa
    public function registrar($nombre){
        $id_asignado = null;
        if($nombre){
            $sql = 'CALL insertarCCAA(:nombre, @id_asignado)';
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR, 20);
            $sentencia->execute();

            if ($sentencia->rowCount() > 0 ){
                $id_asignado = $this->conexion->query("SELECT @id_asignado as id")->fetch(PDO::FETCH_ASSOC);
            }
        }

        return $id_asignado;
    }

    /**
     * actializar informacion
     */
    public function actualizar($id, $nombre){
        $mensaje = null;
        if($id && $nombre) {
            $sql = 'CALL actualizarCCAA(:id, :nombre, @mensaje)';
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT, 2);
            $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR, 20);
            $sentencia->execute();

            if($sentencia->rowCount() > 0 ){
                $mensaje = $this->conexion->query("SELECT @mensaje as mensaje")->fetch(PDO::FETCH_ASSOC);
            }
        }

        return $mensaje;
    }

    // eliminar registros
    public function eliminar($id){
        $resultado = array();
        if ( $id > 0 ){
            try {
                $sql = 'CALL eliminarCCAA(:id, @mensaje)';
                $sentencia = $this->conexion->prepare($sql);
                $sentencia->bindParam(':id', $id, PDO::PARAM_STR, 3);
                $sentencia->execute();
                if ($sentencia->rowCount() > 0 ){
                    $resultado = $this->conexion->query("SELECT @mensaje as mensaje")->fetch(PDO::FETCH_ASSOC);
                }else {
                    $resultado = null;
                }  
            } catch (PDOException $error) {
                //$existingkey = "Integrity constraint violation: 1062 Duplicate entry";
                $error_integridad = 'Integrity constraint violation: 1451 Cannot delete or update a parent row';
                if (strpos($error->getMessage(), $error_integridad) == TRUE) {
                    $resultado[] = 'No se puede eliminar este registro por intengridad referencial.';
                } else {
                    $resultado[] = $error->getMessage();
                }
                
            }


        }else {
            $resultado = null;
        }

        return $resultado;
    }
    
    /**
     *  Insercion multiple de registros importados desde un archivo
     * @param {Array} : array de datos a insertar
     */
    public function insertarMultiple($data){
        $resultado = array('exito' => false, 'mensaje' => '');
        if($data && is_array($data) && count($data) > 0){
            if(key_exists('encabezados', $data) && key_exists('registros', $data) ){
                if(is_array($data['encabezados']) && is_array($data['registros']) ){
                    $num_encabezados = count($data['encabezados']); $num_registros = count($data['registros'][0]);
                    if( $num_encabezados > 0 && $num_registros > 0 && $num_encabezados == $num_registros ){
                        try {
                            # columnas de la tabla donde insertar   #añadir id_ca a las columnas
                            $columnas = $data['encabezados'];       array_unshift($columnas, 'id_ca');
                            # valores a insertar                    #para la sentencia preparada
                            $valores = $data['registros'];          $params = [];
                            // llenar params => :id_ca, :nombre....
                            foreach ($columnas as $columna){$params[] = ":{$columna}";}
                            $this->conexion->beginTransaction(); # iniciar transacion
                            $sql = "INSERT INTO {$this->nombre_tabla} (". implode(',', $columnas).") VALUES (".implode(',', $params).")";
    
                            $sentencia = $this->conexion->prepare($sql);
                            $id_asignados = [];
                            for ($i=0; $i < count($valores); $i++) {
                                // valores a registrar
                                $datos = $valores[$i];
                                // generar id para cada registro
                                $sql_id = "CALL generarIdCCAA(@id)";
                                $sentencia_id = $this->conexion->prepare($sql_id);
                                $sentencia_id->execute();
                                if ($sentencia_id->rowCount() > 0){
                                    $id = $this->conexion->query("SELECT @id as id")->fetch(PDO::FETCH_ASSOC)['id'];
                                }
                                // añadir id al array de datos
                                array_unshift($datos, $id);
                                for($j = 0; $j < count($datos); $j++){
                                    $sentencia-> bindParam("$params[$j]", $datos[$j]);
                                }
                                // añadir id_asignado al array de id asignados
                                $id_asignados[] = $id;
                                
                                // ejecutar insersion
                                $sentencia->execute();
                                
                            }
    
                            $this->conexion->commit();
                            $resultado['exito'] = true;
                            $resultado['mensaje'] = "Se ha importado un total de ".count($id_asignados)." registros correctamenta"; 
                            return $resultado;
    
                            //code...
                        } catch (PDOException $error) {   
                            $this->conexion->rollback();
                            $resultado['mensaje'] = 'Error: '. $error->getMessage();
                            return $resultado;
                        } # fin try catch 
                    }else{
                        $resultado['mensaje'] ='Revisa los enbezados y los registros, no coinciden..';
                        return $resultado;
                    } # if else num_encabezados ... num_registros 

                }else{
                    $resultado['mensaje'] ='Se esperaba un array de encabezados y otro de registros..';
                    return $resultado;
                } # if elsa is array encabezados and registros

            } else {
                $resultado['mensaje'] ='El arreglo de datos no tiene el formato esperado..';
                return $resultado;
            } # if else key exists 

        }else{
            if(!$data){
                $resultado['mensaje'] ='Asegurate de mandar datos para ser insertados';
                return $resultado;
            }
            if( !is_array($data) ){
                $resultado['mensaje'] ='Se esperaba un arreglo de datos';
                return $resultado;
            }
            if( count($data) <= 0 ){
                $resultado['mensaje'] ='El arreglo de datos no puede estar vacio...';
                return $resultado;
            }
        } # if else $data is array
    }


    //eliminar multiples registros
    public function eliminarMultiple($registros = null){
        # code...
        $resultado = array('exito' =>false, 'error'=> '');
        if(is_array($registros) && count($registros) > 0 ){
            try{
                 # iniciar transaccion
                $this->conexion->beginTransaction();
                $sql = "DELETE FROM {$this->nombre_tabla} WHERE id_ca = ?";
                $sentencia = $this->conexion->prepare($sql);
                foreach($registros as $registro){
                    $sentencia->execute([$registro]);
                }
                $this->conexion->commit();
                $resultado['exito']= true;

            } catch (PDOException $error){
                $resultado['exito']= false;
                $resultado['error'] = $error->getMessage();
            }

        } else{
                $resultado['error'] = 'Es necesario un array con elementos';
        }
        return $resultado;
    }

   # $data = [31,30,29,28,27,26,25,24,23,22,21]
}

?>