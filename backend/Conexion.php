<?php
// Parámetros de conexión
$host = "localhost";
$usuario = "root";
$contrasena = "alexander06";
$baseDeDatos = "BoticaBienestar"; 

// Crear la conexión
$conn = new mysqli($host, $usuario, $contrasena, $baseDeDatos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

echo "Conexión exitosa a la base de datos";

// Cerrar conexión (opcional aquí)
$conn->close();
?>
