<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    // Verificar si los campos están completos
    if (empty($usuario) || empty($contrasena)) {
        echo "<script>alert('Por favor completa todos los campos.'); window.history.back();</script>";
        exit();
    }

    // Consulta usando sentencia preparada
    $stmt = $conexion->prepare("SELECT id_usuario, usuario, contraseña, rol FROM Usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();

        // Verifica contraseña
        if ($contrasena === $fila['contraseña']) {
            $_SESSION['id_usuario'] = $fila['id_usuario'];
            $_SESSION['usuario'] = $fila['usuario'];
            $_SESSION['rol'] = $fila['rol'];


            // Redireccionar según rol
            if ($fila['rol'] === 'administrador') {
                header("Location: ../frontend/dashboard1.php");
                exit();
            } elseif ($fila['rol'] === 'vendedor') {
                header("Location: ../frontend/dashboard2.php");
                exit();
            } else {
                echo "<script>alert('Rol no reconocido.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Contraseña incorrecta.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Acceso no permitido.'); window.history.back();</script>";
}
?>