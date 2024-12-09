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

// Consultar la cantidad total de solicitudes
$totalSolicitudesQuery = "SELECT COUNT(*) as total FROM solicitud";
$totalSolicitudesResult = mysqli_query($db, $totalSolicitudesQuery);
$totalSolicitudes = mysqli_fetch_assoc($totalSolicitudesResult)['total'];

// Consultar las solicitudes por fecha (hoy)
$fechaHoy = date('Y-m-d');
$solicitudesHoyQuery = "SELECT COUNT(*) as hoy FROM solicitud WHERE DATE(fecha_recepcion) = '$fechaHoy'";
$solicitudesHoyResult = mysqli_query($db, $solicitudesHoyQuery);
$solicitudesHoy = mysqli_fetch_assoc($solicitudesHoyResult)['hoy'];

// Consultar las solicitudes procesadas
$solicitudesProcesadasQuery = "SELECT COUNT(*) as procesadas FROM solicitud WHERE estado = 'Procesada'";
$solicitudesProcesadasResult = mysqli_query($db, $solicitudesProcesadasQuery);
$solicitudesProcesadas = mysqli_fetch_assoc($solicitudesProcesadasResult)['procesadas'];

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../css/paneladmin.css" rel="stylesheet">
    
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">p</a>
        </div>
    </nav>

    <div class="sidebar">
    <h4>
        <a href="paneladmin.php" class="menu-link">Menú de Administración</a>
    </h4>
    <a href="registrar.php"><i class="fas fa-edit"></i> Registrar Solicitudes</a>
    <a href="ver_solicitudes.php"><i class="fas fa-list-alt"></i> Ver Solicitudes</a>
    <a href="actualizar_solicitudes.php"><i class="fas fa-sync-alt"></i> Actualizar Solicitudes</a>
    <a href="solicitudes_fin.php"><i class="fas fa-check-circle"></i> Solicitudes Finalizadas</a>

    <!-- Contenedor de la imagen -->
    <div class="sidebar-footer">
        <img src="../img/admin.png" alt="Admin" class="admin-image">
    </div>
</div>




    <!-- Contenido principal -->
    <div class="main-content">
        <!-- Botón de Cerrar Sesión -->
        <button class="logout-button" onclick="window.location.href='../controllers/cerrar.php';">Cerrar sesión</button>

        <div class="container-fluid">
            <!-- Título del dashboard -->
            <div class="dashboard-header">
                <h1>Bienvenido al Panel de Administración</h1>
            </div>

            <!-- Tarjetas de Dashboard -->
            <div class="row">
                <!-- Card 1: Total Solicitudes -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-clipboard-list icon"></i> Solicitudes Ingresadas
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $totalSolicitudes; ?> Solicitudes</h5>
                            <p class="card-text">Total de solicitudes registradas en el sistema.</p>
                        </div>
                        <div class="card-footer">
                            <a href="ver_solicitudes.php" class="btn btn-primary">Ver Solicitudes</a>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Solicitudes Hoy -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-calendar-day icon"></i> Solicitudes Hoy
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $solicitudesHoy; ?> Solicitudes</h5>
                            <p class="card-text">Total de solicitudes registradas el dia de hoy.</p>
                        </div>
                        <div class="card-footer">
                            <a href="registrar.php" class="btn btn-success">Registrar Nueva</a>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Solicitudes Procesadas -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-check-circle icon"></i> Solicitudes Editadas
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $solicitudesProcesadas; ?> Solicitudes</h5>
                            <p class="card-text">Total de solicitudes que han sido editadas el dia de hoy.</p>
                        </div>
                        <div class="card-footer">
                            <a href="ver_solicitudes.php" class="btn btn-primary">Ver Solicitudes</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- JS de Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
</body>

</html>
