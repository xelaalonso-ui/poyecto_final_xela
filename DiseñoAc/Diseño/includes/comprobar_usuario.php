<?php
require_once "db_connection.php";
session_start();

if(isset($_POST['login'])){

    $usuario=$_POST['usuario'];
    $pass=$_POST['contrasena'];

    $sql="SELECT * FROM usuarios WHERE usuario=?";
    $stmt=mysqli_prepare($db,$sql);
    mysqli_stmt_bind_param($stmt,"s",$usuario);
    mysqli_stmt_execute($stmt);

    $res=mysqli_stmt_get_result($stmt);
    $user=mysqli_fetch_assoc($res);

    if($user && password_verify($pass,$user['contrasena'])){
        $_SESSION['usuario']=$user['usuario'];
        $_SESSION['id']=$user['id'];
        header("Location: ../p_principla.php");
    }else{
        $_SESSION['error']="Login incorrecto";
        header("Location: ../index.php");
    }
}
?>
