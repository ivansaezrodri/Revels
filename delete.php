<?php
    ##Especificamos que la sesión se llame SESSIONREVEL
    ini_set('session.name','SESSIONREVEL');
    session_start();

    # Si se trata de un usuario que no está logueado se le redirecciona a registro.php
    if (!isset($_SESSION['usuario'])) {
        header('Location: login.php');
    } 


    # Hacemos un ligero regex y eliminamos la publicacion según el iddelrevel y nos aseguramos de que el revel le pertenezca
    if (!empty($_GET['eliminarRevel'])) {

        if (preg_match('/^[A-z\s0-9]{1,999}$/',$_GET['eliminarRevel'])) {
            # Buscamos el id del revel y a quien pertenece
            require('conexionBBDD.inc.php');
            $resultado = $conexion->query('SELECT id,userid FROM revels WHERE id='.$_GET['eliminarRevel'].';');
            unset($conexion);

            foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
                    $revel['id'] = $usuario['id'];
                    $revel['userid'] = $usuario['userid'];
            }  

            # Si el revel pertenece al usuario que realiza la petición se elimina
            if ($revel['userid'] == $_SESSION['id']) {
                
                require('conexionBBDD.inc.php');
                # Para no cambiar y reescribir las claves con el ON DELETE CASCADE cada vez (ya que cambiarlo en mi BBDD una única 
                # vez no va a hacer que tu lo tengas cuando lo revises) lo desactivo y lo activo posteriormente. Se que esto no se
                # ha de hacer pero lo hago para que lo evalues
                $registros = $conexion->exec('SET FOREIGN_KEY_CHECKS = 0;');

                $registros = $conexion->exec('DELETE FROM comments WHERE revelid='.$_GET['eliminarRevel'].';');
                $registros = $conexion->exec('DELETE FROM revels WHERE id='.$_GET['eliminarRevel'].' AND userid='.$_SESSION['id'].';');
                
                $registros = $conexion->exec('SET FOREIGN_KEY_CHECKS = 1;');
                unset($conexion);
                header('Location: list.php');
            }
        } else {
            header('Location: list.php');
        }
    }
    
    
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Revels</title>
    <style>
        body {
            height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            background-color: black;
            flex-direction:column;
        }
        span {
            color:white;
            font-size:3em;
            margin: -1.5em -.5em 0 0;
        }
        .contenedor {
            margin-top:-10em;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-direction:column;
        }
        
    </style>
</head>
<body class="fondo-3">
    <div class="contenedor">
    <svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="none" stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v4a1 1 0 0 0 1 1h3m0-5v10m3-9v8a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1zm7-1v4a1 1 0 0 0 1 1h3m0-5v10"/></svg>
    <span>¿Que buscabas Alex?</span>
    </div>
    
</body>
</html>