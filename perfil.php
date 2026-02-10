<?php
require "includes/comprobar_sesion.php";
require "includes/db_connection.php";

$id=$_SESSION['id'];
$res=mysqli_query($conn,
"SELECT * FROM Datos_personales WHERE id_usuario=$id");
$perfil=mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<script src="efectos.js"></script>
</head>
<body>

<?php include "menu.php"; ?>

<h2>Perfil</h2>
<p>Usuario: <?=$user['usuario']?></p>
<p>Email: <?=$user['correo']?></p>

<?php if($user['foto']): ?>
<img src="<?=$user['foto']?>" width="120">
<?php endif; ?>

</body>
</html>
