<?php
require 'db.php';

$id = $_POST['id'] ?? '';
$usuario = $_POST['usuario'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$clave = $_POST['clave'] ?? ''; // opcional

// Validar campos obligatorios
if(empty($id) || empty($usuario) || empty($direccion)){
    die("Datos incompletos"); // solo verifica los campos que realmente existen
}

// Rol fijo como cliente
$rol = 'cliente';

// Preparar la consulta
if(!empty($clave)){
    $claveHash = password_hash($clave, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET usuario=?, direccion=?, rol=?, clave=? WHERE id=?");
    $stmt->bind_param("ssssi", $usuario, $direccion, $rol, $claveHash, $id);
} else {
    $stmt = $conn->prepare("UPDATE usuarios SET usuario=?, direccion=?, rol=? WHERE id=?");
    $stmt->bind_param("sssi", $usuario, $direccion, $rol, $id);
}

if($stmt->execute()){
    echo "Cliente actualizado correctamente";
} else {
    echo "Error al actualizar cliente: " . $stmt->error;
}
?>
