<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    die("Error: id_usuario no está definido en sesión.");
}
$id_usuario = (int)$_SESSION['id_usuario'];


include '../../backend/conexion.php';

// Obtener ID del usuario que realiza la venta
$id_usuario = $_SESSION['id_usuario'] ?? null;

// Registrar venta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lote'], $_POST['cantidad'])) {
    $id_lote = (int)$_POST['lote'];
    $cantidad = (int)$_POST['cantidad'];

    // Verificar cantidad disponible del lote
    $loteData = $conexion->query("SELECT cantidad, precio_unitario FROM Lotes WHERE id_lote = $id_lote")->fetch_assoc();
    if ($cantidad > 0 && $cantidad <= $loteData['cantidad']) {
        $precio_unitario = $loteData['precio_unitario'];
        $total = $cantidad * $precio_unitario;

        // Registrar venta
        $conexion->query("INSERT INTO Ventas (total, id_usuario) VALUES ($total, $id_usuario)");
        $id_venta = $conexion->insert_id;

        // Registrar salida
        $stmt = $conexion->prepare("INSERT INTO SalidaLotes (id_lote, id_venta, cantidad) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $id_lote, $id_venta, $cantidad);
        $stmt->execute();

        // Actualizar stock
        $conexion->query("UPDATE Lotes SET cantidad = cantidad - $cantidad WHERE id_lote = $id_lote");

        header("Location: realizar_venta.php?exito=1");
        exit();
    } else {
        $error = "La cantidad debe ser mayor a 0 y no superar el stock disponible.";
    }
}

// Obtener lotes disponibles con stock > 0
$lotes = $conexion->query("
    SELECT l.id_lote, l.cantidad, l.precio_unitario, m.nombre 
    FROM Lotes l 
    JOIN Medicamentos m ON l.id_medicamento = m.id_medicamento 
    WHERE l.cantidad > 0
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Venta</title>
  <link rel="stylesheet" href="../dashboard1.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script>
    function actualizarTotal() {
      const select = document.getElementById('lote');
      const cantidad = parseInt(document.getElementById('cantidad').value || 0);
      const precio = parseFloat(select.selectedOptions[0].dataset.precio || 0);
      const total = cantidad * precio;
      document.getElementById('total').value = total.toFixed(2);
    }
  </script>
</head>
<body>
<div class="container p-4">
  <h2>Registrar Venta</h2>
  <a href="../dashboard2.php" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Volver al Dashboard</a>

  <?php if (isset($_GET['exito'])): ?>
    <div class="alert alert-success">Venta registrada correctamente.</div>
  <?php elseif (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" class="card p-4">
    <div class="row g-3 align-items-end">
      <div class="col-md-6">
        <label for="lote" class="form-label">Lote disponible</label>
        <select name="lote" id="lote" class="form-select" onchange="actualizarTotal()" required>
          <option value="">-- Selecciona un lote --</option>
          <?php while ($lote = $lotes->fetch_assoc()): ?>
            <option value="<?= $lote['id_lote'] ?>" 
                    data-precio="<?= $lote['precio_unitario'] ?>">
              <?= $lote['nombre'] ?> - Lote <?= $lote['id_lote'] ?> (<?= $lote['cantidad'] ?> unidades, S/<?= number_format($lote['precio_unitario'], 2) ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label for="cantidad" class="form-label">Cantidad</label>
        <input type="number" name="cantidad" id="cantidad" min="1" class="form-control" required oninput="actualizarTotal()">
      </div>

      <div class="col-md-3">
        <label for="total" class="form-label">Total (S/)</label>
        <input type="text" id="total" class="form-control" disabled>
      </div>

      <div class="col-md-12 d-grid mt-3">
        <button class="btn btn-success" type="submit">Registrar Venta</button>
      </div>
    </div>
  </form>
</div>
</body>
</html>
