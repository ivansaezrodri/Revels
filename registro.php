<?php
    ##Especificamos que la sesión se llame SESSIONREVEL
    ini_set('session.name','SESSIONREVEL');
    session_start();
    
    # Si no está registrado se le redirige a index.php
    if (isset($_SESSION['usuario'])) {
        header('Location: index.php');
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
        <div class="contenedorRegistro">
            <div class="contenedorRegistro__logo">
                Revels
            </div>
            <div class="contenedorRegistro__registro">
                <h1 class="contenedorRegistro__registro--titulo">Registro</h1>
                <form action="altaUsuario.php" method="post">
                    <label for="usuario">Usuario</label><br>
                    <input type="text" name="usuario" id="usuario" value="<?= $_GET['u'] ?? '' ?>"><br>
                    <label for="contrasenya">Contraseña</label><br>
                    <input type="password" name="contrasenya" id="contrasenya"><br>
                    <label for="email">Correo</label><br>
                    <input type="text" name="email" id="email" value="<?= $_GET['e'] ?? '' ?>"><br><br>
                    <input type="submit" class="contenedorRegistro__registro--boton" value="Registrarse">
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