<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Lista de Usuarios</title>
    <link rel='stylesheet' href='./css/style.css'>
    <link rel="shortcut icon" href="./img/agregar-contacto.png" type="image/x-icon">
</head>
<body>
<div class="container"></div>

<?php
         include_once("includes/funciones.php");
    $usuarios = mostrarUsuarios();
        foreach ($usuarios as $usuario) {
         ?>
    <section class="card2">
    <?php 
    
  if($usuario['nombre_imagen']<>''){
    $imagenbase64 = base64_encode($usuario['imagen']);
    $tipoimagen = $usuario['tipo_imagen'];
    $urlimagen = "data:$tipoimagen;base64,$imagenbase64";
    }else{
        $urlimagen="./img/orgulloso.png";
    }
    ?>
    <img class="foto" src="<?php echo $urlimagen; ?>" alt="Foto perfil">

    <p>Nombre:<?php echo $usuario['nombre'] ?></p>
            <p>Correo:<?php echo $usuario['correo'] ?></p>
            <p>Fecha de nacimiento:<?php echo $usuario['fecha_nacimiento'] ?></p>
            <p>Rol:<?php echo $usuario['rol'] ?></p>

            <a href="./includes/eleminarUsuarios.php?id=<?php echo $usuario['id']?>">Elimina al usuario</a>
    </section>
         
        <?php
        }
    ?>      
    <div class="botones">
    <button class="Btn"><a href="./index.php">Atras</a></button>
    <button class="Btn"><a href="./includes/cerrar_ssion.php">Cierre sesion</a></button>
    </div>
            </select>
         
        </div>
    </div>

    <script src="./js/botonsub.js"></script>
    <footer>
        
    </footer>
</body>
</html>