<?php
$host = "localhost";           // o 127.0.0.1
$usuario = "root";             // cambia si usas otro usuario
$contrasena = "";              // cambia si tienes contraseña en MySQL
$base_datos = "BoticaBienestar";

$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Opcional: establecer codificación utf8
$conexion->set_charset("utf8");
?>
