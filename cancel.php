<?php
    ##Especificamos que la sesión se llame SESSIONREVEL
    ini_set('session.name','SESSIONREVEL');
    session_start();


    # Si se trata de un usuario que no está logueado se le redirecciona a login.php
    if (!isset($_SESSION['usuario'])) {
        header('Location: login.php');
    } 

    # Se revisa si el checkbox de eliminar cuenta está marcado 
    if (!empty($_POST['eliminarCuenta'])) {
        
        if (true == $_POST['eliminarCuenta']) {
            require_once('conexionBBDD.inc.php');
            # Para no cambiar y reescribir las claves con el ON DELETE CASCADE cada vez (ya que cambiarlo en mi BBDD una única 
            # vez no va a hacer que tu lo tengas cuando lo revises) lo desactivo y lo activo posteriormente. Se que esto no se
            # ha de hacer pero lo hago para que lo evalues
            $registros = $conexion->exec('SET FOREIGN_KEY_CHECKS = 0;');
            $registros = $conexion->exec('DELETE FROM follows WHERE userid='.$_SESSION['id'].';');
            $registros = $conexion->exec('DELETE FROM comments WHERE userid='.$_SESSION['id'].';');
            $registros = $conexion->exec('DELETE FROM revels WHERE userid='.$_SESSION['id'].';');
            $registros = $conexion->exec('DELETE FROM users WHERE id='.$_SESSION['id'].';');
            $registros = $conexion->exec('SET FOREIGN_KEY_CHECKS = 1;');
            unset($conexion);
            # Desvinculamos el usuario y la sesión 
            unset($_SESSION['usuario']);
            unset($_SESSION['id']);
            unset($_SESSION['email']);

            header('Location: registro.php');
        } else {
            header('Location: cancel.php');
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
            require_once('cabeceraLogueado.inc.php');
        ?>
    </header>
    <main class="fondo-3">
        <div class="contenedorFlex">
            <div class="contenedorCancelar">
                <h1>Eliminar cuenta :(</h1>
                <p class="contenedorCancelar__texto">¿Estas seguro de que quieres dejar de formar parte de la familia <b>Revels</b>?</p>
                <form action="#" method="post">
                    <input type="checkbox" name="eliminarCuenta" id="eliminarCuenta">&nbsp;<label for="eliminarCuenta">Borrar tu cuenta junto con las revelaciones y comentarios.</label><br><br>
                    <input type="submit" class="contenedorCancelar__login--boton" class="contenedorRegistro__boton"value="Eliminar">
                </form>
            </div>
        </div>



    </main>
    <footer>
        <p>© 2022 Revels <br>Conselleria d'Educació, Cultura i Esport | Avís legal | Contacte<br>Iván Sáez Rodrigo</p>
    </footer>
</body>

</html>