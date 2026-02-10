<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Modificar Usuario</title>
    <link rel='stylesheet' href='./css/style.css'>
    <link rel="shortcut icon" href="./img/agregar-contacto.png" type="image/x-icon">
</head>
<body>
    <div class="container"></div>
    <form class="formulario" action="./includes/editar.php" method="post" enctype="multipart/form-data" 
   >
        <h2>Modificar Usuario</h2>
        <?php
        session_start()
        ?>
        <label for="nombre">Nombre Completo</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo $_SESSION['nombre'] ?>" placeholder="Ingresa tu nombre completo" required>
        
        <label for="fecha">Fecha de Nacimiento</label>
        <input type="date" id="fecha" name="fecha" value="<?php echo $_SESSION['fecha_nacimiento'] ?>" required>
        
        <label for="correo">Correo Electrónico</label>
        <input type="email" id="correo" name="correo" value="<?php echo $_SESSION['correo'] ?>" placeholder="ejemplo@correo.com" required>
        
        <label for="clave">Contraseña</label>
        <input type="password" id="clave" name="clave" placeholder="Ingresa tu contraseña" >
        
        <label for="imagen">Foto de Perfil</label>
        <input type="file" id="imagen" name="imagen">
        <button type="submit" >Modificar</button>
        </form>
</body>
</html>

