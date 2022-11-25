<?php
    $imgComment = '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="black" d="M7 14h10q.425 0 .712-.288Q18 13.425 18 13t-.288-.713Q17.425 12 17 12H7q-.425 0-.713.287Q6 12.575 6 13t.287.712Q6.575 14 7 14Zm0-3h10q.425 0 .712-.288Q18 10.425 18 10t-.288-.713Q17.425 9 17 9H7q-.425 0-.713.287Q6 9.575 6 10t.287.712Q6.575 11 7 11Zm0-3h10q.425 0 .712-.287Q18 7.425 18 7t-.288-.713Q17.425 6 17 6H7q-.425 0-.713.287Q6 6.575 6 7t.287.713Q6.575 8 7 8Zm13.3 12.3L18 18H4q-.825 0-1.412-.587Q2 16.825 2 16V4q0-.825.588-1.413Q3.175 2 4 2h16q.825 0 1.413.587Q22 3.175 22 4v15.575q0 .675-.612.937q-.613.263-1.088-.212Z"/></svg>';
    ##Especificamos que la sesión se llame SESSIONREVEL
    ini_set('session.name','SESSIONREVEL');
    session_start();

    # Si se trata de un usuario que no está logueado se le redirecciona a login.php
    if (!isset($_SESSION['usuario'])) {
        header('Location: login.php');
    } 

    # Revisamos si está rellenado el campo unfollow
    if (!empty($_GET['unfollow'])) {

        # Pasamos todos las regex y si encuentra algún fallo se redirige a index.php
        if (preg_match('/^[0-9]{1,}$/',$_GET['unfollow'])) {
            # Si no hay fallos se realiza el unfollow eliminando los rows que lo representan
            require('conexionBBDD.inc.php');
            $registros = $conexion->exec('DELETE FROM follows WHERE userid='.$_SESSION['id'].' AND userfollowed='.$_GET['unfollow'].';');
            unset($conexion);
            header('Location: index.php');
            
        } else {
            header('Location: index.php');
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
        <div class="index">
            <aside class="index__aside">
                <div class="index__titulo">
                    <div class="index__logo">
                        Revels

                    </div>
                    <h2>Seguidos</h2>
                </div>
                <div class="index__seguidos">
                    <div class="index">
                        <?php
                        # Listamos los usuarios seguidos por el usuario
                            require('conexionBBDD.inc.php');
                            $resultado = $conexion->query('SELECT users.usuario nombreusuarioseguido,follows.userfollowed idusuarioseguido from follows INNER JOIN users ON users.id=follows.userfollowed WHERE follows.userid='.$_SESSION['id'].';');
                            unset($conexion);

                            foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
                                echo '<div class="index__usuario">';
                                echo '<h2>'.$usuario['nombreusuarioseguido'].'</h2>';
                                echo '<a href="index.php?unfollow='.$usuario['idusuarioseguido'].'" class="results__seguido">UNFOLLOW</a>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>
            </aside>
            <div class="index__revels">
                <!-- Publicacion -->
                <?php

                # Listamos los revels del usuario junto con sus datos
                require('conexionBBDD.inc.php');
                $resultado = $conexion->query('SELECT revels.id revelid, revels.userid idusuariorevel, users.usuario nombreusuariorevel, revels.texto textorevel, revels.fecha fecharevel FROM revels INNER JOIN users on users.id=revels.userid WHERE revels.userid IN (SELECT follows.userfollowed FROM follows WHERE follows.userid = '.$_SESSION['id'].') OR revels.id IN (SELECT revels.id FROM revels WHERE revels.userid = '.$_SESSION['id'].') ORDER BY revels.fecha DESC;');
                unset($conexion);

                foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
                    echo '<div class="publicacion"><a href="revel.php?revelid='.$usuario['revelid'].'">';
                    echo '    <div class="publicacion__usuario">';
                    echo '        <h2>'.$usuario['nombreusuariorevel'].'</h2>';
                    echo '    </div>';
                    echo '    <p class="publicacion__texto">'.$usuario['textorevel'].'</p>';
                    echo '<div class="publicacion__propiedades"><span class="publicacion__fecha">'.$usuario['fecharevel'].'</span>';
                    # Se cuenta la cantidad de comentarios que tiene el revel 
                    require('conexionBBDD.inc.php');
                    $comentarios = $conexion->query('SELECT * FROM comments WHERE revelid='.$usuario['revelid']);
                    unset($conexion);
                    echo '<span class="publicacion__usuario--comentario">'.$imgComment.'</span><span class="publicacion__comentarios">'.$comentarios->rowCount().'</span></div>';
                    echo '</a></div>';
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