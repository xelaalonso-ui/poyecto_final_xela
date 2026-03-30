<?php
session_start();
require_once "../includes/db_connection.php";

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    exit;
}

$msg = trim($_POST['mensaje'] ?? '');
$id  = (int)$_SESSION['id'];

if (!empty($msg)) {
    $stmt = mysqli_prepare($db,
        "INSERT INTO Actividad (id_usuario, tipo_actividad, descripcion) VALUES (?, 'mensaje', ?)"
    );
    mysqli_stmt_bind_param($stmt, 'is', $id, $msg);
    mysqli_stmt_execute($stmt);
}

echo json_encode(['ok' => true]);
?>
