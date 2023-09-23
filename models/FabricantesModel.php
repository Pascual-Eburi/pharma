
<?php

class FabricantesModel extends UsuariosModel{
    // nombre de la tabla
    protected $nombre_tabla = 'fabricante';
    public function getFabricantes($id = 0){
        $resultado = array('exito' => false, 'data' => array());
        if( $id < 0 || !is_numeric($id)){
            $resultado['data'] = 'Se esperaba un id numerico mayor que 0';

            return $resultado;
        }

        try{
            $sql = "CALL infoFabricantes(:id)";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(':id', $id, PDO::PARAM_STR, 3);
            $sentencia->execute();

            if($sentencia->rowCount() <= 0){
                $resultado['data'] = 'No hay registros de fabricantes';
                return $resultado;
            }

            // todo ok, recorrerer todos los registros
            do{
                $resultado['data'][] = $sentencia->fetch(PDO::FETCH_ASSOC);
            } while( 
                $sentencia->nextRowset() && $sentencia->columnCount()
            );

            $resultado['exito'] = true;
            return $resultado;
            
        }catch(PDOException $error){
            $resultado['data'] = 'ERROR: '.$error->getMessage();
            return $resultado;
        }
    }

    //eliminar multiples registros
    public function eliminarMultiple($registros = null){
        # code...
        $resultado = array('exito' =>false, 'mensaje'=> '');
        if(!is_array($registros) || !count($registros)  ){
            $resultado['mensaje'] = 'Es necesario un array con elementos';
            return $resultado;
        }
        try{
                # iniciar transaccion
            $this->conexion->beginTransaction();

            #select id_usuario del fabricante - tabla fabricante
            $query_usuario = $this->conexion->prepare("SELECT id_usuario FROM {$this->nombre_tabla} WHERE id_fabricante = ? ");

            #foto del usuario - tabla usuario
            $sql_foto = $this->conexion->prepare("SELECT foto FROM {$this->tabla} WHERE id_usuario = ?");

            # consulta eliminar fabricante
            $sql = "DELETE FROM {$this->nombre_tabla} WHERE id_fabricante = ?";
            $sql_del_usuario = "DELETE FROM {$this->tabla} WHERE id_usuario = ?";
            $stmt_user = $this->conexion->prepare($sql_del_usuario);
            $sentencia = $this->conexion->prepare($sql);
            # recorrer para cada registro
            foreach($registros as $registro){
                # ejectutar select id_usuario
                $query_usuario->execute([$registro]);
                $id_usuario = $query_usuario->fetchColumn();
                
                #ejecutar select foto
                
                $sql_foto->execute([$id_usuario]);
                $foto = $sql_foto->fetchColumn();
                if($foto){
                    $resultado['fotos'][] = $foto;
                }

                # eliminar usuario
                if($id_usuario) $stmt_user->execute([$id_usuario]);

                #Eliminar fabricante
                $sentencia->execute([$registro]);
            }
            $this->conexion->commit();
            $resultado['exito'] = true;
            return $resultado;

        } catch (PDOException $error){
            $this->conexion->rollBack();
            $error_integridad = 'Integrity constraint violation: 1451 Cannot delete or update a parent row';
            if (strpos($error->getMessage(), $error_integridad) == TRUE) {
                $resultado['mensaje'] = 'No se puede eliminar algunos registro por intengridad referencial.';
            } else {
                $resultado['mensaje'] = $error->getMessage();
            }

            return $resultado;

        }


        
    }
}

?>