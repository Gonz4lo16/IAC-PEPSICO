<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // tu usuario
$password = "";     // tu contraseña
$dbname = "tu_base"; // cambia por el nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta de prueba
$result = $conn->query("SELECT id, usuario, direccion, rol FROM clientes LIMIT 5");

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "ID: " . $row['id'] . " | Usuario: " . $row['usuario'] . " | Dirección: " . $row['direccion'] . " | Rol: " . $row['rol'] . "<br>";
    }
} else {
    echo "No hay clientes en la base de datos";
}

$conn->close();
?>
