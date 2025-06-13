<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../login.html");
    exit();
}

include '../../backend/conexion.php';

// Crear nuevo usuario
if (isset($_POST['crear'])) {
    $nuevoUsuario = $_POST['nuevo_usuario'];
    $nuevaContra = $_POST['nueva_contrasena'];
    $rol = 'vendedor';

    $stmt = $conexion->prepare("INSERT INTO Usuarios (usuario, contraseña, rol) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nuevoUsuario, $nuevaContra, $rol);
    $stmt->execute();
    header("Location: usuarios.php");
    exit();
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    // Verifica si el usuario tiene ventas
    $verificar = $conexion->query("SELECT COUNT(*) as total FROM Ventas WHERE id_usuario = $id");
    $dato = $verificar->fetch_assoc();

    if ($dato['total'] > 0) {
        echo "<script>alert('No se puede eliminar este usuario porque tiene ventas registradas.'); window.history.back();</script>";
        exit();
    }

    // Si no tiene ventas, permite eliminar
    $conexion->query("DELETE FROM Usuarios WHERE id_usuario = $id");
    header("Location: usuarios.php");
    exit();
}


// Editar usuario
if (isset($_POST['editar'])) {
    $idEditar = $_POST['id_editar'];
    $usuarioEditado = $_POST['usuario_editado'];
    $contraEditada = $_POST['contrasena_editada'];
    $stmt = $conexion->prepare("UPDATE Usuarios SET usuario=?, contraseña=? WHERE id_usuario=?");
    $stmt->bind_param("ssi", $usuarioEditado, $contraEditada, $idEditar);
    $stmt->execute();
    header("Location: usuarios.php");
    exit();
}

// Obtener lista actualizada
$resultado = $conexion->query("SELECT id_usuario, usuario, rol FROM Usuarios WHERE rol = 'vendedor'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios</title>
  <link rel="stylesheet" href="../dashboard1.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container p-4">
    <a href="../dashboard1.php" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left-circle"></i> Volver al Dashboard
  </a>
  <h2>Usuarios - Vendedores</h2>

  <!-- Formulario de creación -->
  <div class="card my-4">
    <div class="card-header bg-success text-white">Registrar nuevo vendedor</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-5">
            <input type="text" name="nuevo_usuario" class="form-control" placeholder="Nombre de usuario" required>
          </div>
          <div class="col-md-5">
            <input type="password" name="nueva_contrasena" class="form-control" placeholder="Contraseña" required>
          </div>
          <div class="col-md-2">
            <button class="btn btn-success w-100" name="crear">Registrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabla de usuarios -->
  <table class="table table-bordered table-hover">
    <thead class="table-success">
      <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Rol</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($fila = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?= $fila['id_usuario'] ?></td>
          <td><?= htmlspecialchars($fila['usuario']) ?></td>
          <td><?= $fila['rol'] ?></td>
          <td>
            <!-- Botón Editar (abre formulario debajo) -->
            <button class="btn btn-warning btn-sm" onclick="mostrarFormulario(<?= $fila['id_usuario'] ?>, '<?= $fila['usuario'] ?>')">Editar</button>
            <a href="?eliminar=<?= $fila['id_usuario'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Formulario oculto para editar -->
  <div class="card mt-4 d-none" id="formEditar">
    <div class="card-header bg-warning">Editar usuario</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="id_editar" id="idEditar">
        <div class="row g-3">
          <div class="col-md-5">
            <input type="text" name="usuario_editado" id="usuarioEditar" class="form-control" required>
          </div>
          <div class="col-md-5">
            <input type="password" name="contrasena_editada" class="form-control" placeholder="Nueva contraseña" required>
          </div>
          <div class="col-md-2 d-grid">
            <button class="btn btn-warning" name="editar">Guardar</button>
          </div>
          <div class="col-md-2 d-grid">
            <button type="button" class="btn btn-secondary" onclick="cancelarEdicion()">Cancelar</button>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>

<!-- Script para editar dinámicamente -->
<script>
function mostrarFormulario(id, usuario) {
  document.getElementById("idEditar").value = id;
  document.getElementById("usuarioEditar").value = usuario;
  document.getElementById("formEditar").classList.remove("d-none");
  document.getElementById("formEditar").scrollIntoView({ behavior: "smooth" });
}

function cancelarEdicion() {
  document.getElementById("formEditar").classList.add("d-none");
  document.getElementById("idEditar").value = "";
  document.getElementById("usuarioEditar").value = "";
}
</script>
</body>
</html>

