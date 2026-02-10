<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$usuario_actual = $_SESSION['usuario'] ?? 'Invitado';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descripción - Red Social</title>
    <link rel="stylesheet" href="style_p.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gwendolyn:wght@400;700&family=Kalnia+Glaze:wdth@118&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #84FFC9, #AAB2FF, #ECA0FF);
            min-height: 100vh;
            padding-bottom: 50px;
        }

        /* Navbar */
        .navbar {
            background-color: #eabaf6;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .navbar-brand {
            color: white;
            font-size: 22px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            font-family: 'Gwendolyn', cursive;
            line-height: 1.3;
        }

        .menu-nav {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }

        .menu-nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .menu-nav a:hover {
            background-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Panda decorativo */
        .panda {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 120px;
            z-index: 50;
            animation: float 3s ease-in-out infinite;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        /* Contenedor de cartas */
        .cartas {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        .card h2 {
            color: #8B5CF6;
            margin-bottom: 15px;
            font-family: 'Gwendolyn', cursive;
            font-size: 32px;
            text-align: center;
        }

        .card p {
            color: #555;
            line-height: 1.8;
            font-size: 16px;
            text-align: justify;
        }

        .card p strong {
            color: #8B5CF6;
            font-size: 18px;
        }

        /* Lazo decorativo */
        .lazo {
            text-align: center;
            margin: 20px 0;
        }

        .lazo img {
            width: 80px;
            opacity: 0.7;
            animation: rotate 4s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        /* Sección de bienvenida */
        .bienvenida {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
            position: relative;
            right: 200px;

        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-container {
                flex-direction: column;
                gap: 15px;
            }

            .navbar-brand {
                text-align: center;
                font-size: 18px;
            }

            .menu-nav {
                justify-content: center;
            }

            .panda {
                width: 80px;
                bottom: 10px;
                right: 10px;
            }

            .card {
                padding: 20px;
            }

            .card h2 {
                font-size: 26px;
            }

            .card p {
                font-size: 14px;
            }
        }

        /* Animación de entrada */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeInUp 0.6s ease-out;
        }

        .card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .card:nth-child(3) {
            animation-delay: 0.4s;
        }
    </style>
</head>

<body>
    <img src="./img/whisper (1).png" alt="Panda decorativo" class="panda">
    
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                Descripción<br>de la<br>Red Social
            </div>

            <div class="menu-nav" id="menuNav">
                <a href="p_principal.php">Inicio</a>
                <a href="descripcion.php">Descripción</a>
                <a href="publicaciones.php">Publicaciones</a>
                <a href="cuenta.php">Mi Cuenta</a>
                <a href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="cartas">
        <?php if (isset($_SESSION['usuario'])): ?>
            <div class="bienvenida">
                <div class="tenor-gif-embed" data-postid="11249423921160715390" data-share-method="host" data-aspect-ratio="1.33871" data-width="500px"><a href="https://tenor.com/view/hey-cat-waving-wave-paw-gif-11249423921160715390">Hey Cat GIF</a>from <a href="https://tenor.com/search/hey-gifs">Hey GIFs</a></div> <script type="text/javascript" async src="https://tenor.com/embed.js"></script>
                <h2> ¡Bienvenido, <?php echo htmlspecialchars($usuario_actual); ?>!</h2>
            </div>
        <?php endif; ?>

        <section class="card">
            <h2>✨ Introducción de la Aplicación</h2>
            <p>
                Esta nueva red social es para que la gente se pueda desahogar de lo que le pasa en la vida cotidiana,
                tanto en lo personal como en lo que le pase día a día: en el trabajo, con su vida amorosa, con
                amigos o familia.
            </p>
            <br>
            <p>
                Así que si quieres desestresarte o simplemente chismear de los dramas de los demás, ¡esta es tu 
                aplicación perfecta!
            </p>
        </section>

       

        <section class="card">
            <h2>🎯 ¿Qué Puedes Hacer Aquí?</h2>
            <p>
                En esta plataforma podrás:
            </p>
            <br>
            <p>
                📝 <strong>Compartir tus experiencias:</strong> Cuenta lo que te pasa sin filtros<br>
                💬 <strong>Interactuar con otros:</strong> Lee y comenta las historias de la comunidad<br>
                🔒 <strong>Mantener tu privacidad:</strong> Tú decides qué compartir y con quién<br>
                🌟 <strong>Desahogarte libremente:</strong> Un espacio seguro para expresarte
            </p>
        </section>

       

        <section class="card">
            <h2>💡 Únete a la Comunidad</h2>
            <p>
                No estás solo en tus experiencias. Miles de usuarios comparten sus historias diariamente.
                Descubre que no eres el único pasando por situaciones difíciles, y encuentra apoyo en nuestra
                comunidad.
            </p>
            <br>
            <p style="text-align: center; font-size: 18px; color: #8B5CF6;">
                <strong>¡Empieza a compartir hoy mismo!</strong>
            </p>
        </section>
    </div>

</body>

</html>