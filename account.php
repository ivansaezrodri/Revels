<?php

    ##Especificamos que la sesión se llame SESSIONREVEL
    ini_set('session.name','SESSIONREVEL');
    session_start();


    # Si se trata de un usuario que no está logueado se le redirecciona a login.php
    if (!isset($_SESSION['usuario'])) {
        header('Location: login.php');
    } 
    

    # Si los campos están bien se modifican los valores cambiados
    if (!empty($_POST)) {

        # Eliminamos los espacios
        foreach ($_POST as $key => $value) {
            $key = trim($value);
        }
        
        # Si el campo usuario está modificado se aplicarán los criterios
        if ($_POST['usuario'] != $_SESSION['usuario']) {
            # Pasamos todos las regex y si encuentra algún fallo el flag $fallo pasa a true para que no se envie el formulario y si se almacena el fallo en $_SESSION[campo]
            if (!preg_match('/^[A-z\s0-9]{3,15}/',$_POST['usuario']) || $_POST['usuario'] == "") {
                $_SESSION['fallo'][] = "El campo Usuario no puede estar vacío y ha de contener como mínimo 3 caracteres y maximo 15.";
                $fallo = true;
            } 
        }
        
        # Sacamos el campo contrasenya para compararlo
        require('conexionBBDD.inc.php');
        $resultado = $conexion->query('SELECT contrasenya FROM users WHERE id='.$_SESSION['id'].';');
        unset($conexion);
        foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
                $datos['contrasenya'] = $usuario['contrasenya'];
        }  

        # Si el campo contrasenya está modificado se aplicarán los criterios
        if (!password_verify($_POST['contrasenya'], $datos['contrasenya']) && !empty($_POST['contrasenya'])) {
            # Pasamos todos las regex y si encuentra algún fallo el flag $fallo pasa a true para que no se envie el formulario y si se almacena el fallo en $_SESSION[campo]
            if (!preg_match('/^[A-z0-9\s]{6,20}$/',$_POST['contrasenya']) || $_POST['contrasenya'] == "") {
                $_SESSION['fallo'][] = "El campo Contraseña no puede estar vacío y ha de contener como mínimo 6 caracteres y máximo 20.";
                $fallo = true;
            } 
        }
    
        # Si el campo email está modificado se aplicarán los criterios
        if ($_POST['email'] != $_SESSION['email']) {
            # Pasamos todos las regex y si encuentra algún fallo el flag $fallo pasa a true para que no se envie el formulario y si se almacena el fallo en $_SESSION[campo]
            if (!preg_match('/^.+@.+\..+$/',$_POST['email']) || $_POST['email'] == "") {
                $_SESSION['fallo'][] = "El campo Correo no puede estar vacío y ha de estar correctamente formado.";
                $fallo = true;
            } 
        }

        # Listamos los usuarios
        require('conexionBBDD.inc.php');
        $resultado = $conexion->query('SELECT * FROM users;');
        unset($conexion);
        # Si ya existe se marca $fallo a true y se redireccionará de nuevo al registro
        foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
                if ($usuario['usuario'] == $_POST['usuario']) {
                    if ($_POST['usuario'] != $_SESSION['usuario']) {
                        $_SESSION['fallo'][] = "Usuario o correo ya registrado.";
                        $fallo = true;
                    }
                } else if($usuario['email'] == $_POST['email']) {
                    if ($_POST['email'] != $_SESSION['email']) {
                        $_SESSION['fallo'][] = "Usuario o correo ya registrado.";
                        $fallo = true;
                    }

                } 
        }

        # Si no existe el usuario en la BBDD se actualiza el campo modificado
        if (!isset($fallo)) {

            if ($_POST['usuario'] != $_SESSION['usuario']) {
                require('conexionBBDD.inc.php');
                $consulta = $conexion->prepare('UPDATE users SET usuario=? WHERE id=?;');
                $consulta->bindParam(1, $_POST['usuario']);
                $consulta->bindParam(2, $_SESSION['id']);
                $consulta->execute();
                unset($conexion);
                $_SESSION['usuario'] = $_POST['usuario'];
            }
            if ($_POST['email'] != $_SESSION['email']) {
                require('conexionBBDD.inc.php');
                $consulta = $conexion->prepare('UPDATE users SET email=? WHERE id=?;');
                $consulta->bindParam(1, $_POST['email']);
                $consulta->bindParam(2, $_SESSION['id']);
                $consulta->execute();
                unset($conexion);
                $_SESSION['email'] = $_POST['email'];
            }
            if (!password_verify($_POST['contrasenya'], $datos['contrasenya'])) {
                # Se hashea la contraseña y se inserta
                $contrasenyaEncriptada = password_hash($_POST['contrasenya'], PASSWORD_DEFAULT);
                require('conexionBBDD.inc.php');
                $consulta = $conexion->prepare('UPDATE users SET contrasenya=? WHERE id=?;');
                $consulta->bindParam(1, $contrasenyaEncriptada);
                $consulta->bindParam(2, $_SESSION['id']);
                $consulta->execute();
                unset($conexion);

            }

            
        } 

        header('Location: account.php');
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
            require_once('cabeceraLogueado.inc.php');
        ?>
    </header>
    <main class="fondo-3">
        <div class="contenedorFlex">
            <div class="contenedorLogin__logo">
                Revels
            </div>
            <div class="contenedorAccount__registro">

                <h1 class="contenedorAccount__registro--titulo">Mi perfil</h1>
                <form action="#" method="post">
                    <label for="usuario">Usuario</label><br>
                    <input type="text" name="usuario" id="usuario" value="<?= $_SESSION['usuario'] ?? '' ?>"><br>
                    <label for="contrasenya">Contraseña</label><br>
                    <input type="text" name="contrasenya" id="contrasenya" placeholder="********" >
                    <label for="email">Correo</label><br>
                    <input type="text" name="email" id="email" value="<?= $_SESSION['email'] ?? '' ?>"><br><br>
                    <input type="submit" class="contenedorLogin__login--boton" value="Guardar">
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
                <div class="contenedorAccount__links">
                    <a href="list.php" class="contenedorAccount__lista">Lista de Revels</a>
                    <a href="cancel.php" class="contenedorAccount__eliminar">Eliminar cuenta</a>
                </div>
            </div>
        </div>



    </main>
    <footer>
        <p>© 2022 Revels <br>Conselleria d'Educació, Cultura i Esport | Avís legal | Contacte<br>Iván Sáez Rodrigo</p>
    </footer>
</body>

</html>