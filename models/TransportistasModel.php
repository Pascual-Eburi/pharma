
<?php

class TransportistasModel extends UsuariosModel{
    // nombre de la tabla
    protected $nombre_tabla = 'transportista';


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
            $query_usuario = $this->conexion->prepare("SELECT id_usuario FROM {$this->nombre_tabla} WHERE id_trans = ? ");

            #foto del usuario - tabla usuario
            $sql_foto = $this->conexion->prepare("SELECT foto FROM {$this->tabla} WHERE id_usuario = ?");

            # consulta eliminar fabricante
            $sql = "DELETE FROM {$this->nombre_tabla} WHERE id_trans = ?";
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

                #Eliminar subtipo
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