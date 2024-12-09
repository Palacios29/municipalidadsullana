<?php
session_start();
require('./fpdf.php');
include '../../config/database.php';  // Incluye la conexión a la base de datos

// Obtenemos el ID de la solicitud, puede ser pasado por URL o como parámetro en sesión
$numerodesoli = isset($_GET['numerodesoli']) ? $_GET['numerodesoli'] : null;
if (!$numerodesoli) {
    die('Error: No se ha especificado el número de solicitud.');
}

class PDF extends FPDF
{
    private $db;

    function __construct($db)
    {
        parent::__construct();
        $this->db = $db;
    }

    function Header()
    {
        $this->Image('logo.png', 15, 15, 70); // Ajusta según sea necesario
        $this->SetFont('Arial', 'B', 19);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(15);

        // Título centrado
        $this->Cell(55); // Espacio inicial para centrar
        $this->Cell(100, 40, utf8_decode('SUB GERENCIA DE TRÁNSITO Y SEGURIDAD VIAL'), 0, 1, 'C', 0);
      
        $this->Ln(1);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(50); // Espacio inicial para centrar
        $this->Cell(100, 1, utf8_decode("Reporte de Solicitud Finalizada"), 0, 1, 'C', 0);
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15); // Coloca el pie de página a 15mm de distancia del final
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->SetY(-15); // Asegurarse de estar en la parte inferior
        $this->SetFont('Arial', 'I', 8);
        $hoy = date('d/m/Y');
        $this->Cell(540, 10, utf8_decode($hoy), 0, 0, 'C');
    }

    // Método para añadir espacio entre las celdas
    function addSpace($space)
    {
        $this->Ln($space);
    }
}

// Crear la instancia del reporte
$pdf = new PDF($db);
$pdf->AddPage('P'); // Asegúrate de que la orientación sea la correcta (portrait o landscape)
$pdf->AliasNbPages();

// Obtener los datos de la solicitud específica
$query = "SELECT numerodesoli, infractor, fecha_recepcion, concepto, autor, ubicacion
          FROM solicitud
          WHERE numerodesoli = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('i', $numerodesoli);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $datos_reporte = $result->fetch_object();

    // Centramos los datos de la solicitud en la página
    $pdf->SetFont('Arial', '', 9);
    
    // Para centrar los datos en la parte media de la página, se debe agregar un salto de línea grande.
    $pdf->Ln(20); // Espacio inicial para centrar más abajo

    // Información de la solicitud alineada a la izquierda, con un margen
    $margen_izq = 20; // Ajusta este valor para darle más o menos margen

   // Definir los márgenes
$margen_izq = 20; // Margen izquierdo para la etiqueta (datos)
$margen_der = 170; // Margen derecho para la respuesta (ajustar según sea necesario)

$pdf->SetFont('Arial', '', 9);

// Definir los márgenes
$margen_izq = 20; // Margen izquierdo para la etiqueta (datos)
$margen_der = 100; // Ajustar el margen derecho para reducir espacio entre las celdas

$pdf->SetFont('Arial', '', 9);

// Nro. Solicitud
$pdf->SetX($margen_izq); // Establecer el margen izquierdo para la etiqueta
$pdf->Cell(0, 10, utf8_decode('Nro. Solicitud: '), 0, 0); // Imprimir etiqueta
$pdf->SetX($margen_der); // Establecer el margen derecho para la respuesta
$pdf->Cell(0, 10, utf8_decode($datos_reporte->numerodesoli), 0, 1); // Imprimir respuesta

// Asunto
$pdf->SetX($margen_izq); // Establecer el margen izquierdo para la etiqueta
$pdf->Cell(0, 10, utf8_decode('Asunto: '), 0, 0); // Imprimir etiqueta
$pdf->SetX($margen_der); // Establecer el margen derecho para la respuesta
$pdf->Cell(0, 10, utf8_decode($datos_reporte->concepto), 0, 1); // Imprimir respuesta

// Infractor
$pdf->SetX($margen_izq); // Establecer el margen izquierdo para la etiqueta
$pdf->Cell(0, 10, utf8_decode('Infractor: '), 0, 0); // Imprimir etiqueta
$pdf->SetX($margen_der); // Establecer el margen derecho para la respuesta
$pdf->Cell(0, 10, utf8_decode($datos_reporte->infractor), 0, 1); // Imprimir respuesta

// Fecha Recepción
$pdf->SetX($margen_izq); // Establecer el margen izquierdo para la etiqueta
$pdf->Cell(0, 10, utf8_decode('Fecha Recepción: '), 0, 0); // Imprimir etiqueta
$pdf->SetX($margen_der); // Establecer el margen derecho para la respuesta
$pdf->Cell(0, 10, utf8_decode($datos_reporte->fecha_recepcion), 0, 1); // Imprimir respuesta

// Autor
$pdf->SetX($margen_izq); // Establecer el margen izquierdo para la etiqueta
$pdf->Cell(0, 10, utf8_decode('Autor: '), 0, 0); // Imprimir etiqueta
$pdf->SetX($margen_der); // Establecer el margen derecho para la respuesta
$pdf->Cell(0, 10, utf8_decode($datos_reporte->autor), 0, 1); // Imprimir respuesta

// Ubicación
$pdf->SetX($margen_izq); // Establecer el margen izquierdo para la etiqueta
$pdf->Cell(0, 10, utf8_decode('Ubicación: '), 0, 0); // Imprimir etiqueta
$pdf->SetX($margen_der); // Establecer el margen derecho para la respuesta
$pdf->Cell(0, 10, utf8_decode($datos_reporte->ubicacion), 0, 1); // Imprimir respuesta


    // Salto de línea antes de las observaciones
    $pdf->addSpace(10);

    // Observaciones (Concepto), alineadas a la izquierda
    $pdf->SetFont('Arial', 'I', 9);
$pdf->MultiCell(0, 10, utf8_decode("Observaciones: " . $datos_reporte->concepto), 0, 'R');

} else {
    $pdf->Cell(0, 10, 'No se encontró la solicitud.', 0, 1);
}

// Agregar el espacio para las firmas
$pdf->addSpace(50); // Espacio antes de las firmas, para que estén casi al final de la página

// Firmas
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(90, 10, 'Firma del Infractor: ________________________', 0, 0);
$pdf->Cell(90, 10, 'Firma del Encargado STySV: ________________________', 0, 1, 'C');

// Salida del PDF
$pdf->Output('Reporte_Solicitud_' . $numerodesoli . '.pdf', 'I');
?>
