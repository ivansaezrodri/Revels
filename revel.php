<?php
    $imgBasura = '<svg xmlns="http://www.w3.org/2000/svg" widh="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 512 512"><rect width="448" height="80" x="32" y="48" fill="black" rx="32" ry="32"/><path fill="black" d="M74.45 160a8 8 0 0 0-8 8.83l26.31 252.56a1.5 1.5 0 0 0 0 .22A48 48 0 0 0 140.45 464h231.09a48 48 0 0 0 47.67-42.39v-.21l26.27-252.57a8 8 0 0 0-8-8.83Zm248.86 180.69a16 16 0 1 1-22.63 22.62L256 318.63l-44.69 44.68a16 16 0 0 1-22.63-22.62L233.37 296l-44.69-44.69a16 16 0 0 1 22.63-22.62L256 273.37l44.68-44.68a16 16 0 0 1 22.63 22.62L278.62 296Z"/></svg>';
    
    ##Especificamos que la sesión se llame SESSIONREVEL
    ini_set('session.name','SESSIONREVEL');
    session_start();

    # Si se trata de un usuario que no está logueado se le redirecciona a registro.php
    if (!isset($_SESSION['usuario'])) {
        header('Location: login.php');
    } 

    # Revisamos que exista el post
    if (!empty($_POST)) {
        if (isset($_POST['textoRevel'])) {
            # Se revisa de que cumple los requisitos y si los cumple se comenta
            if (strlen($_POST['textoRevel'])<220) {
                $fecha = date("Y-m-d H:i:s");
                require('conexionBBDD.inc.php');
                $consulta = $conexion->prepare('INSERT INTO comments (id,revelid,userid,texto,fecha) VALUES (NULL,?,?,?,?);');
                        
                $consulta->bindParam(1, $_GET['revelid']);
                $consulta->bindParam(2, $_SESSION['id']);
                $consulta->bindParam(3, $_POST['textoRevel']);
                $consulta->bindParam(4, $fecha);

                $consulta->execute();
                unset($conexion);
            } else {
                header('Location: revel.php?revelid='.$_SESSION['busqueda']);
            }
        }
    }

    # Si lo que se busca es eliminar el comentario revisamos que venga rellenado
    if (!empty($_GET['eliminarComentario'])) {

        # Pasamos todos las regex y si encuentra algún fallo se redirige al revel.php
        if (!preg_match('/^[0-9]$/',$_GET['eliminarComentario'])) {
            require('conexionBBDD.inc.php');
            $registros = $conexion->exec('DELETE FROM comments WHERE id='.$_GET['eliminarComentario'].';');
            unset($conexion);
        } else {
            header('Location: revel.php?revelid='.$_GET['revelid']);
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
        <div class="contenedorRevel">

            <?php
                # Se revisa que venga el get y se almacenan los datos del revel en $revel y el id del revel para redirigirse posteriormente
                if (!empty($_GET)) {
                    $revel['id'] = $_GET['revelid'];
                    $_SESSION['busqueda'] = $_GET['revelid'];
                    # Se quitan los espacios
                    foreach ($_GET as $key => $value) {
                        $key = trim($value);
                    }
                    
                    # Pasamos todos las regex y si encuentra algún fallo el flag $fallo pasa a true para que no se envie el formulario y si se almacena el fallo en $_SESSION[campo]
                    if (preg_match('/^[0-9]{1,999}$/',$_GET['revelid']) && $_GET['revelid'] != "") {
                        # Listamos los usuarios
                        require('conexionBBDD.inc.php');
                        $resultado = $conexion->query('SELECT revels.id revelid, revels.userid idusuariorevel, users.usuario nombreusuariorevel, revels.texto textorevel, revels.fecha fecharevel FROM revels INNER JOIN users on users.id=revels.userid;');
                        unset($conexion);
                        # Se muestra el revel en cuestión
                        foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
                            
                            if ($_GET['revelid'] == $usuario['revelid']) {
                                echo '<div class="publicacion">';
                                echo '    <div class="publicacion__usuario">';
                                echo '        <h2>'.$usuario['nombreusuariorevel'].'</h2>';
                                echo '    </div>';
                                echo '    <p class="publicacion__texto">'.$usuario['textorevel'].'</p>';
                                echo '<div class="publicacion__propiedades"><span class="publicacion__fecha">'.$usuario['fecharevel'].'</span>';
                                if ($usuario['idusuariorevel'] == $_SESSION['id']) {
                                    echo '<a href="delete.php?eliminarRevel='.$usuario['revelid'].'" class="publicacion__usuario--basura">'.$imgBasura.'</a></div>';
                                } else {
                                    echo '</div>';
                                }
                                echo '</div>';
                            }

                        }
                    } 
                }
            ?>

            <div class="contenedorNuevoRevel__caja revel__formulario">
                <form action="#" method="post">
                    <textarea class="contenedorNuevoRevel__caja--textArea" placeholder="Cuentanos tus inquietudes" name="textoRevel" id="textoRevel" cols="30" rows="10"></textarea>
                    <input type="submit" class="contenedorNuevoRevel__caja--boton" value="Enviar">
                </form>

            </div>

            <div class="revel__comentarios">
                <?php
                    # Se listan los comentarios del revel y si son los propios se da la opción de eliminarlos
                    require('conexionBBDD.inc.php');
                    $resultado = $conexion->query('SELECT comments.id idcomentario, comments.texto textocomentariorevel, users.usuario usuariocomentario, comments.fecha fechacomentario FROM comments INNER JOIN users ON comments.userid=users.id WHERE comments.revelid='.$revel['id'].';');
                    unset($conexion);

                    foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
                        if (isset($usuario['textocomentariorevel']) && $revel['id'] == $_GET['revelid']) {
                            echo '<div class="publicacion">';
                            echo '    <div class="publicacion__usuario">';
                            echo '        <h2>'.$usuario['usuariocomentario'].'</h2>';
                            echo '    </div>';
                            echo '    <p class="publicacion__texto">'.$usuario['textocomentariorevel'].'</p>';
                            echo '<div class="publicacion__propiedades"><span class="publicacion__fecha">'.$usuario['fechacomentario'].'</span>';
                            if ($usuario['usuariocomentario'] == $_SESSION['usuario']) {
                                echo '<a href="revel.php?eliminarComentario='.$usuario['idcomentario'].'&revelid='.$revel['id'].'" class="publicacion__usuario--basura">'.$imgBasura.'</a></div>';
                            } else {
                                echo '</div>';
                            }
                            echo '</div>';
                        }
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
