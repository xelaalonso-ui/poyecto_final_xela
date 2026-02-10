<?php
session_start();
require_once "includes/db_connection.php";

$id=$_SESSION['id'];

$sql="SELECT * FROM usuarios WHERE id=$id";
$res=mysqli_query($db,$sql);
$user=mysqli_fetch_assoc($res);
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
