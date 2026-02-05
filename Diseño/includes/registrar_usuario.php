<?php
require_once "db_connection.php";
session_start();

if(isset($_POST['registro'])){

    $usuario=$_POST['usuario_reg'];
    $correo=$_POST['correo'];
    $pass=password_hash($_POST['contrasena_reg'],PASSWORD_DEFAULT);

    $foto=NULL;

    if(!empty($_FILES['foto_usuario']['name'])){
        $foto="../uploads/".time().$_FILES['foto_usuario']['name'];
        move_uploaded_file($_FILES['foto_usuario']['tmp_name'],$foto);
    }

    $sql="INSERT INTO usuarios(usuario,correo,contrasena,foto)
          VALUES(?,?,?,?)";

    $stmt=mysqli_prepare($db,$sql);
    mysqli_stmt_bind_param($stmt,"ssss",$usuario,$correo,$pass,$foto);
    mysqli_stmt_execute($stmt);

    $_SESSION['success']="Usuario registrado";
    header("Location: ../index.php");
}
?>
