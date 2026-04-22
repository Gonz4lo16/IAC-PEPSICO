<?php
require 'db.php';

if (!isset($_GET['id'])) exit("ID no válido");

$id = intval($_GET['id']);

$sql = "SELECT p.nombre, d.cantidad, d.subtotal
        FROM detalle_pedido d
        JOIN productos p ON d.producto_id = p.id
        WHERE d.pedido_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<em>No hay productos en este pedido.</em>";
    exit;
}

echo "<table style='width:100%; border-collapse: collapse; text-align: left;'>
        <tr style='background:#e9f3ff; font-weight:bold;'>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
        </tr>";

while($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['nombre']}</td>
            <td>{$row['cantidad']}</td>
            <td>S/ " . number_format($row['subtotal'], 2) . "</td>
          </tr>";
}

echo "</table>";
