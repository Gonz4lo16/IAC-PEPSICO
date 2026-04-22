<?php
session_start();
require 'db.php';

// Solo admins
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin'){
    header("Location: login.php");
    exit();
}

if(isset($_POST['id']) && isset($_POST['estado'])){
    $id = intval($_POST['id']);
    $estado = $_POST['estado'];

    // Seguridad: Solo permitir pendiente / entregado
    if($estado != "pendiente" && $estado != "entregado"){
        die("Estado no permitido.");
    }

    $query = "UPDATE pedidos SET estado = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $estado, $id);

    if($stmt->execute()){
        header("Location: ver_pedidos.php?msg=ok");
    } else {
        header("Location: ver_pedidos.php?msg=error");
    }
    exit();
}

header("Location: ver_pedidos.php");
?>
