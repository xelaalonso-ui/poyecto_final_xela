<?php
session_start();
require "db_connection.php";

$usuario = $_POST['usuario'];
$password = $_POST['password'];

$stmt = mysqli_prepare(
    $conn,
    "SELECT id_usuario,password_hash FROM Usuario WHERE username=?"
);
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['usuario'] = $usuario;
    $_SESSION['id'] = $user['id_usuario'];
    header("Location:../p_principla.php");
    exit();
}
header("Location:../index.php?error=1");
