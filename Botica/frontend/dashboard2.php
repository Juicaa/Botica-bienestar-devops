<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Vendedor</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="dashboard1.css">
  <style>
    body::before {
      content: "";
      background: url('multimedia/fondo_login.jpg') no-repeat center center / cover;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      opacity: 0.3;
      z-index: -1;
    }
  </style>
</head>
<body>
<div class="d-flex" style="min-height: 100vh;">
  <!-- Menú lateral -->
  <nav class="bg-white border-end p-3" style="width: 250px;">
    <h5 class="text-success text-center mb-4">Vendedor</h5>
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link active" href="dashboard2.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="vistas/realizar_venta.php"><i class="bi bi-cash-register"></i> Realizar Venta</a></li>
      <li class="nav-item mt-3"><a class="btn btn-outline-danger w-100" href="../backend/logout.php">Cerrar sesión</a></li>
    </ul>
  </nav>

  <!-- Contenido principal -->
  <div class="flex-fill p-4">
    <h3 class="mb-4">Bienvenido, <?= htmlspecialchars($_SESSION['usuario']); ?> 👋</h3>

    <!-- Acceso directo -->
    <a href="vistas/realizar_venta.php" class="btn btn-success btn-lg">
      <i class="bi bi-cart-plus"></i> Registrar nueva venta
    </a>
  </div>
</div>
</body>
</html>
