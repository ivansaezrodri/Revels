<?php
    ## Especificamos que la sesión se llame SESSIONREVEL
    ini_set('session.name','SESSIONREVEL');
    session_start();

    # Si se trata de un usuario que no está logueado se le redirecciona a registro.php
    if (!isset($_SESSION['usuario'])) {
        header('Location: registro.php?u='.$_POST['usuario'].'&e='.$_POST['email']);
    } 


    # Si viene un nuevo registro se mira si existe y si no es así se registra
    if (!empty($_POST)) {

        # Se eliminan los espacios en blanco
        foreach ($_POST as $key => $value) {
            $key = trim($value);
        }
        
        # Pasamos todos las regex y si encuentra algún fallo el flag $fallo pasa a true para que no se envie el formulario y si se almacena el fallo en $_SESSION[campo]
        if (!preg_match('/^[A-z\s0-9]{3,15}/',$_POST['usuario']) || $_POST['usuario'] == "") {
            $_SESSION['fallo'][] = "El campo Usuario no puede estar vacío y ha de contener como mínimo 3 caracteres y maximo 15.";
            $fallo = true;
        } 
    
        # Pasamos todos las regex y si encuentra algún fallo el flag $fallo pasa a true para que no se envie el formulario y si se almacena el fallo en $_SESSION[campo]
        if (!preg_match('/^[A-z0-9\s]{6,20}$/',$_POST['contrasenya']) || $_POST['contrasenya'] == "") {
            $_SESSION['fallo'][] = "El campo Contraseña no puede estar vacío y ha de contener como mínimo 6 caracteres y máximo 20.";
            $fallo = true;
        } 
    
        # Pasamos todos las regex y si encuentra algún fallo el flag $fallo pasa a true para que no se envie el formulario y si se almacena el fallo en $_SESSION[campo]
        if (!preg_match('/^.+@.+\..+$/',$_POST['email']) || $_POST['email'] == "") {
            $_SESSION['fallo'][] = "El campo Correo no puede estar vacío y ha de estar correctamente formado.";
            $fallo = true;
        } 





        # Listamos los usuarios
        require('conexionBBDD.inc.php');
        $resultado = $conexion->query('SELECT * FROM users;');
        unset($conexion);
        # Si ya existe se marca $fallo a true y se redireccionará de nuevo al registro
        foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
                if ($usuario['usuario'] == $_POST['usuario']) {
                    $_SESSION['fallo'][] = "Usuario o correo ya registrado.";
                    $fallo = true;
                } else if($usuario['email'] == $_POST['email']) {
                    $_SESSION['fallo'][] = "Usuario o correo ya registrado.";
                    $fallo = true;
                } 
        }

        # Si no existe el usuario en la BBDD se registra
        if (!isset($fallo)) {
            # Se hashea la contraseña y se inserta
            $contrasenyaEncriptada = password_hash($_POST['contrasenya'], PASSWORD_DEFAULT);
            require('conexionBBDD.inc.php');        
            $consulta = $conexion->prepare('INSERT INTO users (usuario,email,contrasenya) VALUES (?,?,?);');
            
            $consulta->bindParam(1, $_POST['usuario']);
            $consulta->bindParam(2, $_POST['email']);
            $consulta->bindParam(3, $contrasenyaEncriptada);
            
    
            $consulta->execute();
            unset($conexion);

            $_SESSION['usuario'] = $_POST['usuario'];
            $_SESSION['email'] = $_POST['email'];
            header('Location: results.php?busqueda=%');
            exit();
        } else {
            header('Location: registro.php?u='.$_POST['usuario'].'&e='.$_POST['email']);
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