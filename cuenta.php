<?php
session_start();
require_once "includes/db_connection.php";

// Verificar si el usuario ha iniciado sesión
if(!isset($_SESSION['id'])){
    header('Location: index.php');
    exit;
}

$id_usuario = $_SESSION['id'];

// Obtener datos del usuario
$sql_usuario = "SELECT u.*, d.nombre, d.apellido, d.fecha_nacimiento, d.genero, d.direccion, d.telefono
                FROM Usuario u
                LEFT JOIN Datos_personales d ON u.id_usuario = d.id_usuario
                WHERE u.id_usuario = $id_usuario";

$resultado = mysqli_query($db, $sql_usuario);
$usuario = mysqli_fetch_assoc($resultado);

// Obtener foto de perfil
$sql_foto = "SELECT url_foto FROM Fotos 
             WHERE id_usuario = $id_usuario AND tipo_foto = 'perfil' 
             ORDER BY fecha_subida DESC LIMIT 1";
$resultado_foto = mysqli_query($db, $sql_foto);
$foto = mysqli_fetch_assoc($resultado_foto);

// Contar publicaciones del usuario
$sql_count = "SELECT COUNT(*) as total FROM Fotos WHERE id_usuario = $id_usuario AND tipo_foto = 'publicacion'";
$resultado_count = mysqli_query($db, $sql_count);
$count = mysqli_fetch_assoc($resultado_count);

// Obtener últimas publicaciones
$sql_posts = "SELECT * FROM Fotos 
              WHERE id_usuario = $id_usuario AND tipo_foto = 'publicacion' 
              ORDER BY fecha_subida DESC LIMIT 5";
$resultado_posts = mysqli_query($db, $sql_posts);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
            background: linear-gradient(90deg, #84FFC9, #AAB2FF, #ECA0FF);
        }
        
        .menu-nav {
            text-align: center;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
        }
        
        .menu-nav a {
            color: #d755ff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
        }
        
        .menu-nav a:hover {
            text-decoration: underline;
        }
        
        .perfil-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .perfil-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }
        
        .foto-perfil {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 30px;
            border: 3px solid #ff78f8;
        }
        
        .foto-perfil-default {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin-right: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            font-weight: bold;
        }
        
        .info-usuario h2 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .info-usuario p {
            margin: 5px 0;
            color: #666;
        }
        
        .estadisticas {
            display: flex;
            gap: 30px;
            margin-top: 15px;
        }
        
        .estadistica {
            text-align: center;
        }
        
        .estadistica .numero {
            font-size: 24px;
            font-weight: bold;
            color: #7d20bb;
        }
        
        .estadistica .label {
            font-size: 14px;
            color: #666;
        }
        
        .datos-section {
            margin-bottom: 30px;
        }
        
        .datos-section h3 {
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .dato-item {
            display: flex;
            margin-bottom: 10px;
        }
        
        .dato-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        
        .dato-valor {
            color: #333;
        }
        
        .ultimas-publicaciones {
            margin-top: 30px;
        }
        
        .publicacion {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            border-left: 3px solid #c74dff;
            color: black;
        }
        
        .publicacion .fecha {
            font-size: 12px;
            color: #999;
            margin-bottom: 5px;
        }
        
        .publicacion .contenido {
            color: #333;
        }
        
        .btn-editar {
            display: inline-block;
            background: #f93eff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }
        
        .btn-editar:hover {
            background: #da7bff;
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

<div class="perfil-container">
    <div class="perfil-header">
        <?php if($foto && !empty($foto['url_foto'])): ?>
            <img src="<?php echo htmlspecialchars($foto['url_foto']); ?>" alt="Foto de perfil" class="foto-perfil">
        <?php else: ?>
            <div class="foto-perfil-default">
                <?php echo strtoupper(substr($usuario['username'], 0, 1)); ?>
            </div>
        <?php endif; ?>
        
        <div class="info-usuario">
            <h2>@<?php echo htmlspecialchars($usuario['username']); ?></h2>
            <p><?php echo htmlspecialchars($usuario['email']); ?></p>
            
            <div class="estadisticas">
                <div class="estadistica">
                    <div class="numero"><?php echo $count['total']; ?></div>
                    <div class="label">Publicaciones</div>
                </div>
                <div class="estadistica">
                    <div class="numero"><?php echo ucfirst($usuario['estado_cuenta']); ?></div>
                    <div class="label">Estado</div>
                </div>
                <div class="estadistica">
                    <div class="numero">
                        <?php 
                        $fecha_registro = new DateTime($usuario['fecha_registro']);
                        echo $fecha_registro->format('d/m/Y'); 
                        ?>
                    </div>
                    <div class="label">Miembro desde</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="datos-section">
        <h3>Información Personal</h3>
        
        <?php if($usuario['nombre'] || $usuario['apellido']): ?>
            <div class="dato-item">
                <span class="dato-label">Nombre completo:</span>
                <span class="dato-valor">
                    <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                </span>
            </div>
        <?php endif; ?>
        
        <?php if($usuario['fecha_nacimiento']): ?>
            <div class="dato-item">
                <span class="dato-label">Fecha de nacimiento:</span>
                <span class="dato-valor">
                    <?php 
                    $fecha_nac = new DateTime($usuario['fecha_nacimiento']);
                    echo $fecha_nac->format('d/m/Y'); 
                    ?>
                </span>
            </div>
        <?php endif; ?>
        
        <?php if($usuario['genero']): ?>
            <div class="dato-item">
                <span class="dato-label">Género:</span>
                <span class="dato-valor"><?php echo htmlspecialchars($usuario['genero']); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if($usuario['telefono']): ?>
            <div class="dato-item">
                <span class="dato-label">Teléfono:</span>
                <span class="dato-valor"><?php echo htmlspecialchars($usuario['telefono']); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if($usuario['direccion']): ?>
            <div class="dato-item">
                <span class="dato-label">Dirección:</span>
                <span class="dato-valor"><?php echo htmlspecialchars($usuario['direccion']); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if($usuario['ultimo_login']): ?>
            <div class="dato-item">
                <span class="dato-label">Último acceso:</span>
                <span class="dato-valor">
                    <?php 
                    $ultimo_login = new DateTime($usuario['ultimo_login']);
                    echo $ultimo_login->format('d/m/Y H:i'); 
                    ?>
                </span>
            </div>
        <?php endif; ?>
        
        <?php if(!$usuario['nombre'] && !$usuario['apellido'] && !$usuario['fecha_nacimiento']): ?>
            <p style="color: #999; font-style: italic;">
                No has completado tu información personal. 
                <a href="editar_perfil.php">Completa tu perfil aquí</a>
            </p>
        <?php endif; ?>
    </div>
    
    <?php if(mysqli_num_rows($resultado_posts) > 0): ?>
        <div class="ultimas-publicaciones">
            <h3>Últimas Publicaciones</h3>
            <?php while($post = mysqli_fetch_assoc($resultado_posts)): ?>
                <div class="publicacion">
                    <div class="fecha">
                        <?php 
                        $fecha_pub = new DateTime($post['fecha_subida']);
                        echo $fecha_pub->format('d/m/Y H:i'); 
                        ?>
                    </div>
                    <div class="contenido">
                        <?php echo nl2br(htmlspecialchars($post['descripcion'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
    
    <a href="editar_perfil.php" class="btn-editar">Editar Perfil</a>
</div>

</body>
</html>