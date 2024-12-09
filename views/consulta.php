
<?php
session_start();

// Verificar si el usuario no está autenticado
if (empty($_SESSION["id"])) {
    header("Location: /appmuni");
    exit();
}

// Configuración de la base de datos
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

// Variables para almacenar los datos de la solicitud
$solicitud = null;
$error = '';

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el número de solicitud (numerodesoli) desde el formulario
    $numeroSolicitud = mysqli_real_escape_string($db, $_POST['expedientNumber']);

    // Consulta SQL para obtener la solicitud por numerodesoli
    $sql = "SELECT asunto, infractor, estado, autor, ubicacion, fecha_recepcion, concepto 
            FROM solicitud 
            WHERE numerodesoli = '$numeroSolicitud'";

    // Ejecutar la consulta
    $resultado = mysqli_query($db, $sql);

    // Verificar si la consulta devolvió resultados
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $solicitud = mysqli_fetch_assoc($resultado);
    } else {
        $error = 'No se encontró ninguna solicitud con ese número de solicitud.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Solicitud</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/consulta.css" rel="stylesheet"> <!-- Incluye el archivo CSS externo -->
</head>
<body>
    <!-- Botón de Volver a inicio.php -->
    <a href="inicio.php" class="btn back-button">Volver a inicio</a>

    <div class="container">
        <!-- Cabecera: Logo y Título -->
        <div class="header">
            <img src="../img/logo.png" alt="Logo GTSV">
            <h1>SubGerencia de Tránsito y Seguridad Vial – STySV</h1>
        </div>

        <!-- Contenedor para formulario y resultados (horizontales) -->
        <div class="d-flex">
            <!-- Formulario de consulta -->
            <div class="form-container">
                <h5 class="text-center">Consultar Solicitud</h5>
                <form method="POST" action="consulta.php">
                    <div class="form-group">
                        <label for="expedient-number">Número de Solicitud:</label>
                        <input type="text" class="form-control" name="expedientNumber" id="expedient-number" placeholder="Ingrese el número de solicitud" required>
                    </div>
                    <button type="submit" class="btn btn-success">Consultar</button>
                </form>
            </div>

            <!-- Contenedor de resultados -->
            <div class="results-container" id="expedient-results">
                <img src="../busqueda.png" id="search-image" class="search-image" alt="Imagen de búsqueda">
                <!-- Aquí se mostrarán los resultados cuando existan -->
                <div id="expedient-info"></div>
            </div>
        </div>
    </div>

    <footer>
        <p>Contacto: munisullana@gtsv.com | Teléfono: (073) 502730</p>
        <p>Dirección: Calle Bolivar N° 160, Sullana, Perú</p>
    </footer>

    <script>
        // Mostrar resultados y ocultar la imagen de búsqueda cuando hay resultados
        const expedientData = <?php echo json_encode($solicitud); ?>;
        const error = '<?php echo $error; ?>';
        const resultsContainer = document.getElementById('expedient-results');
        const searchImage = document.getElementById('search-image');
        const expedientInfo = document.getElementById('expedient-info');

        if (expedientData) {
            searchImage.style.display = 'none'; // Ocultar la imagen de búsqueda
            resultsContainer.style.display = 'block'; // Mostrar resultados

            // Mostrar los resultados
            expedientInfo.innerHTML = `
                <strong>Estado:</strong> ${expedientData.estado}<br>
                <strong>Asunto:</strong> ${expedientData.asunto}<br>
                <strong>Infractor:</strong> ${expedientData.infractor}<br>
                <strong>Concepto:</strong> ${expedientData.concepto}<br>
                <strong>Fecha de recepción:</strong> ${expedientData.fecha_recepcion}<br>
                <strong>Oficina encargada:</strong> ${expedientData.ubicacion}
            `;
        } else if (error) {
            searchImage.style.display = 'none'; // Ocultar la imagen de búsqueda
            resultsContainer.style.display = 'block'; // Mostrar el espacio de resultados
            expedientInfo.innerHTML = error; // Mostrar error
        }
    </script>

</body>
</html>





