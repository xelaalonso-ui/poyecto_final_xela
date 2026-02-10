<?php
session_start();

// Verificar si el usuario ha iniciado sesión
// Descomentar esto si quieres proteger la página
/*
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}
*/

$usuario_actual = $_SESSION['usuario'] ?? 'Invitado';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de inicio</title>
    <link rel="stylesheet" href="style_p.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Gwendolyn:wght@400;700&family=Kalnia+Glaze:wdth@118&display=swap"
        rel="stylesheet">
</head>

<body>

    <div class="container">

        <div class="container2">
            <div class="titulo">
                <h1>Resulta, pasa <br> y <br> acontece... </h1>
            </div>
        </div>
        
        <!-- From Uiverse.io by pathikcomp -->
        <label class="main">
            Menu
            <input class="inp" checked="" type="checkbox" />
            <div class="bar">
                <span class="top bar-list"></span>
                <span class="middle bar-list"></span>
                <span class="bottom bar-list"></span>
            </div>
            <section class="menu-container">
                <div class="menu-list"><a href="p_principal.php">Inicio</a></div>
                <div class="menu-list"><a href="posts.php">Posts</a></div>
                <div class="menu-list"><a href="cuenta.php">Cuenta</a></div>
                <div class="menu-list"><a href="ajustes.php">Ajustes</a></div>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <div class="menu-list"><a href="logout.php">Cerrar Sesión</a></div>
                <?php endif; ?>
            </section>
        </label>

        <div class="cartas">
            <section class="card">
                <h2>Introducción de la aplicación</h2>
                <p>Esta nueva red social es para que la gente se pueda desahogar de lo que le pasa en la vida cotidiana
                    tanto en lo personal como en lo que le pase día a día, en el trabajo o con su vida amorosa o con
                    amigos o familia.
                    <br>
                    Así que si quieres desestresarte o simplemente chismear de los dramas de los demás, descárgate esta
                    aplicación.
                </p>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <p><strong>Bienvenido, <?php echo htmlspecialchars($usuario_actual); ?>!</strong></p>
                <?php endif; ?>
            </section>
            
            <div class="lazo">
                <img src="./img/lazo-de-cinta.png" alt="">
            </div>
            
            <div class="card2">
                <section class="card">
                    <h2>¿Qué puedes hacer aquí?</h2>
                    <p>Esta nueva red social es para que la gente se pueda desahogar de lo que le pasa en la vida cotidiana
                        tanto en lo personal como en lo que le pase día a día, en el trabajo o con su vida amorosa o con
                        amigos o familia.
                        <br>
                        Así que si quieres desestresarte o simplemente chismear de los dramas de los demás, descárgate esta
                        aplicación.
                    </p>
                </section>
            </div>
            
            <div class="lazo">
                <!-- <img src="./img/lazo-de-cinta.png" alt="" > -->
            </div>
        </div>
    </div>

</body>

</html>