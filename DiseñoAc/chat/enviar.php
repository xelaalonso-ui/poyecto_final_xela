<?php
session_start();
require "../includes/db_connection.php";

$msg=$_POST['mensaje'];
$id=$_SESSION['id'];

$stmt=mysqli_prepare($conn,
"INSERT INTO Actividad(id_usuario,descripcion)
 VALUES(?,?)");
mysqli_stmt_bind_param($stmt,"is",$id,$msg);
mysqli_stmt_execute($stmt);

header("Location:chat.php");
?>