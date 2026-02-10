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
    <script src="efectos.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
            background: linear-gradient(90deg, #84FFC9, #AAB2FF, #ECA0FF);
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
            box-sizing: border-box;
            color: black;
        }
        
        button {
            background-color: #5500c4;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        
        button:hover {
            background-color: #9d35ff;
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
        
        .menu-nav {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .menu-nav a {
            color: #6c0081;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
        }
        
        .menu-nav a:hover {
            text-decoration: underline;
        }
        
        .no-publicaciones {
            text-align: center;
            color: #666;
            padding: 40px;
            background: white;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="menu-nav">
    <a href="p_principal.php">Inicio</a>
    <a href="publicaciones.php">Publicaciones</a>
    <a href="cuenta.php">Mi Cuenta</a>
    <a href="logout.php">Cerrar Sesión</a>
</div>

<h2>Publicaciones</h2>



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

</body>
</html>