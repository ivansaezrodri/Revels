<?php
    # Especificamos que la sesión se llame SESSIONREVEL
    ini_set('session.name','SESSIONREVEL');
    session_start();

    # Si se trata de un usuario que no está logueado se le redirecciona a login.php
    if (!isset($_SESSION['usuario'])) {
        header('Location: login.php');
    } 

    # Si viene un POST['textorevel'] se revisa que venga con datos y sean los establecidos
    if (!empty($_POST)) {
        if (isset($_POST['textoRevel'])) {
            if (preg_match('/^[A-z\s.,"\-\'0-9]{1,220}$/',$_POST['textoRevel'])) {
                # Si todo está correcto se envía (no tengo muy claro como admitir acentos)
                $fecha = date("Y-m-d H:i:s");
                require_once('conexionBBDD.inc.php');

                $consulta = $conexion->prepare('INSERT INTO revels (userid,texto,fecha) VALUES (?,?,?);');

                $consulta->bindParam(1, $_SESSION['id']);
                $consulta->bindParam(2, $_POST['textoRevel']);
                $consulta->bindParam(3, $fecha);
        
                $consulta->execute();

                $revelid=$conexion->lastInsertId();
                unset($conexion);
                # Por ultimo se redirige a la página del revel
                header('Location: revel.php?revelid='.$revelid);
                
            } else {
                header('Location: newRevel.php');
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
            require_once('cabeceraLogueado.inc.php');
        ?>
    </header>
    <main class="fondo-2">
        <div class="contenedorNuevoRevel">
            <div class="contenedorLista__titulo">
                <div class="contenedorLista__logo">
                    Revels

                </div>
                <h2>Nuevo revel</h2>
            </div>

            <div class="contenedorNuevoRevel__caja">
                <form action="newRevel.php" method="post">
                    <textarea class="contenedorNuevoRevel__caja--textArea" placeholder="Cuentanos tus inquietudes" name="textoRevel" id="textoRevel" cols="30" rows="10"></textarea>
                    <input type="submit" class="contenedorNuevoRevel__caja--boton" value="Enviar">
                </form>

            </div>

        </div>



    </main>
    <footer>
        <p>© 2022 Revels <br>Conselleria d'Educació, Cultura i Esport | Avís legal | Contacte<br>Iván Sáez Rodrigo</p>
    </footer>
</body>

</html>