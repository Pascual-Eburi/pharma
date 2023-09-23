<?php
# require_once('Database.php');
class EmployesModel extends Database {
    //nombre de la tabla
    protected $nombre_tabla = 'vendedor';

    //crear - create
    public function create($codigo) {
        # code...
        if ($codigo){
            
            $sql = 'CALL datosLoginVendedor(:codigo,@email,@clave)';
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(':codigo', $codigo, PDO::PARAM_INT, 3);
            $sentencia->execute();
            if($sentencia->rowCount() > 0 ){
                $resultado = $this->conexion->query("SELECT @email,@clave")->fetch(PDO::FETCH_ASSOC);
            }else {
                $resultado = [];
            }
            return $resultado;
        }
    }

    // verificar datos login del vendedor
    public function getLoginVendedor($email, $clave){
        if( $email && $clave ){
            try {
                #$sql = 'CALL verificarLoginVendedor(:emailUsuario,:claveUsuario, @email,@clave)';
                $sql = 'CALL verificarLoginVendedor(:email,@codigo,@email,@clave)';
                $sentencia = $this->conexion->prepare($sql);
                #$sentencia->bindParam(1, $valor, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 4000); 
                $sentencia->bindParam(':email', $email, PDO::PARAM_STR, 90);
                $sentencia->execute();

                if( $sentencia->rowCount() > 0 ){
                    $resultado = $this->conexion->query("SELECT @codigo as codigo,@email as email,@clave as clave")->fetch(PDO::FETCH_ASSOC);
                } else {
                    $resultado = [];
                }
            } catch (PDOException $error) { #si ocure algun error
                //lanzar el error
                $resultado = [];
                echo "Error: ".$error->getMessage();
               

            }


        }else {
            $resultado = [];
        }

        return $resultado;
    }

    //leer - read
    public function get($cod_vendedor ='', $campos = []) {
        # si numero de campos < 1 ==> * ; campos -> campo
        $sql = 'SELECT ';  
        if (count($campos) > 0 ){

            for ($i = 0; $i < count($campos); $i++ ){
                if ($i == count($campos) - 1 ){
                    $sql .="$campos[$i] ";
                }else{
                    $sql .="$campos[$i] ,";  
                } 
            }
        }else{
            $sql .=' * ';
        }

        $sql .=" FROM {$this->nombre_tabla}";
        if (!(empty($cod_vendedor))){
            $sql .= " WHERE cod_vendedor = $cod_vendedor";
        }

        $sql .= ';';

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }else {
            $resultados= [];
        }

        return $resultados;
    }

    //actualizar - update
    public function update($codigo){

        if ($codigo >= 0 ){
            $sql = 'CALL infoVendedores(:codigo)';
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(':codigo', $codigo, PDO::PARAM_STR, 4);
            $sentencia->execute();
            if($sentencia->rowCount() > 0 ){
                do {
                    $registros[] = $sentencia->fetch(PDO::FETCH_ASSOC);
                } while ( $sentencia->nextRowset() && $sentencia->columnCount() );
            }else {
                $registros = [];
            }   
        }else{ $registros = []; }

        return $registros;
    }

    //eliminar - delete
    public function delete($cod_vendedor = ''){
        # code...
        echo 'todo';
    }
}




?>