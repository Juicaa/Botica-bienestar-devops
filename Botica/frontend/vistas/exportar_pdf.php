<?php
require '../../vendor/autoload.php';
require '../../backend/conexion.php';

$pdf = new \TCPDF();
$pdf->AddPage();
$pdf->Image('../multimedia/logo_botica.png', 10, 10, 30); // (ruta, x, y, ancho)
$pdf->Ln(25); // Salto de línea para no sobreponer texto sobre la imagen

$pdf->SetFont('helvetica', '', 9);

// Encabezado HTML
$html = '<h2>Reporte de Ventas</h2>
<table border="1" cellpadding="4">
<tr style="background-color:#f2f2f2;">
<th>ID Venta</th>
<th>Fecha</th>
<th>Usuario</th>
<th>Medicamento</th>
<th>Cantidad</th>
<th>P. Unitario</th>
<th>Total</th>
</tr>';

// Consulta SQL
$sql = "SELECT v.id_venta, v.fecha, u.usuario, m.nombre AS medicamento, s.cantidad, 
               l.precio_unitario, (s.cantidad * l.precio_unitario) AS total
        FROM Ventas v
        JOIN Usuarios u ON v.id_usuario = u.id_usuario
        JOIN SalidaLotes s ON v.id_venta = s.id_venta
        JOIN Lotes l ON s.id_lote = l.id_lote
        JOIN Medicamentos m ON l.id_medicamento = m.id_medicamento
        ORDER BY v.fecha DESC";

$resultado = $conexion->query($sql);

while ($row = $resultado->fetch_assoc()) {
    $html .= "<tr>
        <td>{$row['id_venta']}</td>
        <td>{$row['fecha']}</td>
        <td>{$row['usuario']}</td>
        <td>{$row['medicamento']}</td>
        <td>{$row['cantidad']}</td>
        <td>{$row['precio_unitario']}</td>
        <td>{$row['total']}</td>
    </tr>";
}

$html .= '</table>';

// Generar PDF
$pdf->writeHTML($html);
ob_clean(); // Limpia cualquier espacio o salida previa
$pdf->Output('reporte_ventas.pdf', 'I');


