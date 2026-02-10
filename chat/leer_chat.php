<?php
require "../includes/db_connection.php";

$res=mysqli_query($conn,
"SELECT a.descripcion,u.username
 FROM Actividad a
 JOIN Usuario u ON u.id_usuario=a.id_usuario
 ORDER BY id_actividad DESC");

while($r=mysqli_fetch_assoc($res)){
 echo "<b>{$r['username']}</b>: {$r['descripcion']}<br>";
}
?>