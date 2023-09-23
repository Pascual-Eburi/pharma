<?php

class PaisesModel extends Database {
    //nombre de la tabla
    protected $nombre_tabla = 'pais';
    
    // leer
    public function read($pais){
        if ( $pais >= 0 ){
            $sql = 'CALL infoPaises(:pais)'; # call procedure
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(':pais',$pais, PDO::PARAM_STR, 4);
            $sentencia->execute();

            if( $sentencia->rowCount() > 0 ){
                do{
                    $paises[] = $sentencia->fetch(PDO::FETCH_ASSOC);

                } while( $sentencia->nextRowset() && $sentencia->columnCount() );


            }else{
                $paises = []; // no hay resultados
            }

        }else {
            $paises = []; // se ha recibido un id < 0
        }

        return $paises;
    }
    // crear
    public function registrar($nombre, $codigo, $abreviatura){
        $id_asignado = null;
        if($nombre){
            $sql = 'CALL insertarNuevoPais(:codigo,:abreviatura,:nombre, @id_asignado)';
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(':codigo', $codigo, PDO::PARAM_STR, 2);
            $sentencia->bindParam(':abreviatura', $abreviatura, PDO::PARAM_STR, 3);
            $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR, 30);
            $sentencia->execute();

            if ($sentencia->rowCount() > 0 ){
                $id_asignado = $this->conexion->query("SELECT @id_asignado as id")->fetch(PDO::FETCH_ASSOC);
            }
        }

        return $id_asignado;
    }

    // actualizar
    public function actualizar($id, $nombre, $codigo, $abreviatura){
        $mensaje = null;
        if($nombre) {
            $sql = 'CALL actualizarInfoPais(:id, :codigo, :abreviatura, :nombre, @mensaje)';
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT, 3);
            $sentencia->bindParam(':codigo', $codigo, PDO::PARAM_STR, 2);
            $sentencia->bindParam(':abreviatura', $abreviatura, PDO::PARAM_STR, 3);
            $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR, 30);
            $sentencia->execute();

            if($sentencia->rowCount() > 0 ){
                $mensaje = $this->conexion->query("SELECT @mensaje as mensaje")->fetch(PDO::FETCH_ASSOC);
            }
        }

        return $mensaje;
    }

    // eliminar
    public function eliminar($pais){
        $resultado = array();
        if ( $pais > 0 ){
            try {
                $sql = 'CALL eliminarPais(:pais, @mensaje)';
                $sentencia = $this->conexion->prepare($sql);
                $sentencia->bindParam(':pais', $pais, PDO::PARAM_STR, 3);
                $sentencia->execute();
    
                if ($sentencia->rowCount() > 0 ){
                    $resultado = $this->conexion->query("SELECT @mensaje as mensaje")->fetch(PDO::FETCH_ASSOC);
                }else {
                    $resultado = null;
                } 
            } catch (PDOException $error) {
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
}

?>