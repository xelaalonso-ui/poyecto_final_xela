<?php
header("Content-Type: application/json");
require "../includes/db_connection.php";

$res=mysqli_query($conn,
"SELECT * FROM Fotos ORDER BY id_foto DESC");
$out=[];
while($r=mysqli_fetch_assoc($res)) $out[]=$r;
echo json_encode($out);
?>