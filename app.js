// ====== TOGGLE DEL PANEL DE CARRITO ======
function toggleCarrito() {
  const panel = document.getElementById('carrito-panel');
  panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
}

// ====== AGREGAR PRODUCTO AL CARRITO ======
function agregarCarrito(producto, precio) {
  // Enviamos al servidor los datos mediante fetch
  fetch('agregar_carrito.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `nombre=${encodeURIComponent(producto)}&precio=${precio}`
  })
  .then(() => {
    // Mostrar mensaje de éxito
    mostrarMensajeExito(`${producto} agregado al carrito ✅`);

    // Recarga para actualizar la lista
    setTimeout(() => location.reload(), 1200);
  })
  .catch(error => console.error('Error al agregar producto:', error));
}

// ====== MENSAJE DE ÉXITO ANIMADO ======
function mostrarMensajeExito(texto) {
  const mensaje = document.createElement('div');
  mensaje.classList.add('mensaje-exito');
  mensaje.textContent = texto;
  document.body.appendChild(mensaje);

  // Eliminar el mensaje después de unos segundos
  setTimeout(() => {
    mensaje.remove();
  }, 4000);
}
