<?php

#Base de datos central, aqui se inicia la conexion con la Base de Datos,
#Tambien estan las autorizaciones para poder establecer conexion con la Base de Datos.

class Database {

    #Identificacion de canal de conexion.

    private $hostname = "127.0.0.1";

    #Nombre de la Base de Datos.

    private $database = "scagm";

    #Autorizacion Usuario.

    private $username = "sistemas";

    #Autorizacion Clave de Acceso de Usuario.

    private $password = "Sistemas";

    #Tipo de Compatibilidad -Y Cifrado, Creo-.

    private $charset = "utf8";

    #Establecion conexion.

    function conectar()
    {
        try{
        $conexion = "mysql:host=" . $this->hostname . "; dbname=" . $this->database . ";
        charset=" . $this->charset;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        $pdo = new PDO($conexion, $this->username, $this->password, $options);

        return $pdo;
    #Si en tal caso, no se estable una conexion, entonces, Muestra mensaje de Error.
    
    } catch (PDOException $e){
    echo 'Error conexion:' . $e->getMessage();
    exit();
    }
    }
}

?>