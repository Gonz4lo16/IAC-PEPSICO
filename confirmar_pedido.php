<?php
session_start();
require 'db.php';

if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente'){
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$carrito = $_SESSION['carrito'];

if(empty($carrito)){
    header("Location: index.php");
    exit();
}

// 🔹 Crear pedido principal
$stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total) VALUES (?, 0)");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$pedido_id = $stmt->insert_id;
$stmt->close();

$total_pedido = 0;

// 🔹 Insertar cada producto del carrito en detalle_pedido
foreach($carrito as $item){
    $producto_nombre = $item['producto'];
    $precio_unitario = floatval($item['precio']);
    $cantidad = intval($item['cantidad']);
    $subtotal = $precio_unitario * $cantidad;
    $total_pedido += $subtotal;

    // Obtener ID del producto por nombre
    $stmt = $conn->prepare("SELECT id FROM productos WHERE nombre = ?");
    $stmt->bind_param("s", $producto_nombre);
    $stmt->execute();
    $stmt->bind_result($producto_id);
    $stmt->fetch();
    $stmt->close();

    if($producto_id){
        $stmt = $conn->prepare("
            INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiid", $pedido_id, $producto_id, $cantidad, $precio_unitario);
        $stmt->execute();
        $stmt->close();
    }
}

// 🔹 Actualizar total del pedido
$stmt = $conn->prepare("UPDATE pedidos SET total = ? WHERE id = ?");
$stmt->bind_param("di", $total_pedido, $pedido_id);
$stmt->execute();
$stmt->close();

// 🔹 Vaciar carrito
unset($_SESSION['carrito']);

// 🔹 Redirigir con mensaje de éxito
header("Location: index.php?msg=pedido_ok");
exit();
?>
