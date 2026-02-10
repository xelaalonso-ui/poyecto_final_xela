<?php
require_once "includes/comprobar_sesion.php";

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: index.php");
    exit;
}
?>
