<?php
class Database{
    private $hostname = "localhost";
    private $database = "tienda_online";
    private $username = "root";
    private $password = ""; // Coloca la contraseña correcta aquí
    private $charset = "utf8";
    public $con; // Agrega la propiedad $con

    function conectar(){
        try{
            $conexion = "mysql:host=" . $this->hostname . "; dbname=" . $this->database . "; charset=". $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            $this->con = new PDO($conexion, $this->username, $this->password, $options); // Asigna el valor a $this->con

            return $this->con;
        }catch(PDOException $e){
            echo 'Error conexión: ' . $e->getMessage();
            exit;
        }
    }
}
?>
