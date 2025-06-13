<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../login.html");
    exit();
}

include '../../backend/conexion.php';

// Crear categoría
if (isset($_POST['crear'])) {
    $nombre = trim($_POST['nombre']);
    $stmt = $conexion->prepare("INSERT INTO Categorias (nombre) VALUES (?)");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    header("Location: categorias.php");
    exit();
}

// Editar categoría
if (isset($_POST['editar'])) {
    $id = $_POST['id_editar'];
    $nombre = trim($_POST['nombre_editado']);
    $stmt = $conexion->prepare("UPDATE Categorias SET nombre=? WHERE id_categoria=?");
    $stmt->bind_param("si", $nombre, $id);
    $stmt->execute();
    header("Location: categorias.php");
    exit();
}

// Eliminar categoría
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM Categorias WHERE id_categoria=$id");
    header("Location: categorias.php");
    exit();
}

// Obtener todas las categorías
$categorias = $conexion->query("SELECT * FROM Categorias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Categorías</title>
  <link rel="stylesheet" href="../dashboard1.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container p-4">
  <h2>Gestión de Categorías</h2>
  <a href="../dashboard1.php" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left"></i> Volver al Dashboard
  </a>

  <!-- Formulario para crear -->
  <div class="card mb-4">
    <div class="card-header bg-success text-white">Registrar nueva categoría</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-10">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre de categoría" required>
          </div>
          <div class="col-md-2 d-grid">
            <button class="btn btn-success" name="crear">Registrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabla de categorías -->
  <table class="table table-bordered table-hover">
    <thead class="table-success">
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($cat = $categorias->fetch_assoc()): ?>
        <tr>
          <td><?= $cat['id_categoria'] ?></td>
          <td><?= htmlspecialchars($cat['nombre']) ?></td>
          <td>
            <button class="btn btn-warning btn-sm" onclick="mostrarFormulario(<?= $cat['id_categoria'] ?>, '<?= htmlspecialchars($cat['nombre'], ENT_QUOTES) ?>')">Editar</button>
            <a href="?eliminar=<?= $cat['id_categoria'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta categoría?')">Eliminar</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Formulario para editar -->
  <div class="card mt-4 d-none" id="formEditar">
    <div class="card-header bg-warning">Editar categoría</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="id_editar" id="idEditar">
        <div class="row g-3">
          <div class="col-md-10">
            <input type="text" name="nombre_editado" id="nombreEditar" class="form-control" required>
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
function mostrarFormulario(id, nombre) {
  document.getElementById("idEditar").value = id;
  document.getElementById("nombreEditar").value = nombre;
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
