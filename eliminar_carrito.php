<?php
session_start();

$index = $_POST['index'];
if(isset($_SESSION['carrito'][$index])){
    array_splice($_SESSION['carrito'], $index, 1);
}
header("Location: index.php");
exit();
?>
