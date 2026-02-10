<?php
require_once "db_connection.php";
session_start();

$id=$_SESSION['id'];

if(isset($_POST['editar'])){

    $usuario=$_POST['usuario'];
    $correo=$_POST['correo'];

    $sql="UPDATE usuarios SET usuario=?,correo=? WHERE id=?";
    $stmt=mysqli_prepare($db,$sql);

    mysqli_stmt_bind_param($stmt,"ssi",$usuario,$correo,$id);
    mysqli_stmt_execute($stmt);

    $_SESSION['usuario']=$usuario;

    header("Location: ../perfil.php");
}
?>
