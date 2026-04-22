<?php
session_start();
require 'db.php';

if($_SESSION['rol']!="admin") exit();

if(isset($_POST['nombre'], $_POST['precio'])){
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $img = $_POST['img'] ?: "https://via.placeholder.com/400x300?text=SinImagen";

    $stmt = $conn->prepare("INSERT INTO productos (nombre, precio, imagen) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $nombre, $precio, $img);
    $stmt->execute();
}
header("Location: index.php");
?>
