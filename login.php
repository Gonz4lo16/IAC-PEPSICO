<?php
session_start();
require 'db.php';

$error = "";

// Si el formulario fue enviado
if (isset($_POST['usuario']) && isset($_POST['clave'])) {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    // Buscamos el usuario en la base de datos
    $stmt = $conn->prepare("SELECT id, clave, rol FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_usuario, $clave_hash, $rol);
        $stmt->fetch();

        // Verificamos la contraseña encriptada
        if (password_verify($clave, $clave_hash)) {
            $_SESSION['rol'] = $rol;
            $_SESSION['usuario'] = $usuario;
            $_SESSION['usuario_id'] = $id_usuario;

            header("Location: index.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login PepsiCo</title>
<style>
body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, sans-serif;
  background: #f0f2f5;
  color: #333;
}
.login-box {
  background: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
  text-align: center;
  width: 320px;
  margin: 100px auto;
}
.login-box img {
  margin-bottom: 10px;
}
.login-box input {
  width: 90%;
  padding: 10px;
  margin: 8px 0;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 16px;
}
.login-box button {
  background: #27ae60;
  border: none;
  padding: 10px 18px;
  color: white;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
}
.login-box button:hover {
  background: #219150;
}
.login-box p {
  margin-top: 10px;
}
</style>
</head>
<body>

<div class="login-box">
    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRh6LhrvwuBAWVIojFlwEhwXID9ne26_k22xw&s" alt="PepsiCo Logo" width="120">
    <h2>Iniciar Sesión</h2>

    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="clave" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>

    <?php if ($error): ?>
        <p style="color:#e74c3c;"><?= $error ?></p>
    <?php endif; ?>
</div>

</body>
</html>
