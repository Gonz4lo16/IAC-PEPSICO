<?php
require 'db.php';

$q = $_GET['q'] ?? '';
if(empty($q)){
    echo json_encode([]);
    exit;
}

// Buscamos en la tabla usuarios donde rol = 'cliente'
$stmt = $conn->prepare("SELECT id, usuario, direccion FROM usuarios WHERE rol='cliente' AND (usuario LIKE ? OR id LIKE ?) LIMIT 5");
$like = "%$q%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$result = $stmt->get_result();

$clientes = [];
while($row = $result->fetch_assoc()){
    $clientes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($clientes);
?>
