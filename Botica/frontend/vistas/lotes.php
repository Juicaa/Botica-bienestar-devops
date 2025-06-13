<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../login.html");
    exit();
}

include '../../backend/conexion.php';

// CREAR
if (isset($_POST['crear'])) {
    $id_medicamento = $_POST['id_medicamento'];
    $cantidad = $_POST['cantidad'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $precio = $_POST['precio_unitario'];

    $stmt = $conexion->prepare("INSERT INTO Lotes (id_medicamento, cantidad, fecha_ingreso, fecha_vencimiento, precio_unitario) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissd", $id_medicamento, $cantidad, $fecha_ingreso, $fecha_vencimiento, $precio);
    $stmt->execute();
    header("Location: lotes.php");
    exit();
}

// EDITAR
if (isset($_POST['editar'])) {
    $id = $_POST['id_editar'];
    $cantidad = $_POST['cantidad_editada'];
    $fecha_ingreso = $_POST['fecha_ingreso_editada'];
    $fecha_vencimiento = $_POST['fecha_vencimiento_editada'];
    $precio = $_POST['precio_editado'];

    $stmt = $conexion->prepare("UPDATE Lotes SET cantidad=?, fecha_ingreso=?, fecha_vencimiento=?, precio_unitario=? WHERE id_lote=?");
    $stmt->bind_param("issdi", $cantidad, $fecha_ingreso, $fecha_vencimiento, $precio, $id);
    $stmt->execute();
    header("Location: lotes.php");
    exit();
}

// ELIMINAR
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM Lotes WHERE id_lote=$id");
    header("Location: lotes.php");
    exit();
}

// OBTENER DATOS
$lotes = $conexion->query("
  SELECT l.*, m.nombre AS medicamento 
  FROM Lotes l 
  JOIN Medicamentos m ON l.id_medicamento = m.id_medicamento
");
$medicamentos = $conexion->query("SELECT * FROM Medicamentos");

$hoy = new DateTime();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Lotes</title>
  <link rel="stylesheet" href="../dashboard1.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container p-4">
  <h2>Gestión de Lotes</h2>
  <a href="../dashboard1.php" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Volver al Dashboard
  </a>

  <!-- Formulario de creación -->
  <div class="card mb-4">
    <div class="card-header bg-success text-white">Registrar nuevo lote</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-3">
            <select name="id_medicamento" class="form-select" required>
              <option value="" disabled selected>Seleccionar medicamento</option>
              <?php while ($m = $medicamentos->fetch_assoc()): ?>
                <option value="<?= $m['id_medicamento'] ?>"><?= $m['nombre'] ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-2">
            <input type="number" name="cantidad" class="form-control" placeholder="Cantidad" required>
          </div>
          <div class="col-md-2">
            <input type="date" name="fecha_ingreso" class="form-control" required>
          </div>
          <div class="col-md-2">
            <input type="date" name="fecha_vencimiento" class="form-control" required>
          </div>
          <div class="col-md-2">
            <input type="number" name="precio_unitario" step="0.01" class="form-control" placeholder="Precio" required>
          </div>
          <div class="col-md-1 d-grid">
            <button class="btn btn-success" name="crear">Registrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabla de lotes -->
  <table class="table table-bordered table-hover">
    <thead class="table-success">
      <tr>
        <th>ID</th>
        <th>Medicamento</th>
        <th>Cantidad</th>
        <th>Ingreso</th>
        <th>Vencimiento</th>
        <th>Precio (S/)</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($lotes as $l): 
        $fechaVencimiento = new DateTime($l['fecha_vencimiento']);
        $intervalo = $hoy->diff($fechaVencimiento);
        $diasRestantes = (int)$intervalo->format('%r%a');
        $claseFila = $diasRestantes <= 15 ? 'table-warning' : '';
      ?>
        <tr class="<?= $claseFila ?>">
          <td><?= $l['id_lote'] ?></td>
          <td><?= $l['medicamento'] ?></td>
          <td><?= $l['cantidad'] ?></td>
          <td><?= $l['fecha_ingreso'] ?></td>
          <td>
            <?= $l['fecha_vencimiento'] ?>
            <?php if ($diasRestantes <= 15): ?>
              <span class="text-danger fw-bold">⚠ Próximo a vencer</span>
            <?php endif; ?>
          </td>
          <td><?= number_format($l['precio_unitario'], 2) ?></td>
          <td>
            <button class="btn btn-warning btn-sm" onclick="mostrarFormulario(
              <?= $l['id_lote'] ?>,
              <?= $l['cantidad'] ?>,
              '<?= $l['fecha_ingreso'] ?>',
              '<?= $l['fecha_vencimiento'] ?>',
              <?= $l['precio_unitario'] ?>
            )">Editar</button>
            <a href="?eliminar=<?= $l['id_lote'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este lote?')">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Formulario de edición -->
  <div class="card mt-4 d-none" id="formEditar">
    <div class="card-header bg-warning">Editar lote</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="id_editar" id="idEditar">
        <div class="row g-3">
          <div class="col-md-2">
            <input type="number" name="cantidad_editada" id="cantidadEditar" class="form-control" required>
          </div>
          <div class="col-md-3">
            <input type="date" name="fecha_ingreso_editada" id="ingresoEditar" class="form-control" required>
          </div>
          <div class="col-md-3">
            <input type="date" name="fecha_vencimiento_editada" id="vencimientoEditar" class="form-control" required>
          </div>
          <div class="col-md-3">
            <input type="number" step="0.01" name="precio_editado" id="precioEditar" class="form-control" required>
          </div>
          <div class="col-md-1 d-grid">
            <button class="btn btn-warning" name="editar">Guardar</button>
            <button type="button" class="btn btn-secondary mt-2" onclick="cancelarEdicion()">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function mostrarFormulario(id, cantidad, ingreso, vencimiento, precio) {
  document.getElementById("idEditar").value = id;
  document.getElementById("cantidadEditar").value = cantidad;
  document.getElementById("ingresoEditar").value = ingreso;
  document.getElementById("vencimientoEditar").value = vencimiento;
  document.getElementById("precioEditar").value = precio;
  document.getElementById("formEditar").classList.remove("d-none");
  document.getElementById("formEditar").scrollIntoView({ behavior: "smooth" });
}

function cancelarEdicion() {
  document.getElementById("formEditar").classList.add("d-none");
  document.getElementById("idEditar").value = "";
}
</script>
</body>
</html>
