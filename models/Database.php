<?php
/**
 *  conexion a la base de datos
 */

class Database{
    #====== atributos
    private $servidor = 'localhost';
    private static $usuario = 'root';
    private static $password = '';
    private $nombreBD = 'catalogofarmacia';
    protected $conexion;

    #=========== metodos
    # constructor
    public function __construct(){
        try {
            // conexion a la base de datos
            $dsn = "mysql:host={$this->servidor};dbname={$this->nombreBD}; charset=utf8";
            $options = array(PDO::ATTR_PERSISTENT); #hacer la conexion persitente para mejorar el rendimiento de la app
            $opciones = array(
                PDO::ATTR_PERSISTENT => TRUE,
                PDO::ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', //after php5.3.6
            );
            $this->conexion= new PDO($dsn, self::$usuario, self::$password, $opciones);


        } catch (PDOException $error) { #si ocure algun error
            //lanzar el error
            echo "Error en la conexión: ".$error->getMessage();

        }
    }

    #obtener nombre db
    public function getDBName(){return $this->nombreBD; }
}

?>
