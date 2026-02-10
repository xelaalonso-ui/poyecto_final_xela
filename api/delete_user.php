<?php
require "../includes/db_connection.php";
$id=$_GET['id'];
mysqli_query($conn,
"DELETE FROM Usuario WHERE id_usuario=$id");
header("Location:listar_usuarios.php");
?>
