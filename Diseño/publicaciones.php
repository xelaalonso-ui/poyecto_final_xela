<?php
session_start();
require_once "includes/db_connection.php";

if(isset($_POST['publicar'])){
    $texto=$_POST['texto'];
    $id=$_SESSION['id'];

    mysqli_query($db,
      "INSERT INTO publicaciones(usuario_id,contenido)
       VALUES($id,'$texto')");
}

$res=mysqli_query($db,
"SELECT p.*,u.usuario 
 FROM publicaciones p
 JOIN usuarios u ON u.id=p.usuario_id
 ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<script src="efectos.js"></script>
</head>
<body>

<h2>Publicaciones</h2>

<form method="POST">
<textarea name="texto"></textarea>
<button name="publicar">Publicar</button>
</form>

<?php while($row=mysqli_fetch_assoc($res)): ?>
<p>
<b><?=$row['usuario']?>:</b>
<?=$row['contenido']?>
</p>
<?php endwhile; ?>

</body>
</html>
