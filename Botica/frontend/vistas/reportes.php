<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../login.html");
    exit();
}

include '../../backend/conexion.php';

// Filtros dinámicos
$filtro = "WHERE 1=1";
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$usuario = $_GET['usuario'] ?? '';
$medicamento = $_GET['medicamento'] ?? '';

if (!empty($desde) && !empty($hasta)) {
    $filtro .= " AND DATE(v.fecha) BETWEEN '$desde' AND '$hasta'";
}
if (!empty($usuario)) {
    $filtro .= " AND u.usuario = '$usuario'";
}
if (!empty($medicamento)) {
    $filtro .= " AND m.nombre = '$medicamento'";
}

// Obtener datos
$query = "
    SELECT v.id_venta, v.fecha, u.usuario, m.nombre AS medicamento, s.cantidad, l.precio_unitario, (s.cantidad * l.precio_unitario) AS total
    FROM Ventas v
    JOIN Usuarios u ON v.id_usuario = u.id_usuario
    JOIN SalidaLotes s ON v.id_venta = s.id_venta
    JOIN Lotes l ON s.id_lote = l.id_lote
    JOIN Medicamentos m ON l.id_medicamento = m.id_medicamento
    $filtro
    ORDER BY v.fecha DESC
";
$reportes = $conexion->query($query);

// Para filtros select
$usuarios = $conexion->query("SELECT DISTINCT usuario FROM Usuarios WHERE rol = 'vendedor'");
$medicamentos = $conexion->query("SELECT DISTINCT nombre FROM Medicamentos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reportes de Ventas</title>
  <link rel="stylesheet" href="../dashboard1.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container p-4">
  <h2>Reportes de Ventas</h2>
  <a href="../dashboard1.php" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Volver al Dashboard</a>

  <!-- Filtros -->
  <form class="row g-3 mb-4" method="GET">
    <div class="col-md-3">
      <label>Desde</label>
      <input type="date" class="form-control" name="desde" value="<?= $desde ?>">
    </div>
    <div class="col-md-3">
      <label>Hasta</label>
      <input type="date" class="form-control" name="hasta" value="<?= $hasta ?>">
    </div>
    <div class="col-md-3">
      <label>Usuario</label>
      <select class="form-select" name="usuario">
        <option value="">Todos</option>
        <?php while ($u = $usuarios->fetch_assoc()): ?>
          <option value="<?= $u['usuario'] ?>" <?= $usuario == $u['usuario'] ? 'selected' : '' ?>>
            <?= $u['usuario'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label>Medicamento</label>
      <select class="form-select" name="medicamento">
        <option value="">Todos</option>
        <?php while ($m = $medicamentos->fetch_assoc()): ?>
          <option value="<?= $m['nombre'] ?>" <?= $medicamento == $m['nombre'] ? 'selected' : '' ?>>
            <?= $m['nombre'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-12 d-flex gap-3">
      <button class="btn btn-success" type="submit">Filtrar</button>
      <a href="reportes.php" class="btn btn-outline-secondary">Limpiar</a>
    </div>
  </form>

  <!-- Tabla de resultados -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-success">
        <tr>
          <th>ID Venta</th>
          <th>Fecha</th>
          <th>Usuario</th>
          <th>Medicamento</th>
          <th>Cantidad</th>
          <th>Precio Unitario (S/)</th>
          <th>Total (S/)</th>
        </tr>
      </thead>
      <tbody>
        <?php $totalGeneral = 0; while ($r = $reportes->fetch_assoc()): 
          $totalGeneral += $r['total'];
        ?>
        <tr>
          <td><?= $r['id_venta'] ?></td>
          <td><?= $r['fecha'] ?></td>
          <td><?= $r['usuario'] ?></td>
          <td><?= $r['medicamento'] ?></td>
          <td><?= $r['cantidad'] ?></td>
          <td><?= number_format($r['precio_unitario'], 2) ?></td>
          <td><?= number_format($r['total'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
      <tfoot>
        <tr class="table-light fw-bold">
          <td colspan="6" class="text-end">TOTAL GENERAL:</td>
          <td>S/ <?= number_format($totalGeneral, 2) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- Botones para exportar (pueden enlazarse con PHP más adelante) -->
  <div class="mt-3 d-flex gap-3">
    <a href="exportar_excel.php" class="btn btn-outline-success">Exportar a Excel</a>
    <a href="exportar_pdf.php" class="btn btn-outline-danger">Exportar a PDF</a>
  </div>
</div>
</body>
</html>
