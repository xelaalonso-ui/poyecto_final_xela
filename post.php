<?php
require "includes/comprobar_sesion.php";
require "includes/db_connection.php";

if(isset($_POST['texto'])){
 $texto=$_POST['texto'];
 $id=$_SESSION['id'];

 $stmt=mysqli_prepare($conn,
 "INSERT INTO Fotos(id_usuario,url_foto)
 VALUES(?,?)");
 mysqli_stmt_bind_param($stmt,"is",$id,$texto);
 mysqli_stmt_execute($stmt);
}

$res=mysqli_query($conn,
"SELECT f.*,u.username
 FROM Fotos f
 JOIN Usuario u ON u.id_usuario=f.id_usuario
 ORDER BY id_foto DESC");
?>