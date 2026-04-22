<?php
session_start();

$index = $_POST['index'];
$accion = $_POST['accion'];

if(isset($_SESSION['carrito'][$index])){
    if($accion == 'mas'){
        $_SESSION['carrito'][$index]['cantidad']++;
    } elseif($accion == 'menos'){
        $_SESSION['carrito'][$index]['cantidad']--;
        if($_SESSION['carrito'][$index]['cantidad'] <= 0){
            array_splice($_SESSION['carrito'], $index, 1);
        }
    }
}

header("Location: index.php");
exit();
?>
