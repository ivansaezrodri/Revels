<?php 
    # Especificamos la la sesión
    ini_set('session.name','SESSIONREVEL');
    session_start();
    # Desvinculamos el usuario y la sesión 
    unset($_SESSION['usuario']);
    unset($_SESSION['id']);
    unset($_SESSION['email']);
    # Destruimos la sesión
    session_destroy();
    header('Location: registro.php');

?>