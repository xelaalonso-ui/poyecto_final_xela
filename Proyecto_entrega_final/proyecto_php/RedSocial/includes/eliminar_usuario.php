<?php
session_start();
require_once "db_connection.php";

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = mysqli_prepare($db, "DELETE FROM Usuario WHERE id_usuario = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}

header("Location: ../lista_usuarios.php");
exit;
?>
