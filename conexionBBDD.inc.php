<?php   
    # Creamos la conexión a la base de datos indicada con el usuario y pass indicado
    $dsn ='mysql:host=localhost:3306;dbname=revels';
    $opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
    
    try {
        $conexion = new PDO($dsn, 'revel', 'lever', $opciones);
    } catch (PDOException $e) {
        echo 'Fallo durante la conexión: ' . $e->getMessage();
    }

?>