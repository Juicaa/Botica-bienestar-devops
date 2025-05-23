<?php
// Parámetros de conexión
$host = "localhost";       // o IP del servidor, como "127.0.0.1"
$usuario = "root";         // Usuario de MySQL
$contrasena = "";          // Contraseña del usuario
$baseDeDatos = "mi_base";  // Nombre de la base de datos

// Crear la conexión
$conn = new mysqli($host, $usuario, $contrasena, $baseDeDatos);

// Verificar si hubo un error de conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Si todo está bien, mostrar mensaje
echo "Conexión exitosa a la base de datos";

// Cerrar la conexión (opcional si se hace al final del script)
$conn->close();
?>