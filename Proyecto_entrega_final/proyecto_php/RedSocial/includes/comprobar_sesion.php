<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
session_regenerate_id(true);
?>
