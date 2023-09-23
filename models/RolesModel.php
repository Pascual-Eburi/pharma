<?php

class RolesModel extends Database{
    protected $nombre_tabla = 'roles';

    public function getRoles($id = 0){
        $resultado = array('exito' => false, 'data' => array());
        if( $id < 0 || !is_numeric($id)){
            $resultado['data'] = 'Se esperaba un id numerico mayor que 0';
            return $resultado;
        }

        try{
            $sql = "CALL infoRoles(:id)";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT, 11);
            $sentencia->execute();

            if($sentencia->rowCount() <= 0){
                $resultado['data'] = 'No hay registros de roles';
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
}

?>