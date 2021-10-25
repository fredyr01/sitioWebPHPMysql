<?php

    $host = "localhost";
    $bd = "sitiophpmysql";
    $user = "root";
    $password = "";

    try {
        $conexion = new PDO("mysql:host=$host;dbname=$bd",$user,$password);
        // if($conexion){
        //     echo "Conectado al sistema";
        // }
    } catch (Exception $e) {
        echo $e->getMessage();
    }

?>