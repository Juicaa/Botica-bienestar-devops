<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../login.html");
    exit();
}

include '../../backend/conexion.php';

// Lotes por vencer
$hoy = date('Y-m-d');
$limite = date('Y-m-d', strtotime('+15 days'));
$vencimientos = $conexion->query("
    SELECT l.id_lote, m.nombre AS medicamento, l.fecha_vencimiento, l.cantidad 
    FROM Lotes l
    JOIN Medicamentos m ON l.id_medicamento = m.id_medicamento
    WHERE l.fecha_vencimiento BETWEEN '$hoy' AND '$limite'
");

// Lotes con stock bajo
$stock_bajo = $conexion->query("
    SELECT l.id_lote, m.nombre AS medicamento, l.cantidad 
    FROM Lotes l
    JOIN Medicamentos m ON l.id_medicamento = m.id_medicamento
    WHERE l.cantidad < 10
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Alertas</title>
  <link rel="stylesheet" href="../dashboard1.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container p-4">
  <h2>Alertas del Sistema</h2>
  <a href="../dashboard1.php" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Volver al Dashboard</a>

  <div class="row">
    <div class="col-md-6">
      <h5 class="text-danger">🔴 Lotes por vencer (≤ 15 días)</h5>
      <table class="table table-bordered table-sm">
        <thead class="table-danger">
          <tr>
            <th>ID Lote</th>
            <th>Medicamento</th>
            <th>Fecha Vencimiento</th>
            <th>Cantidad</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($v = $vencimientos->fetch_assoc()): ?>
          <tr>
            <td><?= $v['id_lote'] ?></td>
            <td><?= $v['medicamento'] ?></td>
            <td><?= $v['fecha_vencimiento'] ?></td>
            <td><?= $v['cantidad'] ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="col-md-6">
      <h5 class="text-warning">🟠 Lotes con stock bajo (&lt; 10)</h5>
      <table class="table table-bordered table-sm">
        <thead class="table-warning">
          <tr>
            <th>ID Lote</th>
            <th>Medicamento</th>
            <th>Cantidad</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($s = $stock_bajo->fetch_assoc()): ?>
          <tr>
            <td><?= $s['id_lote'] ?></td>
            <td><?= $s['medicamento'] ?></td>
            <td><?= $s['cantidad'] ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
