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

// Obtener solicitudes finalizadas
$filtroNumero = isset($_GET['numerodesoli']) ? $_GET['numerodesoli'] : '';
$query = "SELECT * FROM solicitud WHERE estado = 'FINALIZADO'";
if (!empty($filtroNumero)) {
    $query .= " AND numerodesoli = " . intval($filtroNumero);
}
$result = mysqli_query($db, $query);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes Finalizadas</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../css/paneladmin.css" rel="stylesheet"> <!-- Archivo de estilos personalizados -->
</head>

<body>

    <!-- Sidebar -->
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
        <div class="container mt-5">
            <h1>Solicitudes Finalizadas</h1>
            <button class="logout-button" onclick="window.location.href='../controllers/cerrar.php';">Cerrar sesión</button>

            <!-- Filtro por número de solicitud -->
            <form class="row mb-4" method="GET" action="">
                <div class="col-md-10">
                    <input type="text" name="numerodesoli" class="form-control" placeholder="Filtrar por número de solicitud" value="<?php echo htmlspecialchars($filtroNumero); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filtrar</button>
                </div>
            </form>

            <!-- Tabla de solicitudes finalizadas -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th># Solicitud</th>
                        <th>Asunto</th>
                        <th>Infractor</th>
                        <th>Autor</th>
                        <th>Ubicación</th>
                        <th>Fecha Recepción</th>
                        <th>Observaciones</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['numerodesoli']; ?></td>
                <td><?php echo htmlspecialchars($row['asunto']); ?></td>
                <td><?php echo htmlspecialchars($row['infractor']); ?></td>
                <td><?php echo htmlspecialchars($row['autor']); ?></td>
                <td><?php echo htmlspecialchars($row['ubicacion']); ?></td>
                <td><?php echo $row['fecha_recepcion']; ?></td>
                <td><?php echo htmlspecialchars($row['concepto']); ?></td>
                <td>
                    <!-- Redirección a reportePersonal.php con el número de solicitud -->
                    <div class="col-auto ms-auto">
                    <a href="fpdf/reporteindividual.php?numerodesoli=<?php echo $row['numerodesoli']; ?>" class="btn btn-success" target="_blank">Generar Reporte</a>



        </div>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="8" class="text-center">No se encontraron solicitudes finalizadas.</td>
        </tr>
    <?php endif; ?>
</tbody>

            </table>
        </div>
    </div>

    <!-- JS de Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
</body>

</html>
