<?php
session_start();
require_once "includes/db_connection.php";

// Verificar si el usuario ha iniciado sesión
if(!isset($_SESSION['id'])){
    header('Location: index.php');
    exit;
}

// Procesar nueva publicación
if(isset($_POST['publicar'])){
    $texto = $_POST['texto'] ?? '';
    $id = $_SESSION['id'];
    
    if(!empty($texto)){
        // Escapar datos para prevenir SQL injection
        $texto_limpio = mysqli_real_escape_string($db, $texto);
        
        // Insertar publicación en la tabla Fotos
        $sql = "INSERT INTO Fotos (id_usuario, descripcion, tipo_foto) 
                VALUES ($id, '$texto_limpio', 'publicacion')";
        
        if(mysqli_query($db, $sql)){
            // Registrar actividad
            mysqli_query($db, "INSERT INTO Actividad (id_usuario, tipo_actividad, descripcion) 
                              VALUES ($id, 'publicacion', 'Nueva publicación creada')");
            
            $mensaje_exito = "Publicación creada exitosamente";
        } else {
            $mensaje_error = "Error al publicar: " . mysqli_error($db);
        }
    } else {
        $mensaje_error = "El texto de la publicación no puede estar vacío";
    }
}

// Obtener todas las publicaciones
$res = mysqli_query($db,
    "SELECT f.*, u.username, u.email
     FROM Fotos f
     JOIN Usuario u ON u.id_usuario = f.id_usuario
     WHERE f.tipo_foto = 'publicacion'
     ORDER BY f.fecha_subida DESC");

if(!$res){
    die("Error al obtener publicaciones: " . mysqli_error($db));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicaciones</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            background: linear-gradient(90deg, #84FFC9, #AAB2FF, #ECA0FF);
        }

        /* Navbar */
        .navbar {
            background-color: #eabaf6;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: relative;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
        }

        .menu-toggle span {
            display: block;
            width: 25px;
            height: 3px;
            background-color: white;
            margin: 5px 0;
            transition: 0.3s;
        }

        .menu-nav {
            display: flex;
            gap: 30px;
        }

        .menu-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .menu-nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Contenido principal */
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }

        .mensaje {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .exito {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            resize: vertical;
        }

        button[type="submit"] {
            background-color: #ffc8c4;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background-color: #ceafff;
        }

        .publicacion {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .publicacion .autor {
            font-weight: bold;
            color: #600047;
            margin-bottom: 5px;
        }

        .publicacion .contenido {
            color: #333;
            line-height: 1.6;
            margin: 10px 0;
        }

        .publicacion .fecha {
            color: #666;
            font-size: 12px;
            margin-top: 10px;
        }

        .no-publicaciones {
            text-align: center;
            color: #666;
            padding: 40px;
            background: white;
            border-radius: 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .menu-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: #ffc8dd;
                flex-direction: column;
                gap: 0;
                padding: 10px 0;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }

            .menu-nav.active {
                display: flex;
            }

            .menu-nav a {
                padding: 15px 20px;
                border-radius: 0;
            }

            .menu-nav a:hover {
                background-color: rgba(255, 255, 255, 0.1);
            }
        }
        .panda{
            position: relative;
            top: 39px;
            z-index: 1;
        }
        .koala{
            position: relative;
            left:  400px;
            bottom: 150px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
     <img src="./img/panda.png" alt="" class="panda
     ">
    
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">Mi Red Social</div>
          
            
            <button class="menu-toggle" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="menu-nav" id="menuNav">
                <a href="descripcion.php">Descripion</a>
                <a href="publicaciones.php">Publicaciones</a>
                <a href="cuenta.php">Mi Cuenta</a>
                <a href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <!-- Contenido -->
    <div class="container">
        <h2>Publicaciones</h2>

        <?php if (isset($mensaje_exito)): ?>
            <div class="mensaje exito"><?php echo htmlspecialchars($mensaje_exito); ?></div>
        <?php endif; ?>

        <?php if (isset($mensaje_error)): ?>
            <div class="mensaje error"><?php echo htmlspecialchars($mensaje_error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <textarea name="texto" placeholder="¿Qué está pasando?" required></textarea>
            <button type="submit" name="publicar">Publicar</button>
        </form>

        <div class="publicaciones-container">
            <?php if(mysqli_num_rows($res) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($res)): ?>
                    <div class="publicacion">
                        <div class="autor">@<?php echo htmlspecialchars($row['username']); ?></div>
                        <div class="contenido"><?php echo nl2br(htmlspecialchars($row['descripcion'])); ?></div>
                        <div class="fecha">
                            <?php 
                            $fecha = new DateTime($row['fecha_subida']);
                            echo $fecha->format('d/m/Y H:i'); 
                            ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-publicaciones">
                    <p>Aún no hay publicaciones. ¡Sé el primero en publicar algo!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('menuNav');
            menu.classList.toggle('active');
        }

        // Cerrar menú al hacer clic en un enlace (móvil)
        document.querySelectorAll('.menu-nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    document.getElementById('menuNav').classList.remove('active');
                }
            });
        });

        // Cerrar menú al hacer clic fuera (móvil)
        document.addEventListener('click', (e) => {
            const menu = document.getElementById('menuNav');
            const toggle = document.querySelector('.menu-toggle');
            
            if (!menu.contains(e.target) && !toggle.contains(e.target)) {
                menu.classList.remove('active');
            }
        });
    </script>
    <script src="efectos.js"></script>
   
</body>
</html>