<?php
$host     = "localhost:3306";
$user     = "root";
$password = "";
$database = "red_social";

$db = mysqli_connect($host, $user, $password, $database);

if (mysqli_connect_errno()) {
    die("Conexión fallida: " . mysqli_connect_error());
}

mysqli_set_charset($db, "utf8");
?>
