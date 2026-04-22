<?php
session_start();
require 'db.php';

if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin'){
    header("Location: index.php");
    exit();
}

if(isset($_POST['usuario'], $_POST['clave'], $_POST['direccion'])){

    $usuario = trim($_POST['usuario']);
    $direccion = trim($_POST['direccion']);
    $clave = password_hash($_POST['clave'], PASSWORD_BCRYPT);
    $rol = 'cliente';

    // Validación de duplicidad
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario=?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        header("Location: index.php?msg=usuario_existente");
        exit();
    }
    $stmt->close();

    // Insertar cliente con dirección
    $stmt = $conn->prepare("
        INSERT INTO usuarios (usuario, direccion, clave, rol)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $usuario, $direccion, $clave, $rol);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php?msg=usuario_creado");
    exit();

} else {
    header("Location: index.php");
    exit();
}
?>
