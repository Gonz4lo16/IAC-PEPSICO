<?php
$host = "localhost";
$user = "root";
$pass = ""; // vacío por defecto en XAMPP
$db   = "ventas_pepsico";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Error en la conexión: " . $conn->connect_error);
}
?>
