<?php
// Leer la variable de entorno DATABASE_URL configurada en Render
$databaseUrl = getenv('DATABASE_URL');

// Usar esta variable para conectar con la base de datos
$pdo = new PDO($databaseUrl);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Verificar si la conexión fue exitosa
if ($pdo) {
    echo "Conexión exitosa!";
} else {
    echo "Error en la conexión.";
}
?>
