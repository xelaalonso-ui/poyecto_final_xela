<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    
    <link rel="shortcut icon" href="./img/agregar-contacto.png" type="image/x-icon">
 
  <link rel='stylesheet' href='./css/style.css'>
    
</head>
<body>
<div class="panda">
<img src="./img/panda-rojo (1).png" alt="" >
</div>

<div class="container"></div>
    <form class="formulario" action="./includes/registrar_usuario.php" method="post" enctype="multipart/form-data" 
   >
        <h2>Registro de Usuario</h2>
        <label for="nombre">Nombre Completo</label>
        <input type="text" id="nombre" name="nombre" placeholder="Ingresa tu nombre completo" required>
        
        <label for="fecha">Fecha de Nacimiento</label>
        <input type="date" id="fecha" name="fecha" required>
        
        <label for="correo">Correo Electrónico</label>
        <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com" required>
        
        <label for="clave">Contraseña</label>
        <input type="password" id="clave" name="clave" placeholder="Ingresa tu contraseña" required>
        
        <label for="imagen">Foto de Perfil</label>
        <input type="file" id="imagen" name="imagen">
        
        <div class="tipo-usuario">
            <input type="radio" id="usuario" name="rol" value="Usuario" required checked>
            <label for="usuario">Usuario </label>
            
            <input type="radio" id="administrador" name="rol" value="Administrador">
            <label for="administrador" >Administrador</label>
        </div>
        <p>Ya tienes cuenta?? <a href="./login.php">Inicia sesion</a></p>
        <button type="submit" >Registrarse</button>
        
       
            <script src="./js/botonsub.js"></script>

    </form>
</body>
</html>
