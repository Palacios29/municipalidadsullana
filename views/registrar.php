<?php
include '../config/database.php';  // Asegúrate de que la ruta sea correcta
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

$mensajeExito = '';  // Variable para el mensaje de éxito

// Configurar la zona horaria de Lima
date_default_timezone_set('America/Lima');

// Procesamiento del formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar que los campos necesarios no estén vacíos
    if (empty($_POST['numerodesoli']) || empty($_POST['asunto']) || empty($_POST['infractor']) || 
        empty($_POST['estado']) || empty($_POST['autor']) || empty($_POST['ubicacion']) || 
        empty($_POST['concepto']) || empty($_POST['dni'])) {
        echo "Por favor, complete todos los campos.";
    } else {
        // Recibir los datos del formulario
        $numero_solicitud = mysqli_real_escape_string($db, $_POST['numerodesoli']);  // Asegúrate de que sea una cadena
        $asunto = $_POST['asunto'];
        $infractor = $_POST['infractor'];
        $estado = $_POST['estado'];
        $autor = $_POST['autor'];
        $ubicacion = $_POST['ubicacion'];
        $fecha_recepcion = date('Y-m-d H:i:s');  // Fecha actual en formato 'YYYY-MM-DD HH:MM:SS'
        $concepto = $_POST['concepto'];
        $dni = $_POST['dni'];

        // Preparar la consulta SQL
        $sql = "INSERT INTO solicitud (numerodesoli, asunto, infractor, estado, autor, ubicacion, fecha_recepcion, concepto, dni)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Usar la conexión con la variable $db
        if ($stmt = mysqli_prepare($db, $sql)) {
            // Vincular los parámetros. El tipo para dni es 'i' (entero), y numerodesoli es ahora 's' para string.
            mysqli_stmt_bind_param($stmt, "ssssssssi", $numero_solicitud, $asunto, $infractor, $estado, $autor, $ubicacion, $fecha_recepcion, $concepto, $dni);

            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                // Mensaje de éxito con diseño destacado
                $mensajeExito = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                    <strong>¡Éxito!</strong> La solicitud fue registrada con éxito. Puedes registrar otra solicitud.
                                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                  </div>";

                // Limpiar los campos del formulario para un nuevo registro
                $_POST = array();  // Limpiar todos los valores de $_POST
            } else {
                echo "Error al registrar la solicitud: " . mysqli_error($db);
            }

            // Cerrar la declaración
            mysqli_stmt_close($stmt);
        } else {
            echo "Error al preparar la consulta: " . mysqli_error($db);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Solicitudes</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../css/registrar.css" rel="stylesheet"> <!-- Incluye el archivo CSS externo -->
   
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Panel</a>
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

    
    <!-- Main Content -->
    <div class="main-content">
        <?= $mensajeExito ?>

        <div class="container-fluid">
            <h1>Registrar Solicitudes</h1>
             <!-- Botón de Cerrar Sesión -->
        <button class="logout-button" onclick="window.location.href='../controllers/cerrar.php';">Cerrar sesión</button>

<div class="container-fluid">

            <!-- Formulario -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-edit icon"></i> Formulario de Registro
                </div>
                <div class="card-body">
                    <form action="registrar.php" method="POST">
                        <!-- Otros campos del formulario -->
                        <div class="row mb-3">
                        <div class="col-md-6">
    <label for="numerodesoli" class="form-label">Número de Solicitud</label>
    <input type="text" id="numerodesoli" name="numerodesoli" class="form-control" required>

</div>

<div class="col-md-6 d-flex">
    <div class="me-2 flex-grow-1">
        <label for="dni" class="form-label">DNI</label>
        <input 
            type="text" 
            id="dni" 
            name="dni" 
            class="form-control" 
            maxlength="8" 
            pattern="\d{8}" 
            title="Ingrese 8 datos númericos" 
            required
        >
    </div>
    <button type="button" class="btn btn-info btn-sm" id="boton">Buscar DNI</button>
</div>

<script>
    document.getElementById('dni').addEventListener('input', function (event) {
        // Permitir solo números
        this.value = this.value.replace(/[^0-9]/g, '');
        // Limitar a 8 caracteres
        if (this.value.length > 8) {
            this.value = this.value.slice(0, 8);
        }
    });
</script>

</div>

<div class="row mb-3">
    <div class="col-md-6">
    <label for="asunto" class="form-label">Asunto</label>
        <select id="asunto" name="asunto" class="form-select" required>
            <option value="Caducidad de PIT">Caducidad de PIT</option>
            <option value="Prescripción de PIT">Prescripción de PIT</option>
            <option value="Concurso de PIT">Cocurso de PIT</option>
            <option value="Desafectación de PIT">Desafectación de PIT</option>
            <option value="Nulidad de PIT">Nulidad de PIT</option>
            <option value="Copia de PIT">Copia de PIT</option>
            <option value="Nulidad de RG">Nulidad de RG</option>
        </select>
        
        
    </div>
    <div class="col-md-6">
        <label for="infractor" class="form-label">Infractor</label>
        <input type="text" id="infractor" name="infractor" class="form-control" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="estado" class="form-label">Estado</label>
        <select id="estado" name="estado" class="form-select" required>
            <option value="PENDIENTE">PENDIENTE</option>
            <option value="EN PROCESO">EN PROCESO</option>
            <option value="FINALIZADO">FINALIZADO</option>
        </select>
    </div>
    <div class="col-md-6">
        
    <label for="autor" class="form-label">Autor</label>
        <select id="autor" name="autor" class="form-select" required>
            <option value="Ana Maria Moreno">Ana Maria Moreno</option>
            <option value="Viviana Bayona Escarate">Viviana Bayona Escarate</option>
            <option value="Cristian Sanchez Nizama">Cristian Sanchez Nizama</option>
            <option value="Jose Guevara Zapata">Jose Guevara Zapata</option>
            <option value="Luis Jimenez Saavedra">Luis Jimenez Saavedra</option>
            <option value="Jose del Carmen Ramos Chero">Jose del Carmen Ramos Chero</option>
            <option value="Yasmin Paola Tadeo Salazar">Yasmin Paola Tadeo Salazar</option>
            <option value="Liset Riofrio">Liset Riofrio</option>
            <option value="Melisa Otero Reyes">Melisa Otero Reyes</option>
        </select>
    </div>
</div>

<div class="row mb-3">
<label for="ubicacion" class="form-label">Ubicacion</label>
        <select id="ubicacion" name="ubicacion" class="form-select" required>
            <option value="Oficina 1 Administrativos">Oficina 1 Administrativos</option>
            <option value="Oficina 2 Tecnicos">Oficina 2 Tecnicos</option>
            <option value="Oficina 3 SubGerencia">Oficina 3 SubGerencia</option>
           
        </select>
    </div>
    <div class="col-md-6">
        <label for="concepto" class="form-label">Observaciones</label>
        <input type="text" id="concepto" name="concepto" class="form-control" required>
    </div>
</div>


                        <button type="submit" class="btn btn-primary">Registrar Solicitud</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir Main.js -->
    <script>
        let boton = document.getElementById("boton");
        boton.addEventListener("click", traerDatos);

        function traerDatos() {
            let dni = document.getElementById("dni").value.trim();  // Elimina posibles espacios extra
            if (!dni) {
                alert("Por favor, ingrese un DNI.");
                return;
            }

            // Desactiva el botón y muestra mensaje de carga
            boton.disabled = true;
            let loadingMessage = document.createElement("span");
            loadingMessage.textContent = "Cargando...";
            document.body.appendChild(loadingMessage);

            fetch("https://apiperu.dev/api/dni/" + dni + "?api_token=1fd138e9c20b299c25169bcf566974014f173f7d11bfe655e43cc85faecb9e30")
                .then((response) => response.json())
                .then((data) => {
                    if (data.data) {
                        document.getElementById("infractor").value = data.data.nombres + " " + data.data.apellido_paterno + " " + data.data.apellido_materno;
                    } else {
                        alert("No se encontró información para el DNI ingresado.");
                    }
                })
                .catch((error) => {
                    alert("Hubo un problema al obtener la información.");
                })
                .finally(() => {
                    boton.disabled = false;
                    loadingMessage.remove();
                });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
