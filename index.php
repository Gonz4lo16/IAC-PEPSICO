<?php
session_start();
require 'db.php';

if(!isset($_SESSION['rol'])){
    header("Location: login.php");
    exit();
}
$rol = $_SESSION['rol'];

// Traer productos de la DB
$productos = [];
$result = $conn->query("SELECT * FROM productos WHERE activo = 1");
while($row = $result->fetch_assoc()){
    $productos[] = $row;
}

// Carrito en sesión
if(!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];
$carrito = $_SESSION['carrito'];

// Calcular total
$total = 0;
foreach($carrito as $item){
    $total += $item['precio'] * $item['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PepsiCo Ventas</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<script src="app.js" defer></script>

<style>
body {
  background: #f3f5f9;
  font-family: 'Poppins', sans-serif;
  margin: 0;
}

/* Header */
header {
  background: #003366;
  color: white;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
header h1 {
  margin: 0;
}
header .btn {
  background: #e67e22;
  color: white;
  padding: 8px 14px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  transition: 0.3s;
}
header .btn:hover {
  background: #d35400;
}

header .btn-logout {
  background: #e74c3c;
  color: white;
  padding: 8px 14px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  transition: 0.3s;
}
header .btn-logout:hover {
  background: #e74c3c;
}


/* Mensaje */
.mensaje-exito {
  background: #27ae60;
  color: white;
  text-align: center;
  padding: 10px;
  font-weight: 600;
}

/* Panel admin */
#admin-panel {
  background: #fff;
  padding: 20px;
  width: 90%;
  margin: 20px auto;
  border-radius: 10px;
  box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}
#admin-panel h2 {
  color: #003366;
}
#admin-panel input, #admin-panel button {
  padding: 8px;
  margin: 5px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

/* Productos */
.productos {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 25px;
  padding: 20px;
}
.card {
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 6px 16px rgba(0,0,0,0.1);
  width: 230px;
  padding: 15px;
  text-align: center;
  transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}
.card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 10px;
}
.card h3 {
  margin: 10px 0 5px;
  color: #333;
}
.card p {
  color: #27ae60;
  font-weight: 600;
  margin-bottom: 10px;
}

/* Cantidad */
.cantidad-selector {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 12px;
}
.cantidad-selector button {
  background: #e0e0e0;
  border: none;
  padding: 6px 10px;
  font-size: 18px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s;
}
.cantidad-selector button:hover {
  background: #ccc;
}
.cantidad-selector input {
  width: 45px;
  text-align: center;
  border: 1px solid #ddd;
  margin: 0 5px;
  border-radius: 8px;
  padding: 4px;
}

