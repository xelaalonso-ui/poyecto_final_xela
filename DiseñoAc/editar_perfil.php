<?php
session_start();
require_once "includes/db_connection.php";

// Verificar si el usuario ha iniciado sesión
if(!isset($_SESSION['id'])){
    header('Location: index.php');
    exit;
}

$id_usuario = $_SESSION['id'];
$mensaje_exito = '';
$mensaje_error = '';

// Procesar formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = mysqli_real_escape_string($db, $_POST['nombre'] ?? '');
    $apellido = mysqli_real_escape_string($db, $_POST['apellido'] ?? '');
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $genero = mysqli_real_escape_string($db, $_POST['genero'] ?? '');
    $direccion = mysqli_real_escape_string($db, $_POST['direccion'] ?? '');
    $telefono = mysqli_real_escape_string($db, $_POST['telefono'] ?? '');
    
    // Verificar si ya existen datos personales
    $check_sql = "SELECT id_datos FROM Datos_personales WHERE id_usuario = $id_usuario";
    $check_result = mysqli_query($db, $check_sql);
    
    if(mysqli_num_rows($check_result) > 0){
        // Actualizar datos existentes
        $sql = "UPDATE Datos_personales SET 
                nombre = '$nombre',
                apellido = '$apellido',
                fecha_nacimiento = " . ($fecha_nacimiento ? "'$fecha_nacimiento'" : "NULL") . ",
                genero = '$genero',
                direccion = '$direccion',
                telefono = '$telefono'
                WHERE id_usuario = $id_usuario";
    } else {
        // Insertar nuevos datos
        $sql = "INSERT INTO Datos_personales (id_usuario, nombre, apellido, fecha_nacimiento, genero, direccion, telefono)
                VALUES ($id_usuario, '$nombre', '$apellido', 
                " . ($fecha_nacimiento ? "'$fecha_nacimiento'" : "NULL") . ", 
                '$genero', '$direccion', '$telefono')";
    }
    
    if(mysqli_query($db, $sql)){
        // Procesar foto de perfil si se subió
        if(isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0){
            $foto_nombre = $_FILES['foto_perfil']['name'];
            $foto_tmp = $_FILES['foto_perfil']['tmp_name'];
            $extension = pathinfo($foto_nombre, PATHINFO_EXTENSION);
            $nuevo_nombre = 'perfil_' . $id_usuario . '_' . time() . '.' . $extension;
            $foto_destino = 'uploads/' . $nuevo_nombre;

            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if(move_uploaded_file($foto_tmp, $foto_destino)){
                // Eliminar foto de perfil anterior
                mysqli_query($db, "DELETE FROM Fotos WHERE id_usuario = $id_usuario AND tipo_foto = 'perfil'");
                
                // Guardar nueva foto
                $sql_foto = "INSERT INTO Fotos (id_usuario, url_foto, descripcion, tipo_foto) 
                             VALUES ($id_usuario, '$foto_destino', 'Foto de perfil', 'perfil')";
                mysqli_query($db, $sql_foto);
            }
        }
        
        $mensaje_exito = "Perfil actualizado correctamente";
        
        // Registrar actividad
        mysqli_query($db, "INSERT INTO Actividad (id_usuario, tipo_actividad, descripcion) 
                          VALUES ($id_usuario, 'edicion_perfil', 'Perfil actualizado')");
    } else {
        $mensaje_error = "Error al actualizar: " . mysqli_error($db);
    }
}

// Obtener datos actuales
$sql_usuario = "SELECT u.*, d.nombre, d.apellido, d.fecha_nacimiento, d.genero, d.direccion, d.telefono
                FROM Usuario u
                LEFT JOIN Datos_personales d ON u.id_usuario = d.id_usuario
                WHERE u.id_usuario = $id_usuario";

$resultado = mysqli_query($db, $sql_usuario);
$usuario = mysqli_fetch_assoc($resultado);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
            color: black;
        }
        
        .menu-nav {
            text-align: center;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
        }
        
        .menu-nav a {
            color: #007bff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
        }
        
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        h2 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
            color: black;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="date"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
            color: black;
        }
        
        input[type="file"] {
            padding: 5px;
        }
        
        .info-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            color: black;
        }
        
        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 500;
        }
        
        .btn-guardar {
            background-color: #ff5ad6;
            color: white;
        }
        
        .btn-guardar:hover {
            background-color: #ff6ab4;
        }
        
        .btn-cancelar {
            background-color: #6c757d;
            color: white;
            color: black;
        }
        
        .btn-cancelar:hover {
            background-color: #5a6268;
            color: black;
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

<div class="form-container">
    <h2>Editar Perfil</h2>
    
    <?php if ($mensaje_exito): ?>
        <div class="mensaje exito"><?php echo htmlspecialchars($mensaje_exito); ?></div>
    <?php endif; ?>
    
    <?php if ($mensaje_error): ?>
        <div class="mensaje error"><?php echo htmlspecialchars($mensaje_error); ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Usuario</label>
            <input type="text" value="<?php echo htmlspecialchars($usuario['username']); ?>" disabled>
            <div class="info-text">El nombre de usuario no se puede cambiar</div>
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <input type="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>
            <div class="info-text">El email no se puede cambiar</div>
        </div>
        
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" 
                   value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" id="apellido" name="apellido" 
                   value="<?php echo htmlspecialchars($usuario['apellido'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" 
                   value="<?php echo htmlspecialchars($usuario['fecha_nacimiento'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="genero">Género</label>
            <select id="genero" name="genero">
                <option value="">Seleccionar...</option>
                <option value="Masculino" <?php echo ($usuario['genero'] ?? '') === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                <option value="Femenino" <?php echo ($usuario['genero'] ?? '') === 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                <option value="Otro" <?php echo ($usuario['genero'] ?? '') === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                <option value="Prefiero no decir" <?php echo ($usuario['genero'] ?? '') === 'Prefiero no decir' ? 'selected' : ''; ?>>Prefiero no decir</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono" 
                   value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" 
                   value="<?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="foto_perfil">Cambiar Foto de Perfil</label>
            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
            <div class="info-text">Formatos aceptados: JPG, PNG, GIF (máx. 5MB)</div>
        </div>
        
        <div class="btn-container">
            <button type="submit" class="btn-guardar">Guardar Cambios</button>
            <button type="button" class="btn-cancelar" onclick="window.location.href='cuenta.php'">Cancelar</button>
        </div>
    </form>
</div>

</body>
</html>