<?php

function limpiar($dato){
    return htmlspecialchars(trim($dato));
}

function estaLogueado(){
    return isset($_SESSION['usuario']);
}

?>
