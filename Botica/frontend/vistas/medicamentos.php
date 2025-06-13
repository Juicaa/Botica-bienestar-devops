<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../login.html");
    exit();
}

include '../../backend/conexion.php';

// CREAR
if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['id_categoria'];
    $stmt = $conexion->prepare("INSERT INTO Medicamentos (nombre, id_categoria) VALUES (?, ?)");
    $stmt->bind_param("si", $nombre, $categoria);
    $stmt->execute();
    header("Location: medicamentos.php");
    exit();
}

// EDITAR
if (isset($_POST['editar'])) {
    $id = $_POST['id_editar'];
    $nombre = $_POST['nombre_editado'];
    $categoria = $_POST['categoria_editada'];
    $stmt = $conexion->prepare("UPDATE Medicamentos SET nombre=?, id_categoria=? WHERE id_medicamento=?");
    $stmt->bind_param("sii", $nombre, $categoria, $id);
    $stmt->execute();
    header("Location: medicamentos.php");
    exit();
}

// ELIMINAR
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM Medicamentos WHERE id_medicamento=$id");
    header("Location: medicamentos.php");
    exit();
}

// CONSULTAS
$medicamentos = $conexion->query("
  SELECT m.id_medicamento, m.nombre, c.nombre AS categoria 
  FROM Medicamentos m 
  JOIN Categorias c ON m.id_categoria = c.id_categoria
");

$categorias = $conexion->query("SELECT * FROM Categorias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Medicamentos</title>
  <link rel="stylesheet" href="../dashboard1.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container p-4">
  <h2>Gestión de Medicamentos</h2>
  <a href="../dashboard1.php" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Volver al Dashboard
  </a>

  <!-- Formulario crear -->
  <div class="card mb-4">
    <div class="card-header bg-success text-white">Registrar nuevo medicamento</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-6">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre del medicamento" required>
          </div>
          <div class="col-md-4">
            <select name="id_categoria" class="form-select" required>
              <option value="" disabled selected>Seleccione categoría</option>
              <?php while ($cat = $categorias->fetch_assoc()): ?>
                <option value="<?= $cat['id_categoria'] ?>"><?= $cat['nombre'] ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-2 d-grid">
            <button class="btn btn-success" name="crear">Registrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabla de medicamentos -->
  <table class="table table-bordered table-hover">
    <thead class="table-success">
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Categoría</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($medicamentos as $med): ?>
        <tr>
          <td><?= $med['id_medicamento'] ?></td>
          <td><?= htmlspecialchars($med['nombre']) ?></td>
          <td><?= $med['categoria'] ?></td>
          <td>
            <button class="btn btn-warning btn-sm"
              onclick="mostrarFormulario(<?= $med['id_medicamento'] ?>, '<?= $med['nombre'] ?>', <?= array_search($med['categoria'], array_column(iterator_to_array($categorias), 'nombre')) ?>)">Editar</button>
            <a href="?eliminar=<?= $med['id_medicamento'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este medicamento?')">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Formulario de edición -->
  <div class="card mt-4 d-none" id="formEditar">
    <div class="card-header bg-warning">Editar medicamento</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="id_editar" id="idEditar">
        <div class="row g-3">
          <div class="col-md-5">
            <input type="text" name="nombre_editado" id="nombreEditar" class="form-control" required>
          </div>
          <div class="col-md-5">
            <select name="categoria_editada" id="categoriaEditar" class="form-select" required>
              <?php
              // Volver a consultar categorías (por si ya se consumieron antes)
              $categorias2 = $conexion->query("SELECT * FROM Categorias");
              while ($cat = $categorias2->fetch_assoc()):
              ?>
                <option value="<?= $cat['id_categoria'] ?>"><?= $cat['nombre'] ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-2 d-grid">
            <button class="btn btn-warning" name="editar">Guardar</button>
            <button type="button" class="btn btn-secondary mt-2" onclick="cancelarEdicion()">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function mostrarFormulario(id, nombre, categoriaId) {
  document.getElementById("idEditar").value = id;
  document.getElementById("nombreEditar").value = nombre;
  document.getElementById("categoriaEditar").value = categoriaId;
  document.getElementById("formEditar").classList.remove("d-none");
  document.getElementById("formEditar").scrollIntoView({ behavior: "smooth" });
}
function cancelarEdicion() {
  document.getElementById("formEditar").classList.add("d-none");
  document.getElementById("idEditar").value = "";
  document.getElementById("nombreEditar").value = "";
}
</script>
</body>
</html>

