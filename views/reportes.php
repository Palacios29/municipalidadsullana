<?php
session_start();
include '../config/database.php';  // Asegúrate de que la ruta sea correcta

// Verificar si el usuario no está autenticado
if (empty($_SESSION["id"])) {
    header("Location: /appmuni");
    exit();
}

// Conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "munidb";

// Crear conexión
$db = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

// Verificar la conexión
if (!$db) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Manejo del filtro por número de solicitud
$solicitud = null;
if (isset($_POST['buscar']) && !empty($_POST['numerodesoli'])) {
    $numerodesoli = mysqli_real_escape_string($db, $_POST['numerodesoli']);
    // Consultar la solicitud filtrada
    $solicitudQuery = "SELECT * FROM solicitud WHERE numerodesoli = '$numerodesoli'";
    $solicitudResult = mysqli_query($db, $solicitudQuery);
    $solicitud = mysqli_fetch_assoc($solicitudResult);
}


// Generación de reporte en PDF

if (isset($_POST['generar_pdf']) && $solicitud) {
    require('../views/fpdf/reporte.php');  // Incluye la librería FPDF

    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Título
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(200, 10, 'Reporte de Solicitud', 0, 1, 'C');
    
    // Detalles de la solicitud
    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(10);  // Salto de línea
    $pdf->Cell(40, 10, 'Numero de Solicitud: ', 0, 0);
    $pdf->Cell(150, 10, $solicitud['numerodesoli'], 0, 1);
    $pdf->Cell(40, 10, 'Asunto: ', 0, 0);
    $pdf->Cell(150, 10, $solicitud['asunto'], 0, 1);
    $pdf->Cell(40, 10, 'Infractor: ', 0, 0);
    $pdf->Cell(150, 10, $solicitud['infractor'], 0, 1);
    $pdf->Cell(40, 10, 'Estado: ', 0, 0);
    $pdf->Cell(150, 10, $solicitud['estado'], 0, 1);
    $pdf->Cell(40, 10, 'Fecha de Recepción: ', 0, 0);
    $pdf->Cell(150, 10, $solicitud['fecha_recepcion'], 0, 1);
    $pdf->Cell(40, 10, 'Concepto: ', 0, 0);
    $pdf->MultiCell(0, 10, $solicitud['concepto']);
    
    // Salvar el PDF
    $pdf->Output('D', 'reporte_solicitud_' . $solicitud['numerodesoli'] . '.pdf');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Reporte de Solicitudes</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Roboto', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #2f3b4c;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.7rem;
            color: #fff;
        }

        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #2f3b4c;
            color: white;
            padding-top: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar h4 {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            color: #fff;
            margin-bottom: 40px;
        }

        .sidebar a {
            color: #d1d1d1;
            padding: 12px;
            display: block;
            text-decoration: none;
            margin-bottom: 15px;
            font-size: 1.1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #444;
            color: white;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            background-color: #ffffff;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border-radius: 15px 15px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .search-form input, .search-form button {
            margin-right: 10px;
        }
        .logout-button {
            position: fixed;
            top: 15px;
            right: 30px;
            background-color: #f44336;
            color: white;
            font-size: 16px;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
            z-index: 1100;
        }

        .logout-button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sistema Muni</a>
        </div>
    </nav>
   <!-- Botón de Cerrar Sesión -->
   <button class="logout-button" onclick="window.location.href='../controllers/cerrar.php';">Cerrar sesión</button>

<div class="container-fluid">
    <div class="sidebar">
        <h4>Menú de Administración</h4>
        <a href="registrar.php"><i class="fas fa-edit"></i> Registrar Solicitudes</a>
        <a href="ver_solicitudes.php"><i class="fas fa-list-alt"></i> Ver Solicitudes</a>
        <a href="actualizar_solicitudes.php"><i class="fas fa-sync-alt"></i> Actualizar Solicitudes</a>
        <a href="reportes.php"><i class="fas fa-chart-line"></i> Generar Reportes</a>
    </div>

    <div class="main-content">
        <h1>Generar Reporte de Solicitudes</h1>
        <form method="POST" class="search-form mb-4">
            <input type="text" name="numerodesoli" class="form-control d-inline w-50" placeholder="Buscar por Número de Solicitud" required>
            <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
        </form>

        <?php if ($solicitud): ?>
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file-pdf"></i> Reporte de Solicitud
                </div>
                <div class="card-body">
                    <h5>Detalles de la Solicitud</h5>
                    <ul>
                        <li><strong>Número de Solicitud:</strong> <?php echo $solicitud['numerodesoli']; ?></li>
                        <li><strong>Asunto:</strong> <?php echo $solicitud['asunto']; ?></li>
                        <li><strong>Infractor:</strong> <?php echo $solicitud['infractor']; ?></li>
                        <li><strong>Estado:</strong> <?php echo $solicitud['estado']; ?></li>
                        <li><strong>Fecha de Recepción:</strong> <?php echo $solicitud['fecha_recepcion']; ?></li>
                        <li><strong>Observaciones:</strong> <?php echo $solicitud['concepto']; ?></li>
                    </ul>

                    <form method="POST">
                    <div class="text-right">
                    <a href="fpdf/reporte.php" class="btn btn-success" target="_blank">Generar Reporte</a>

                    </div>
                        
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    

    <!-- Bootstrap 5 JS y Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
</body>

</html>