/* Botones */
.add {
  background: linear-gradient(90deg, #2ecc71, #27ae60);
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.95rem;
  transition: background 0.3s;
}
.add:hover {
  background: linear-gradient(90deg, #27ae60, #1e8449);
}
.edit, .delete {
  background: #3498db;
  color: white;
  border: none;
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}
.delete {
  background: #e74c3c;
}
.edit:hover {
  background: #2980b9;
}
.delete:hover {
  background: #c0392b;
}

/* -------------------- CARRITO ELEGANTE -------------------- */
.carrito-elegante {
  position: fixed;
  right: 0;
  top: 0;
  width: 340px;
  height: 100vh;
  background: #ffffff;
  box-shadow: -4px 0 15px rgba(0,0,0,0.15);
  padding: 25px;
  border-radius: 20px 0 0 20px;
  display: none;
  overflow-y: auto;
  animation: slideIn 0.3s ease-in-out;
}
@keyframes slideIn {
  from { transform: translateX(100%); }
  to { transform: translateX(0); }
}
.carrito-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.cerrar-carrito {
  background: transparent;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #888;
  transition: color 0.2s;
}
.cerrar-carrito:hover { color: #000; }
.carrito-item {
  background: #f8f9fc;
  border-radius: 10px;
  padding: 10px 12px;
  margin-bottom: 12px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  transition: transform 0.2s;
}
.carrito-item:hover { transform: scale(1.02); }
.item-info {
  font-size: 14px;
  color: #333;
}
.item-info strong {
  font-size: 15px;
  color: #003366;
}
.subtotal {
  color: #27ae60;
  font-weight: 600;
  margin: 5px 0 0;
}
.acciones {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 6px;
}
.cantidad-form {
  display: flex;
  align-items: center;
  gap: 5px;
}
.btn-control {
  background: #e0e0e0;
  border: none;
  padding: 4px 10px;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-control:hover { background: #ccc; }
.btn-delete {
  background: #e74c3c;
  border: none;
  color: white;
  padding: 4px 10px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-delete:hover { background: #c0392b; }

.carrito-total {
  text-align: center;
  border-top: 2px solid #eee;
  margin-top: 20px;
  padding-top: 10px;
}
.carrito-total span {
  color: #27ae60;
  font-weight: bold;
}
.btn-confirmar-elegante {
  background: linear-gradient(90deg, #2ecc71, #27ae60);
  color: white;
  border: none;
  padding: 12px 15px;
  width: 100%;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  font-size: 1rem;
  margin-top: 15px;
  transition: transform 0.2s, background 0.3s;
}
.btn-confirmar-elegante:hover {
  background: linear-gradient(90deg, #27ae60, #1e8449);
  transform: scale(1.03);
}

/* Botón flotante carrito */
#carrito-toggle {
  position: fixed;
  right: 25px;
  bottom: 25px;
  background: linear-gradient(135deg, #2ecc71, #27ae60);
  color: white;
  font-size: 26px;
  padding: 16px;
  border-radius: 50%;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  transition: transform 0.2s, background 0.3s;
}
#carrito-toggle:hover {
  transform: scale(1.1);
  background: linear-gradient(135deg, #27ae60, #1e8449);
}

/* ================= MODALES =================== */
.modal {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: white;
  padding: 25px;
  width: 350px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.3);
  animation: fadeIn 0.2s ease-in-out;
}

.modal-content h2 {
  margin-top: 0;
  color: #003366;
}

.modal-content input {
  width: 100%;
  margin-bottom: 12px;
  padding: 8px;
  border-radius: 8px;
  border: 1px solid #ccc;
}

.btn-modal {
  width: 100%;
  padding: 10px;
  background: #27ae60;
  color: white;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: bold;
  transition: 0.2s;
}

.btn-modal:hover {
  background: #1e8449;
}

.close {
  float: right;
  font-size: 22px;
  cursor: pointer;
}

/* Contenedor del panel */
#admin-panel {
    max-width: 450px;
    margin: 0 auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.12);
}

/* Titulos */
#admin-panel h2, 
#admin-panel h3 {
    text-align: center;
    margin-bottom: 15px;
    font-weight: 700;
    color: #2b2b2b;
}

/* Inputs */
#admin-panel input {
    width: 100%;
    padding: 12px;
    margin-bottom: 12px;
    border-radius: 8px;
    border: 1px solid #dcdcdc;
    font-size: 15px;
    transition: .2s;
}

#admin-panel input:focus {
    border-color: #5a8dee;
    box-shadow: 0 0 6px rgba(90,141,238,0.4);
}

/* ====== BOTÓN PRINCIPAL DEL PANEL (btn-panel) ====== */
.btn-panel {
    display: block;
    width: 100%;
    background: #5a8dee;        /* Azul moderno */
    color: white;
    padding: 12px 18px;
    border-radius: 10px;
    border: none;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.25s ease;
    margin-bottom: 15px;
    box-shadow: 0 4px 12px rgba(90, 141, 238, 0.3);
}

.btn-panel:hover {
    background: #3d6ed4;
    transform: translateY(-2px);
}


</style>
</head>
<body>

<header>
  <h1>PepsiCo - Ventas</h1>

  <?php if($rol == 'admin'): ?>
    <a href="ver_pedidos.php" class="btn">📦 Pedidos</a>
  <?php endif; ?>

  <a href="logout.php" class="btn btn-logout">Cerrar Sesión</a>
</header>

<!-- ✅ MENSAJE DE CONFIRMACIÓN -->
<?php if(isset($_GET['msg'])): ?>
  <div class="mensaje-exito">
    <?php
      if($_GET['msg'] == 'pedido_ok') echo "✅ ¡Tu pedido fue confirmado exitosamente!";
      if($_GET['msg'] == 'usuario_creado') echo "✅ ¡Cliente creado correctamente!";
      if($_GET['msg'] == 'usuario_existente') echo "⚠️ El nombre de usuario ya existe.";
    ?>
  </div>
<?php endif; ?>

<!-- PANEL ADMIN -->
<section id="admin-panel" style="<?= $rol=='admin'?'display:block;':'display:none;' ?>">
  <h2>Panel de Admin</h2>

  <!-- Botón para abrir modal de productos -->
  <button class="btn-panel" onclick="abrirModal('modalProducto')">
    ➕ Añadir Producto
  </button>

  <div class="admin-separator"></div>

  <!-- Botón para abrir modal de clientes -->
  <button class="btn-panel" onclick="abrirModal('modalCliente')">
    👤 Crear Cliente
  </button>
  <!-- Botón para abrir modal de editar -->
  <button class="btn-panel" onclick="abrirModal('modalEditarCliente')">
  ✏️ Editar Cliente
</button>
</section>



<!-- LISTA DE PRODUCTOS -->
<section class="productos" id="lista-productos">
<?php foreach($productos as $p): ?>
  <div class="card">
    <img src="<?= $p['imagen'] ?>" alt="<?= $p['nombre'] ?>">
    <h3><?= $p['nombre'] ?></h3>
    <p>S/ <?= number_format($p['precio'],2) ?></p>

    <?php if($rol=='cliente'): ?>
      <form method="POST" action="agregar_carrito.php">
        <input type="hidden" name="nombre" value="<?= $p['nombre'] ?>">
        <input type="hidden" name="precio" value="<?= $p['precio'] ?>">
        <div class="cantidad-selector">
          <button type="button" onclick="cambiarCantidad(this, -1)">−</button>
          <input type="number" name="cantidad" value="1" min="1">
          <button type="button" onclick="cambiarCantidad(this, 1)">+</button>
        </div>
        <button type="submit" class="add">🛒 Añadir al carrito</button>
      </form>
    <?php endif; ?>

    <?php if($rol=='admin'): ?>
      <form method="POST" action="editar_producto.php" style="display:inline-block;">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        <button class="edit">Editar</button>
      </form>
      <form method="POST" action="eliminar_producto.php" style="display:inline-block;">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        <button class="delete">Eliminar</button>
      </form>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</section>

<!-- NUEVO PANEL DE CARRITO -->
<div id="carrito-toggle" onclick="toggleCarrito()" style="<?= $rol=='cliente'?'display:block;':'display:none;' ?>">
  <span style="font-size: 22px;">🛒</span>
</div>

<div id="carrito-panel" class="carrito-elegante">
  <div class="carrito-header">
    <h2>🧺 Tu Carrito</h2>
    <button onclick="toggleCarrito()" class="cerrar-carrito">✕</button>
  </div>
  <hr>

  <ul id="lista-carrito">
    <?php foreach($carrito as $i => $item): ?>
      <li class="carrito-item">
        <div class="item-info">
          <strong><?= htmlspecialchars($item['producto']) ?></strong>
          <span>S/ <?= number_format($item['precio'], 2) ?> x <?= $item['cantidad'] ?></span>
          <p class="subtotal">Subtotal: <strong>S/ <?= number_format($item['precio'] * $item['cantidad'], 2) ?></strong></p>
        </div>

        <div class="acciones">
          <form method="POST" action="actualizar_carrito.php" class="cantidad-form">
            <input type="hidden" name="index" value="<?= $i ?>">
            <button type="submit" name="accion" value="menos" class="btn-control">−</button>
            <button type="submit" name="accion" value="mas" class="btn-control">+</button>
          </form>

          <form method="POST" action="eliminar_carrito.php" class="delete-form">
            <input type="hidden" name="index" value="<?= $i ?>">
            <button type="submit" class="btn-delete">❌</button>
          </form>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <div class="carrito-total">
    <h3>Total: <span>S/ <?= number_format($total,2) ?></span></h3>
  </div>

  <form method="POST" action="confirmar_pedido.php">
    <button type="submit" class="btn-confirmar-elegante">✅ Confirmar Pedido</button>
  </form>
</div>

<script>
function cambiarCantidad(btn, delta) {
  const input = btn.parentNode.querySelector("input[type=number]");
  let val = parseInt(input.value) + delta;
  if(val < 1) val = 1;
  input.value = val;
}
function toggleCarrito(){
  const panel = document.getElementById('carrito-panel');
  panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
}
setTimeout(() => {
  const msg = document.querySelector('.mensaje-exito');
  if(msg) msg.style.display = 'none';
}, 3000);

function abrirModal(id) {
  document.getElementById(id).style.display = "flex";
}

function cerrarModal(id) {
  document.getElementById(id).style.display = "none";
}

function buscarCliente(){
  let query = document.getElementById('buscarCliente').value.trim();
  if(query.length < 1){
    document.getElementById('listaClientes').innerHTML = '';
    document.getElementById('formEditarCliente').style.display = 'none';
    return;
  }

  fetch('buscar_cliente.php?q=' + encodeURIComponent(query))
    .then(res => res.json())
    .then(data => {
      if(data.length === 0){
        document.getElementById('listaClientes').innerHTML = '<div>No se encontró ningún cliente</div>';
        return;
      }
      let html = '';
      data.forEach(cliente => {
        html += `<div style="padding:5px; cursor:pointer; border-bottom:1px solid #eee;" onclick="seleccionarCliente(${cliente.id}, '${cliente.usuario}', '${cliente.direccion}')">
                   ${cliente.id} - ${cliente.usuario} - ${cliente.direccion}
                 </div>`;
      });
      document.getElementById('listaClientes').innerHTML = html;
    })
    .catch(err => console.log('Error:', err));
}

// Seleccionar cliente
function seleccionarCliente(id, usuario, direccion){
  document.getElementById('editarId').value = id;
  document.getElementById('editarUsuario').value = usuario;
  document.getElementById('editarDireccion').value = direccion;
  document.getElementById('formEditarCliente').style.display = 'block';
  document.getElementById('listaClientes').innerHTML = '';
  document.getElementById('buscarCliente').value = usuario;
}

// Enviar formulario via fetch
const formEditar = document.getElementById('formEditarCliente');
const mensajeEditar = document.getElementById('mensajeEditar');

formEditar.addEventListener('submit', function(e){
  e.preventDefault();
  let formData = new FormData(formEditar);

  fetch('editar_cliente.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    mensajeEditar.innerText = data;
    mensajeEditar.style.color = data.includes("correctamente") ? "green" : "red";
  })
  .catch(err => {
    mensajeEditar.innerText = "Error al actualizar cliente";
    mensajeEditar.style.color = "red";
    console.error(err);
  });
});
</script>

<!-- ===================== MODAL AGREGAR PRODUCTO ===================== -->
<div id="modalProducto" class="modal">
  <div class="modal-content">
    <span class="close" onclick="cerrarModal('modalProducto')">&times;</span>
    <h2>Agregar Producto</h2>

    <form method="POST" action="agregar_producto.php">
      <label>Nombre:</label>
      <input type="text" name="nombre" required>

      <label>Precio (S/):</label>
      <input type="number" name="precio" required>

      <label>URL de Imagen:</label>
      <input type="text" name="img">

      <button type="submit" class="btn-modal">Guardar</button>
    </form>
  </div>
</div>

<!-- ===================== MODAL CREAR CLIENTE ===================== -->
<div id="modalCliente" class="modal">
  <div class="modal-content">
    <span class="close" onclick="cerrarModal('modalCliente')">&times;</span>
    <h2>Crear Cliente</h2>

    <form method="POST" action="crear_usuario.php">

      <label>Usuario:</label>
      <input type="text" name="usuario" required>

      <label>Dirección:</label>
      <input type="text" name="direccion" required placeholder="Ej: Jr. Lima 123">

      <label>Contraseña:</label>
      <input type="password" name="clave" required>

      <button type="submit" class="btn-modal">Crear Cliente</button>
    </form>
  </div>
</div>
<!-- MODAL EDITAR CLIENTE -->
<div id="modalEditarCliente" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:1000;">
  <div class="modal-content" style="background:white; padding:20px; border-radius:10px; width:400px; max-width:90%; position:relative;">
    
    <span class="close" onclick="document.getElementById('modalEditarCliente').style.display='none'" style="position:absolute; top:10px; right:15px; cursor:pointer; font-size:20px;">&times;</span>
    
    <h2 style="text-align:center; margin-bottom:15px;">Editar Cliente</h2>

    <input type="text" id="buscarCliente" placeholder="Buscar cliente por nombre" onkeyup="buscarCliente()" style="width:100%; padding:8px; margin-bottom:5px; border-radius:5px; border:1px solid #ccc;">
    <div id="listaClientes" style="border:1px solid #ccc; max-height:120px; overflow:auto; margin-bottom:15px;"></div>

    <form id="formEditarCliente" method="POST" action="editar_cliente.php">
      <input type="hidden" name="id" id="editarId">

      <label>Usuario:</label>
      <input type="text" name="usuario" id="editarUsuario" required style="width:100%; padding:8px; margin-bottom:10px; border-radius:5px; border:1px solid #ccc;">

      <label>Dirección:</label>
      <input type="text" name="direccion" id="editarDireccion" required style="width:100%; padding:8px; margin-bottom:10px; border-radius:5px; border:1px solid #ccc;">

      <label>Contraseña:</label>
      <input type="password" name="clave" id="editarClave" placeholder="Dejar vacío si no se cambia" style="width:100%; padding:8px; margin-bottom:15px; border-radius:5px; border:1px solid #ccc;">

      <button type="submit" style="width:100%; padding:10px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;">Guardar Cambios</button>
    </form>

  </div>
</div>


</body>
</html>
