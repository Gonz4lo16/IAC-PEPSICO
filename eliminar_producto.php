<?php
session_start();
require 'db.php';
if($_SESSION['rol']!="admin") exit();
if(isset($_POST['id'])){
    $id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE productos SET activo = 0 WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
header("Location: index.php");
?>
