<?php
require_once "db_connection.php";

$id=$_GET['id'];

mysqli_query($db,"DELETE FROM usuarios WHERE id=$id");

header("Location: ../lista_usuarios.php");
?>
