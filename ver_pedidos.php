<?php
session_start();
require 'db.php';

// Solo admins pueden acceder
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Obtener pedidos
$query = "
SELECT p.id, u.usuario AS cliente, u.direccion, p.fecha, p.total, p.estado
FROM pedidos p
JOIN usuarios u ON p.usuario_id = u.id
ORDER BY p.fecha DESC
";

$result = $conn->query($query);
$pedidos = $result->fetch_all(MYSQLI_ASSOC);

// Obtener los detalles de todos los pedidos
$detalles = [];
$detQuery = "
SELECT d.pedido_id, pr.nombre AS producto, d.cantidad, d.precio
FROM detalle_pedido d
JOIN productos pr ON d.producto_id = pr.id
";
$detResult = $conn->query($detQuery);
while($row = $detResult->fetch_assoc()){
    $detalles[$row['pedido_id']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Pedidos</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #eef2f7;
  margin: 0;
}

/* Encabezado */
header {
  background: #003366;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 35px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}
header h1 {
  font-size: 1.6rem;
  margin: 0;
}
header a {
  background: #e67e22;
  color: white;
  padding: 10px 16px;
  text-decoration: none;
  border-radius: 8px;
  font-weight: bold;
  transition: 0.3s;
}
header a:hover {
  background: #d35400;
}

/* Tabla de pedidos */
.container {
  width: 90%;
  margin: 40px auto;
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 14px;
  text-align: center;
  border-bottom: 1px solid #ddd;
}
th {
  background: #003366;
  color: white;
  font-weight: 600;
}
tr:hover {
  background: #f8faff;
}

/* Botón ver detalles */
.btn-detalle {
  background: #3498db;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}
.btn-detalle:hover {
  background: #2980b9;
}

/* Sección de detalles (oculta inicialmente) */
.detalles {
  display: none;
  background: #f9fbfd;
  border-top: 1px solid #ccc;
  padding: 10px 20px;
  text-align: left;
}
.detalles table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 8px;
}
.detalles th {
  background: #f0f0f0;
  color: #333;
}
.detalles td, .detalles th {
  padding: 8px;
  border-bottom: 1px solid #ddd;
}
</style>
</head>
<body>

<header>
  <h1>📦 Pedidos Registrados</h1>
  <a href="index.php">⬅ Volver al Panel</a>
</header>

<div class="container">
  <table>
    <tr>
      <th>ID Pedido</th>
      <th>Cliente</th>
      <th>Dirección</th>
      <th>Fecha</th>
      <th>Total</th>
      <th>Estado</th>
      <th>Acción</th>
    </tr>

    <?php if(count($pedidos) > 0): ?>
      <?php foreach($pedidos as $p): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><?= htmlspecialchars($p['cliente']) ?></td>
          <td><?= htmlspecialchars($p['direccion']) ?></td>
          <td><?= $p['fecha'] ?></td>
          <td>S/<?= number_format($p['total'], 2) ?></td>
          <td>
    <?php if ($p['estado'] == 'entregado'): ?>
        <span style="color: green; font-weight: bold;">Entregado ✔</span>
    <?php else: ?>
        <form action="actualizar_estado.php" method="POST" style="display:flex; gap:5px; justify-content:center;">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <select name="estado">
                <option value="pendiente" selected>Pendiente</option>
                <option value="entregado">Entregado</option>
            </select>
            <button type="submit" style="
                background:#27ae60;
                color:white;
                border:none;
                padding:4px 8px;
                border-radius:6px;
                cursor:pointer;
            ">✔</button>
        </form>
    <?php endif; ?>
</td>

          <td><button class="btn-detalle" onclick="toggleDetalles(<?= $p['id'] ?>)">Ver detalles</button></td>
        </tr>

        <tr id="detalles-<?= $p['id'] ?>" class="detalles">
          <td colspan="6">
            <?php if(isset($detalles[$p['id']])): ?>
              <table>
                <tr>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Precio Unitario</th>
                  <th>Subtotal</th>
                </tr>
                <?php foreach($detalles[$p['id']] as $d): ?>
                  <tr>
                    <td><?= htmlspecialchars($d['producto']) ?></td>
                    <td><?= $d['cantidad'] ?></td>
                    <td>S/<?= number_format($d['precio'], 2) ?></td>
                    <td>S/<?= number_format($d['cantidad'] * $d['precio'], 2) ?></td>
                  </tr>
                <?php endforeach; ?>
              </table>
            <?php else: ?>
              <p>No hay detalles disponibles para este pedido.</p>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="6">No hay pedidos registrados.</td></tr>
    <?php endif; ?>
  </table>
</div>

<script>
function toggleDetalles(id) {
  const fila = document.getElementById('detalles-' + id);
  fila.style.display = fila.style.display === 'table-row' ? 'none' : 'table-row';
}
</script>

</body>
</html>
