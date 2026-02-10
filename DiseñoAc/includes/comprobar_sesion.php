<?php
session_start();
if(!isset($_SESSION['usuario'])){
 header("Location:../index.php");
 exit();
}
session_regenerate_id(true);
?>