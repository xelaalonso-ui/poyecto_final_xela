<?php
$host = "localhost:3306"; // Nombre del host (servidor de la base de datos)
$user = "root"; // Nombre del usuario
$password = ""; // Contraseña del usuario
$database = "red_social"; // Nombre de la Base de Datos

// Conectarse a la Base de Datos 
$db = mysqli_connect($host, $user, $password, $database);

// Verificar la conexión
if (mysqli_connect_errno()) {
    printf("Conexión fallida: %s\n", mysqli_connect_error());
    exit();
}

// Establecer el conjunto de caracteres
mysqli_set_charset($db, "utf8");

?>