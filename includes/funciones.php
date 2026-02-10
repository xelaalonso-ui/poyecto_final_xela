<?php

function limpiar($dato){
    return htmlspecialchars(trim($dato));
}

function estaLogueado(){
    return isset($_SESSION['usuario']);
}

require_once 'db_connection.php';

function mostrarUsuarios() {
    global $db;
    
    $sql = "SELECT * FROM Usuario ORDER BY id_usuario DESC";
    $resultado = mysqli_query($db, $sql);
    
    if(!$resultado) {
        die("Error en la consulta: " . mysqli_error($db));
    }
    
    $usuarios = array();
    while($fila = mysqli_fetch_assoc($resultado)) {
        $usuarios[] = $fila;
    }
    
    return $usuarios;
}
?>

