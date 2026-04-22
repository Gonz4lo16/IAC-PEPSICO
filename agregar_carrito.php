<?php
session_start();

$nombre = $_POST['nombre'];
$precio = $_POST['precio'];
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

if(!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

$existe = false;
foreach($_SESSION['carrito'] as &$item){
    if($item['producto'] == $nombre){
        $item['cantidad'] += $cantidad;
        $existe = true;
        break;
    }
}
if(!$existe){
    $_SESSION['carrito'][] = [
        'producto' => $nombre,
        'precio' => $precio,
        'cantidad' => $cantidad
    ];
}

header("Location: index.php");
exit();
?>
