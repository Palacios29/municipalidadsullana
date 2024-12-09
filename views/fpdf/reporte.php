<?php
session_start();
require('./fpdf.php');
include '../../config/database.php';  // Incluye la conexión a la base de datos

class PDF extends FPDF
{
    private $db; // Agrega una propiedad para la conexión

    function __construct($db)
    {
        parent::__construct();
        $this->db = $db; // Asigna la conexión a la propiedad
    }

    function Header()
    {
        $this->Image('logo.png', 10, 10, 70); // Ajusta según sea necesario
        $this->SetFont('Arial', 'B', 19);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(15);

        $this->Cell(55); // Espacio inicial para centrar
        $this->Cell(180, 18, utf8_decode('SUB GERENCIA DE TRÁNSITO Y SEGURIDAD VIAL'), 1, 1, 'C', 0);
        
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(70); // Espacio inicial para centrar
        $this->Cell(150, 6, utf8_decode("Reporte de Solicitudes"), 0, 1, 'C', 0);
        $this->Ln(5);

        $this->SetFillColor(125, 173, 221);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(163, 163, 163);
        $this->SetFont('Arial', 'B', 12);

        $this->SetX(4);
        $this->Cell(10, 10, utf8_decode('N°'), 1, 0, 'C', 1);
        $this->Cell(20, 10, utf8_decode('Solicitud'), 1, 0, 'C', 1);
        $this->Cell(65, 10, utf8_decode('Infractor'), 1, 0, 'C', 1); // Ampliado
        $this->Cell(35, 10, utf8_decode('Fecha Recepción'), 1, 0, 'C', 1);
        $this->Cell(160, 10, utf8_decode('Observaciones'), 1, 1, 'C', 1); // Ampliado
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');

        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $hoy = date('d/m/Y');
        $this->Cell(540, 10, utf8_decode($hoy), 0, 0, 'C');
    }
}

$pdf = new PDF($db);
$pdf->AddPage('L'); // Landscape
$pdf->AliasNbPages();

$pdf->SetFont('Arial', '', 9);
$pdf->SetDrawColor(163, 163, 163);

$consulta_reporte_solicitudes = $db->query("SELECT numerodesoli, infractor, fecha_recepcion, concepto FROM solicitud");

if (!$consulta_reporte_solicitudes) {
    die('Error en la consulta: ' . $db->error);
}

$i = 0;

while ($datos_reporte = $consulta_reporte_solicitudes->fetch_object()) {
    $i++;

    // Definir los anchos de las columnas
    $ancho_nro = 10;
    $ancho_solicitud = 20;
    $ancho_infractor = 65;
    $ancho_fecha = 35;
    $ancho_concepto = 160;

    // Altura base de una celda
    $altura_base = 10;

    // Calcular el número de líneas necesarias para "Infractor" y "Concepto"
    $pdf->SetFont('Arial', '', 9);
    $lineas_infractor = $pdf->GetStringWidth(utf8_decode($datos_reporte->infractor)) / $ancho_infractor;
    $lineas_concepto = $pdf->GetStringWidth(utf8_decode($datos_reporte->concepto)) / $ancho_concepto;

    // Redondear hacia arriba para obtener el número total de líneas
    $lineas_infractor = ceil($lineas_infractor);
    $lineas_concepto = ceil($lineas_concepto);

    // Obtener la altura máxima para la fila
    $altura_fila = max($altura_base, $lineas_infractor * 5, $lineas_concepto * 5);

    // N° (Número)
    $pdf->SetX(4);
    $pdf->Cell($ancho_nro, $altura_fila, utf8_decode($i), 1, 0, 'C', 0);

    // Solicitud
    $pdf->Cell($ancho_solicitud, $altura_fila, utf8_decode($datos_reporte->numerodesoli), 1, 0, 'C', 0);

    // Infractor (nombre)
    $x_infractor = $pdf->GetX();
    $y_infractor = $pdf->GetY();
    $pdf->MultiCell($ancho_infractor, 5, utf8_decode($datos_reporte->infractor), 0, 'L', 0);
    $pdf->SetXY($x_infractor + $ancho_infractor, $y_infractor);

    // Dibujar la celda de "Infractor" con la altura calculada
    $pdf->Rect($x_infractor, $y_infractor, $ancho_infractor, $altura_fila);

    // Fecha
    $pdf->Cell($ancho_fecha, $altura_fila, utf8_decode($datos_reporte->fecha_recepcion), 1, 0, 'C', 0);

    // Concepto (Observaciones)
    $x_concepto = $pdf->GetX();
    $y_concepto = $pdf->GetY();
    $pdf->MultiCell($ancho_concepto, 5, utf8_decode($datos_reporte->concepto), 0, 'L', 0);
    $pdf->SetXY($x_concepto + $ancho_concepto, $y_concepto);

    // Dibujar la celda de "Concepto" con la altura calculada
    $pdf->Rect($x_concepto, $y_concepto, $ancho_concepto, $altura_fila);

    // Salto de línea para la siguiente fila
    $pdf->Ln($altura_fila);
}





$pdf->Output('Reporte_Solicitudes.pdf', 'I');
?>
