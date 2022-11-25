<?php
    ##Especificamos que la sesión se llame SESSIONREVEL
    ini_set('session.name','SESSIONREVEL');
    session_start();

    # Si se trata de un usuario que no está logueado se le redirecciona a login.php
    if (!isset($_SESSION['usuario'])) {
        header('Location: login.php');
    } 

    # Si viene de su primer registro le asignamos la variable $_SESSION['id'] ya que es necesaría si se quieren modificar parametros en account (tal y como se hace en el log in)
    if (!isset($_SESSION['id'])) {
        require('conexionBBDD.inc.php');
        $resultado = $conexion->query('SELECT * FROM users WHERE usuario="'.$_SESSION['usuario'].'";');
        unset($conexion);
        foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
            $_SESSION['id'] = $usuario['id'];
        }
    }

    # Se revisa queno venga vacío el GET
    if (!empty($_GET)) {
        # Si presiona seguir a alguien se registrará en la tabla de follows
        if (isset($_GET['seguir'])) {
            # Se hace un regex y si se pasa se inserta en follows el follow
            if (preg_match('/[0-9]/',$_GET['seguir'])) {
                require('conexionBBDD.inc.php');
                $consulta = $conexion->prepare('INSERT INTO follows (userid,userfollowed) VALUES (?,?);');

                $consulta->bindParam(1, $_SESSION['id']);
                $consulta->bindParam(2, $_GET['seguir']);
        
                $consulta->execute();
                unset($conexion);

                # Se redireccionará a la busqueda que había hecho con anterioridad
                header('Location: results.php?busqueda='.$_SESSION['busqueda']);
            }
            # Se revisa de si se trata de un unfollow
        }  elseif(isset($_GET['unfollow'])) {

            # Pasamos todos las regex y si encuentra algún fallo se redirige a la ultima busqueda
            
            if (preg_match('/^[0-9]{1,}$/',$_GET['unfollow'])) {
                # Si no hay fallo elimina el follow 
                require('conexionBBDD.inc.php');
                $registros = $conexion->exec('DELETE FROM follows WHERE userid='.$_SESSION['id'].' AND userfollowed='.$_GET['unfollow'].';');
                unset($conexion);
                header('Location: results.php?busqueda='.$_SESSION['busqueda']);
                
            } else {
                header('Location: results.php?busqueda='.$_SESSION['busqueda']);
            }
    
        }
    } else {
        header('Location: results.php?busqueda='.$_SESSION['busqueda']);
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
            <div class="results">
                <div class="index__titulo">
                    <div class="index__logo">
                        Revels

                    </div>
                    <h2>Miembros</h2>
                </div>
                <div class="results__seguidos">
                    <div class="results__seguidos--lista">
                        <?php
                        # Se revisa que recibe por GET el parametro busqueda
                        if (isset($_GET['busqueda'])) {
                            if ($_GET['busqueda'] == "" && $_SESSION['busqueda']) {
                                header('Location: index.php');
                            }
                            # Se asocia la ultima busqueda en sesión para cuando se redirija
                            $_SESSION['busqueda'] = $_GET['busqueda'];                   

                            # Se buscan los seguidos
                            require('conexionBBDD.inc.php');
                            $siguiendo = $conexion->query('SELECT userfollowed FROM follows WHERE userid='.$_SESSION['id'].';');
                            $siguiendo = array_values($siguiendo->fetchAll(PDO::FETCH_COLUMN,0));

                            # Se busca el usuario que coincida con la busqueda 
                            $resultado = $conexion->query('SELECT * FROM users WHERE usuario LIKE "%'.$_GET['busqueda'].'%" AND id!='.$_SESSION['id'].';');
                            unset($conexion);
    
                            # Si ya existe se marca $fallo a true y se redireccionará de nuevo al registro
                            foreach ($resultado->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
                                echo '<a href="#"><div class="results__usuario">';
                                echo '<h2>'.$usuario['usuario'].'</h2>';
                                    # Si el usuario buscado lo sigue muestra la opción de dejar de seguir y si no lo sigue la de seguir
                                    if (in_array($usuario['id'],$siguiendo)) {
                                        echo '<a href="results.php?unfollow='.$usuario['id'].'" class="results__seguido">UNFOLLOW</a>';
                                        
                                    } else {
                                        echo '<a href="results.php?seguir='.$usuario['id'].'" class="results__seguir">SEGUIR</a>';
                                    }
                                echo '</div></a>';
                            }
                            
                        }
                            
                        ?>
                        
                    </div>
                </div>
            </aside>
        </div>



    </main>
    <footer>
        <p>© 2022 Revels <br>Conselleria d'Educació, Cultura i Esport | Avís legal | Contacte<br>Iván Sáez Rodrigo</p>
    </footer>
</body>

</html>