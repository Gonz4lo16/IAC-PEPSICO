<?php
session_start();
require 'db.php';
if($_SESSION['rol']!="admin") exit();

if(isset($_POST['id'])){
    $id = $_POST['id'];
    $producto = $conn->query("SELECT * FROM productos WHERE id=$id")->fetch_assoc();
    $nuevoNombre = $_POST['nombre'] ?? $producto['nombre'];
    $nuevoPrecio = $_POST['precio'] ?? $producto['precio'];

    if(isset($_POST['nombre'], $_POST['precio'])){
        $stmt = $conn->prepare("UPDATE productos SET nombre=?, precio=? WHERE id=?");
        $stmt->bind_param("sdi", $_POST['nombre'], $_POST['precio'], $id);
        $stmt->execute();
        header("Location: index.php");
        exit();
    }
}
?>

<form method="POST">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="text" name="nombre" value="<?= $producto['nombre'] ?>" required>
    <input type="number" name="precio" value="<?= $producto['precio'] ?>" required>
    <button type="submit">Guardar Cambios</button>
</form>
