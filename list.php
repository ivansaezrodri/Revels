<?php
    $imgBasura = '<svg xmlns="http://www.w3.org/2000/svg" widh="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 512 512"><rect width="448" height="80" x="32" y="48" fill="black" rx="32" ry="32"/><path fill="black" d="M74.45 160a8 8 0 0 0-8 8.83l26.31 252.56a1.5 1.5 0 0 0 0 .22A48 48 0 0 0 140.45 464h231.09a48 48 0 0 0 47.67-42.39v-.21l26.27-252.57a8 8 0 0 0-8-8.83Zm248.86 180.69a16 16 0 1 1-22.63 22.62L256 318.63l-44.69 44.68a16 16 0 0 1-22.63-22.62L233.37 296l-44.69-44.69a16 16 0 0 1 22.63-22.62L256 273.37l44.68-44.68a16 16 0 0 1 22.63 22.62L278.62 296Z"/></svg>';
    
    ##Especificamos que la sesión se llame SESSIONREVEL
    ini_set('session.name','SESSIONREVEL');
    session_start();

    # Si se trata de un usuario que no está logueado se le redirecciona a login.php
    if (!isset($_SESSION['usuario'])) {
        header('Location: login.php');
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
        <div class="contenedorLista">
            <div class="contenedorLista__titulo">
                <div class="contenedorLista__logo">
                    Revels

                </div>
                <h2>Mis publicaciones</h2>
            </div>

            <?php
                #  Se listan todos lo revels del usuario logueado
                require_once('conexionBBDD.inc.php');
                $resultado = $conexion->query('SELECT * FROM revels WHERE userid='.$_SESSION['id'].';');
                unset($conexion);

                foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
                    echo '<div class="publicacion"><a href="revel.php?revelid='.$usuario['id'].'">';
                    echo '    <div class="publicacion__usuario">';
                    echo '        <h2>'.$_SESSION['usuario'].'</h2>';
                    echo '    </div>';
                    echo '    <p class="publicacion__texto">'.$usuario['texto'].'</p>';
                    echo '<div class="publicacion__propiedades"><span class="publicacion__fecha">'.$usuario['fecha'].'</span><a href="delete.php?eliminarRevel='.$usuario['id'].'" class="publicacion__usuario--basura">'.$imgBasura.'</a></div>';
                    echo '</a></div>';
                }

            ?>
        </div>



    </main>
    <footer>
        <p>© 2022 Revels <br>Conselleria d'Educació, Cultura i Esport | Avís legal | Contacte<br>Iván Sáez Rodrigo</p>
    </footer>
</body>

</html>