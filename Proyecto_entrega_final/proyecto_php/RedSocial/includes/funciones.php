<?php
require_once __DIR__ . '/db_connection.php';

function mostrarUsuarios() {
    global $db;
    $sql = "SELECT u.id_usuario, u.username, u.email, u.estado_cuenta, u.fecha_registro,
                   d.nombre, d.apellido, d.fecha_nacimiento,
                   f.url_foto
            FROM Usuario u
            LEFT JOIN Datos_personales d ON u.id_usuario = d.id_usuario
            LEFT JOIN Fotos f ON f.id_foto = (
                SELECT id_foto FROM Fotos f2
                WHERE f2.id_usuario = u.id_usuario AND f2.tipo_foto = 'perfil'
                ORDER BY f2.fecha_subida DESC LIMIT 1
            )
            ORDER BY u.id_usuario DESC";
    $res = mysqli_query($db, $sql);
    if (!$res) return [];
    $lista = [];
    while ($row = mysqli_fetch_assoc($res)) $lista[] = $row;
    return $lista;
}

function eliminarUsuario($id) {
    global $db;
    $id = intval($id);
    $stmt = mysqli_prepare($db, "DELETE FROM Usuario WHERE id_usuario = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    return mysqli_stmt_execute($stmt);
}

function limpiar($dato) {
    return htmlspecialchars(trim($dato));
}
?>
