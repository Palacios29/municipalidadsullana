<?php
    include '../config/database.php';  // Asegúrate de que la ruta sea correcta
    session_start();

    // Establecer la zona horaria a Perú
    date_default_timezone_set('America/Lima');
    // Obtener la fecha y hora actual en Perú (zona horaria de Lima)


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

    // Obtener el valor del filtro (si se ha enviado)
    $nombreInfractor = isset($_GET['nombre_infractor']) ? mysqli_real_escape_string($db, $_GET['nombre_infractor']) : '';

    // Consulta para obtener las solicitudes con filtro por nombre
    $sql = "SELECT * FROM solicitud";
    if (!empty($nombreInfractor)) {
        $sql .= " WHERE infractor LIKE '%$nombreInfractor%'";
    }
    $sql .= " ORDER BY numerodesoli DESC";  // Cambiar el campo de orden si es necesario
    $resultado = mysqli_query($db, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Solicitudes</title>
    
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

        .navbar-nav .nav-link {
            color: #d1d1d1 !important;
            font-size: 1.1rem;
        }

        .navbar-nav .nav-link:hover {
            color: #fff !important;
            background-color: #444;
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

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #007bff;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
        }

        .table td, .table th {
            vertical-align: middle;
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

        .card {
            margin-top: 20px;
        }
        .sidebar-footer {
    margin-top: auto;
    text-align: center;
    padding-bottom: 100px; /* Ajusta la separación inferior */
}


.admin-image {
    max-width: 80%; /* Ajusta el tamaño máximo al 80% del ancho del contenedor */
    border-radius: 8px; /* Opcional: bordes redondeados */
    display: block; /* Se asegura de que la imagen sea un bloque para centrarla */
    margin: 0 auto 100px; /* Añade un pequeño margen inferior */
}
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Panel Ad</a>
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

    </div>

    <!-- Contenido principal -->
    <div class="main-content">
        <!-- Botón de Cerrar Sesión -->
        <button class="logout-button" onclick="window.location.href='../controllers/cerrar.php';">Cerrar sesión</button>

        <div class="container-fluid">
            <!-- Título del dashboard -->
            <div class="dashboard-header">
                <h1>Solicitudes Registradas</h1>
            </div>

            <!-- Filtro por nombre de infractor -->
            <!-- Filtro por nombre de infractor -->
<form method="get" class="mb-4">
    <div class="row align-items-center">
        <!-- Campo de búsqueda -->
        <div class="col-md-4">
            <input type="text" name="nombre_infractor" class="form-control" placeholder="Buscar por nombre de infractor" value="<?php echo $nombreInfractor; ?>">
        </div>
        <!-- Botón Buscar -->
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
        <!-- Botón Generar Reporte -->
        <div class="col-auto ms-auto">
            <a href="fpdf/reporte.php" class="btn btn-success" target="_blank">Generar Reporte</a>
        </div>
    </div>
</form>


          
            <!-- Tabla de Solicitudes -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list-alt"></i> Lista de Solicitudes
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th># Solicitud</th>
                                <th>Asunto</th>
                                <th>Infractor</th>
                                <th>Estado</th>
                                <th>Autor</th>
                                <th>Ubicación</th>
                                <th>Fecha de Recepción</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Verificar si hay registros
                            if (mysqli_num_rows($resultado) > 0) {
                                // Mostrar cada solicitud en una fila
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['numerodesoli'] . "</td>";
                                    echo "<td>" . $row['asunto'] . "</td>";
                                    echo "<td>" . $row['infractor'] . "</td>";
                                    echo "<td>" . $row['estado'] . "</td>";
                                    echo "<td>" . $row['autor'] . "</td>";
                                    echo "<td>" . $row['ubicacion'] . "</td>";
                                    echo "<td>" . date('d-m-Y H:i:s', strtotime($row['fecha_recepcion'])) . "</td>";
                                    echo "<td>" . $row['concepto'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No se encontraron solicitudes registradas.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
</body>

</html>

<?php
// Cerrar la conexión a la base de datos
mysqli_close($db);
?>
