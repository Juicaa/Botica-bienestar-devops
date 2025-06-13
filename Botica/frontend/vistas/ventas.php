<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../login.html");
    exit();
}

include '../../backend/conexion.php';

// Filtro por fechas
$condicion = "";
$desde = "";
$hasta = "";

if (!empty($_GET['desde']) && !empty($_GET['hasta'])) {
    $desde = $_GET['desde'];
    $hasta = $_GET['hasta'];
    $condicion = "WHERE DATE(v.fecha) BETWEEN '$desde' AND '$hasta'";
}

$query = "
    SELECT v.id_venta, v.fecha, v.total, u.usuario
    FROM Ventas v
    JOIN Usuarios u ON v.id_usuario = u.id_usuario
    $condicion
    ORDER BY v.fecha DESC
";
$ventas = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Ventas</title>
  <link rel="stylesheet" href="../dashboard1.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container p-4">
  <h2>Historial de Ventas</h2>
  <a href="../dashboard1.php" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Volver al Dashboard
  </a>

  <!-- Formulario de filtrado -->
  <form class="row g-3 mb-4" method="GET">
    <div class="col-md-4">
      <label for="desde" class="form-label">Desde</label>
      <input type="date" class="form-control" name="desde" value="<?= $desde ?>">
    </div>
    <div class="col-md-4">
      <label for="hasta" class="form-label">Hasta</label>
      <input type="date" class="form-control" name="hasta" value="<?= $hasta ?>">
    </div>
    <div class="col-md-4 d-flex align-items-end">
      <button class="btn btn-success w-100">Filtrar</button>
    </div>
  </form>

  <!-- Tabla de ventas -->
  <table class="table table-bordered table-hover">
    <thead class="table-success">
      <tr>
        <th>ID Venta</th>
        <th>Fecha</th>
        <th>Total (S/)</th>
        <th>Vendedor</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($venta = $ventas->fetch_assoc()): ?>
        <tr>
          <td><?= $venta['id_venta'] ?></td>
          <td><?= $venta['fecha'] ?></td>
          <td><?= number_format($venta['total'], 2) ?></td>
          <td><?= $venta['usuario'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
