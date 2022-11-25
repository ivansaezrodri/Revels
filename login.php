<?php

    ## Especificamos que la sesión se llame SESSIONREVEL
    ini_set('session.name','SESSIONREVEL');
    session_start();
    

    # Si el usuario ya está logueado se le enviará a index.php 
    if (isset($_SESSION['usuario'])) {
        header('Location: index.php');
    } 

    # Si revisa que se reciben parametros por post
    if (!empty($_POST)) {
        $registrado = false;

        # Quitamos los espacios
        foreach ($_POST as $key => $value) {
            $key = trim($value);
        }

        # Pasamos todos las regex y si encuentra algún fallo el flag $fallo pasa a true para que no se envie el formulario y si se almacena el fallo en $_SESSION['fallo']
        if (!preg_match('/^[A-z\s0-9]{3,20}/',$_POST['usuarioOMail']) || $_POST['usuarioOMail'] == "") {
            $_SESSION['fallo'][] = "El campo Usuario no puede estar vacío y ha de contener como mínimo 3 caracteres y maximo 20.";
            $fallo = true;
        } 
    
        # Pasamos todos las regex y si encuentra algún fallo el flag $fallo pasa a true para que no se envie el formulario y si se almacena el fallo en $_SESSION['fallo']
        if (!preg_match('/^[A-z0-9\s]{6,20}$/',$_POST['contrasenya']) || $_POST['contrasenya'] == "") {
            $_SESSION['fallo'][] = "El campo Contraseña no puede estar vacío y ha de contener como mínimo 6 caracteres y máximo 20.";
            $fallo = true;
        } 
    


        # Si no hay fallo se revisa en la BBDD
        if (!isset($fallo)) {
            # Revisamos si el usuario/mail y contraseña concuerdan
            require_once('conexionBBDD.inc.php');
            $resultado = $conexion->query('SELECT * FROM users WHERE usuario="'.$_POST['usuarioOMail'].'" OR email="'.$_POST['usuarioOMail'].'";');
            unset($conexion);
            # Si el correo o el usuario coincide con el password almacenado se le da la sesión y se le envía a index.php
            foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
                    if ($usuario['usuario'] == $_POST['usuarioOMail']) {
                        var_dump($usuario['usuario']);
                        $registrado = true;
                    } else if($usuario['email'] == $_POST['usuarioOMail']) {
                        var_dump($usuario['email']);
                        $registrado = true;
                    } 
    
                    if (password_verify($_POST['contrasenya'], $usuario['contrasenya']) && $registrado) {                    
                        $_SESSION['id'] = $usuario['id'];
                        $_SESSION['usuario'] = $usuario['usuario'];
                        $_SESSION['email'] = $usuario['email'];
                        header('Location: index.php');
                    } else{
                        $contrasenyaIncorrecta = true;
                    }
            }  
            if (isset($contrasenyaIncorrecta)) {
                $_SESSION['fallo'][] = "Usuario o contraseña incorrecta.";
            }
        }

    
    }
    
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Khojki:wght@400;600&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Revels - Iván Sáez</title>
</head>

<body>
    <header>
        <?php
            require_once('cabeceraNoLogueado.inc.php');
        ?>
    </header>
    <main class="fondo-1">
        <div class="contenedorFlex">
            <div class="contenedorLogin__logo">
                Revels
            </div>
            <div class="contenedorRegistro__registro">
                <h1 class="contenedorRegistro__registro--titulo">Iniciar sesión</h1>
                <form action="#" method="post">
                    <label for="usuarioOMail">Usuario</label><br>
                    <input type="text" name="usuarioOMail" id="usuarioOMail" value="<?= $_POST['usuarioOMail'] ?? '' ?>"><br>
                    <label for="contrasenya">Contraseña</label><br>
                    <input type="password" name="contrasenya" id="contrasenya" value="<?= $_POST['contrasenya'] ?? '' ?>"><br><br>
                    <input type="submit" class="contenedorLogin__login--boton" value="Entrar">
                </form>
                <?php
                # Si viene un fallo lo mostramos
                if (isset($_SESSION['fallo'])) {
                    echo '<span class="fallo"><b>ALERTA</b>: <br>';
                    foreach (array_values($_SESSION['fallo']) as $fallo => $value) {
                        echo '-'.$value.'<br>';
                    }
                    echo '</span>';
                    unset($_SESSION['fallo']);
                }
                ?>
            </div>
        </div>



    </main>
    <footer>
        <p>© 2022 Revels <br>Conselleria d'Educació, Cultura i Esport | Avís legal | Contacte<br>Iván Sáez Rodrigo</p>
    </footer>
</body>

</html>