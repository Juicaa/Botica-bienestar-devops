<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.html");
    exit();
}

include '../backend/conexion.php';

// CONSULTAS DINÁMICAS
$totalMedicamentos = $conexion->query("SELECT COUNT(*) FROM Medicamentos")->fetch_row()[0];
$stockTotal = $conexion->query("SELECT SUM(cantidad) FROM Lotes")->fetch_row()[0] ?? 0;

$hoy = date('Y-m-d');
$limite = date('Y-m-d', strtotime('+15 days'));
$lotesPorVencer = $conexion->query("SELECT COUNT(*) FROM Lotes WHERE fecha_vencimiento BETWEEN '$hoy' AND '$limite'")->fetch_row()[0];

$ventasHoy = $conexion->query("SELECT SUM(total) FROM Ventas WHERE DATE(fecha) = CURDATE()")->fetch_row()[0] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Administrador</title>
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
      <h5 class="text-success text-center mb-4">Administrador</h5>
      <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link active" href="dashboard1.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="vistas/usuarios.php"><i class="bi bi-person"></i> Usuarios</a></li>
        <li class="nav-item"><a class="nav-link" href="vistas/medicamentos.php"><i class="bi bi-capsule-pill"></i> Medicamentos</a></li>
        <li class="nav-item"><a class="nav-link" href="vistas/categorias.php"><i class="bi bi-tags"></i> Categorías</a></li>
        <li class="nav-item"><a class="nav-link" href="vistas/lotes.php"><i class="bi bi-box-seam"></i> Lotes</a></li>
        <li class="nav-item"><a class="nav-link" href="vistas/ventas.php"><i class="bi bi-cart-check"></i> Ventas</a></li>
        <li class="nav-item"><a class="nav-link" href="vistas/alertas.php"><i class="bi bi-bell"></i> Alertas</a></li>
        <li class="nav-item"><a class="nav-link" href="vistas/reportes.php"><i class="bi bi-bar-chart-line"></i> Reportes</a></li>
        <li class="nav-item mt-3"><a class="btn btn-outline-danger w-100" href="../backend/logout.php">Cerrar sesión</a></li>
      </ul>
    </nav>

    <!-- Contenido principal -->
    <div class="flex-fill p-4">
      <h3 class="mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?> 👋</h3>

      <!-- Tarjetas resumen -->
      <div class="row g-3">
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3">
            <h6>Total Medicamentos</h6>
            <h4><?= $totalMedicamentos ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3">
            <h6>Stock Disponible</h6>
            <h4><?= $stockTotal ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3">
            <h6>Lotes por Vencer</h6>
            <h4><?= $lotesPorVencer ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3">
            <h6>Ventas del Día</h6>
            <h4>S/ <?= number_format($ventasHoy, 2) ?></h4>
          </div>
        </div>
      </div>

      <!-- Espacio para gráficos y tablas dinámicas -->
    </div>
  </div>
</body>
</html>
