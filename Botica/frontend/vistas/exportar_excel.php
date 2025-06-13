<?php
require '../../vendor/autoload.php';
require '../../backend/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crear objeto Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Ventas");

// Encabezados
$sheet->fromArray(
    ['ID Venta', 'Fecha', 'Usuario', 'Medicamento', 'Cantidad', 'Precio Unitario', 'Total'],
    NULL, 'A1'
);

// Consulta
$sql = "SELECT v.id_venta, v.fecha, u.usuario, m.nombre AS medicamento, s.cantidad, 
               l.precio_unitario, (s.cantidad * l.precio_unitario) AS total
        FROM Ventas v
        JOIN Usuarios u ON v.id_usuario = u.id_usuario
        JOIN SalidaLotes s ON v.id_venta = s.id_venta
        JOIN Lotes l ON s.id_lote = l.id_lote
        JOIN Medicamentos m ON l.id_medicamento = m.id_medicamento
        ORDER BY v.fecha DESC";

$resultado = $conexion->query($sql);
$fila = 2;

while ($row = $resultado->fetch_assoc()) {
    $sheet->setCellValue("A$fila", $row['id_venta']);
    $sheet->setCellValue("B$fila", $row['fecha']);
    $sheet->setCellValue("C$fila", $row['usuario']);
    $sheet->setCellValue("D$fila", $row['medicamento']);
    $sheet->setCellValue("E$fila", $row['cantidad']);
    $sheet->setCellValue("F$fila", $row['precio_unitario']);
    $sheet->setCellValue("G$fila", $row['total']);
    $fila++;
}

// Descargar archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_ventas.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
