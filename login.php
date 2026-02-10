<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion</title>
    <link rel='stylesheet' href='./css/style.css'>
    <link rel="shortcut icon" href="./img/agregar-contacto.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Noto+Sans+Egyptian+Hieroglyphs&family=Noto+Serif+Balinese&family=Sankofa+Display&display=swap" rel="stylesheet">
</head>
<body>
    <?php
    session_start();
    if(isset($_SESSION['correo'])){
        header("Location:index.php");
    }
    ?>
<div class="container"></div>
<h1 class="texto">Entra en la mejor tienda animal. <img src="./img/zorro (1).png" alt=""></h1>
    <form class="formulario" method="POST" action="./includes/comprobar_usuario.php">
        <h2>Acceso Rápido</h2>
        <label for="correo">Correo Electrónico</label>
        <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com" required>
        
        <label for="clave">Contraseña</label>
        <input type="password" id="clave" name="clave" placeholder="Ingresa tu contraseña" required >

        
        <div class="botones">
            <button type= "submit"class="enviar">Enviar</button>
            <button type= "button"class="suscribirse" onclick="redireccionar()">Suscribirse</button>
        </div>
    </form>
    <script src="./js/botonsub.js"></script>
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2>Te as registrado exitosamente</h2>
            <p>Muchas gracias por registrarte</p>
            
            <button class="modal-button" onclick="cerrarModal()">Aceptar</button>
            
    </div>
   


  

</body>
</html>
