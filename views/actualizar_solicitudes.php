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
$filter = "";
if (isset($_POST['buscar']) && !empty($_POST['numerodesoli'])) {
    $numerodesoli = mysqli_real_escape_string($db, $_POST['numerodesoli']);
    $filter = " WHERE numerodesoli LIKE '%$numerodesoli%'";
}

// Consultar las solicitudes con el filtro aplicado
$solicitudesQuery = "SELECT * FROM solicitud" . $filter;
$solicitudesResult = mysqli_query($db, $solicitudesQuery);

// Editar solicitud
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $numerodesoli = $_POST['numerodesoli'];
    $asunto = $_POST['asunto'];
    $infractor = $_POST['infractor'];
    $autor = $_POST['autor'];
    $ubicacion = $_POST['ubicacion'];
    $concepto = $_POST['concepto'];
    $estado = $_POST['estado'];

// Obtener la fecha y hora actual en Perú (zona horaria de Lima)
date_default_timezone_set('America/Lima');
$fecha_actualizacion = date('Y-m-d H:i:s');  // Formato 'YYYY-MM-DD HH:MM:SS'

// Actualizar la solicitud en la base de datos
$updateQuery = "UPDATE solicitud SET 
                    numerodesoli='$numerodesoli', 
                    asunto='$asunto',
                    infractor='$infractor',
                    autor='$autor',
                    ubicacion='$ubicacion',
                    concepto='$concepto',
                    estado='$estado', 
                    fecha_actualizacion='$fecha_actualizacion' 
                WHERE id=$id";
mysqli_query($db, $updateQuery);

    header("Location: actualizar_solicitudes.php");
    exit();
}

// Eliminar solicitud
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    // Eliminar la solicitud de la base de datos
    $deleteQuery = "DELETE FROM solicitud WHERE id=$id";
    mysqli_query($db, $deleteQuery);
    header("Location: actualizar_solicitudes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Solicitudes</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../css/actualizar_solicitudes.css" rel="stylesheet"> <!-- Incluye el archivo CSS externo -->
   
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

    <div class="main-content">
        <h1>Actualizar Solicitudes</h1>
        <form method="POST" class="search-form mb-4">
            <input type="text" name="numerodesoli" class="form-control d-inline w-50" placeholder="Buscar por Número de Solicitud" required>
            <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
        </form>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-list-alt"></i> Lista de Solicitudes
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Número de Solicitud</th>
                            <th>Fecha de Actualización</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($solicitud = mysqli_fetch_assoc($solicitudesResult)) { ?>
                        <tr>
                            <td><?php echo $solicitud['numerodesoli']; ?></td>
                            <td><?php echo $solicitud['fecha_actualizacion']; ?></td>
                            <td><?php echo $solicitud['estado']; ?></td>
                            <td>
                                <!-- Botón Editar -->
                                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#editarModal<?php echo $solicitud['id']; ?>">Editar</button>
                                <!-- Modal para Editar -->
                                <div class="modal fade" id="editarModal<?php echo $solicitud['id']; ?>" tabindex="-1">
                                    <form method="POST">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Solicitud</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?php echo $solicitud['id']; ?>">
                                                    <div class="mb-3">
                                                        <label for="numerodesoli" class="form-label">numero de solicitud</label>
                                                        <input type="text" name="numerodesoli" class="form-control" value="<?php echo $solicitud['numerodesoli']; ?>" required>
                                                    </div>
                                                    
                                                         <div class="mb-3">
                                                        <label for="asunto" class="form-label">Asunto</label>
                                                        <select name="asunto" class="form-select">
                                                            <option value="Caducidad de PIT" <?php if ($solicitud['asunto'] == 'Caducidad de PIT') echo 'selected'; ?>>Caducidad de PIT</option>
                                                            <option value="Prescripción de PIT" <?php if ($solicitud['asunto'] == 'Prescripción de PIT') echo 'selected'; ?>>Prescripción de PIT</option>
                                                            <option value="Concurso de PIT" <?php if ($solicitud['asunto'] == 'Concurso de PIT') echo 'selected'; ?>>Concurso de PIT</option>
                                                            <option value="Desafectación de PIT" <?php if ($solicitud['asunto'] == 'Desafectación de PIT') echo 'selected'; ?>>Desafectación de PIT</option>
                                                            <option value="Nulidad de PIT" <?php if ($solicitud['asunto'] == 'Nulidad de PIT') echo 'selected'; ?>>Nulidad de PIT</option>
                                                            <option value="Copia de PIT" <?php if ($solicitud['asunto'] == 'Copia de PIT') echo 'selected'; ?>>Copia de PIT</option>
                                                            <option value="Nulidad de RG" <?php if ($solicitud['asunto'] == 'Nulidad de RG') echo 'selected'; ?>>Nulidad de RG</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="infractor" class="form-label">Infractor</label>
                                                        <input type="text" name="infractor" class="form-control" value="<?php echo $solicitud['infractor']; ?>" required>
                                                    </div>


                                                 
                                                    <div class="mb-3">
                                                        <label for="autor" class="form-label">Autor</label>
                                                        <select name="autor" class="form-select">
                                                            <option value="Ana Maria Moreno" <?php if ($solicitud['autor'] == 'Ana Maria Moreno') echo 'selected'; ?>>Ana Maria Moreno</option>
                                                            <option value="Viviana Bayona Escarate" <?php if ($solicitud['autor'] == 'Viviana Bayona Escarate') echo 'selected'; ?>>Viviana Bayona Escarate</option>
                                                            <option value="Cristian Sanchez Nizama" <?php if ($solicitud['autor'] == 'Cristian Sanchez Nizama') echo 'selected'; ?>>Cristian Sanchez Nizama</option>
                                                            <option value="Jose Guevara Zapata" <?php if ($solicitud['autor'] == 'Jose Guevara Zapata') echo 'selected'; ?>>Jose Guevara Zapata</option>
                                                            <option value="Luis Jimenez Saavedra" <?php if ($solicitud['autor'] == 'JLuis Jimenez Saavedra') echo 'selected'; ?>>Luis Jimenez Saavedra</option>
                                                            <option value="Jose del Carmen Ramos Chero" <?php if ($solicitud['autor'] == 'Jose del Carmen Ramos Chero') echo 'selected'; ?>>Jose del Carmen Ramos Chero</option>
                                                            <option value="Yasmin Paola Tadeo Salazar" <?php if ($solicitud['autor'] == 'Yasmin Paola Tadeo Salazar') echo 'selected'; ?>>Yasmin Paola Tadeo Salazar</option>
                                                            <option value="Liset Riofrio" <?php if ($solicitud['autor'] == 'Liset Riofrio') echo 'selected'; ?>>Liset Riofrio</option>
                                                            <option value="Melisa Otero Reyes" <?php if ($solicitud['autor'] == 'Melisa Otero Reyes') echo 'selected'; ?>>Melisa Otero Reyes</option>

                                                        </select>
                                                   
                                                    </div>



                                                    <div class="mb-3">
                                                        <label for="ubicacion" class="form-label">Ubicación</label>
                                                        <select name="ubicacion" class="form-select">
                                                            <option value="Oficina 1 Administrativos" <?php if ($solicitud['ubicacion'] == 'ubi1') echo 'selected'; ?>>Oficina 1 Administrativos</option>
                                                            <option value="Oficina 2 Tecnicos" <?php if ($solicitud['ubicacion'] == 'Oficina 2 Tecnicos') echo 'selected'; ?>>Oficina 2 Tecnicos</option>
                                                            <option value="Oficina 3 SubGerencia" <?php if ($solicitud['ubicacion'] == 'Oficina 3 SubGerencia') echo 'selected'; ?>>Oficina 3 SubGerencia</option>


                                                        </select>
                                                                                                        </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="estado" class="form-label">Estado</label>
                                                        <select name="estado" class="form-select">
                                                            <option value="PENDIENTE" <?php if ($solicitud['estado'] == 'PENDIENTE') echo 'selected'; ?>>Pendiente</option>
                                                            <option value="EN PROCESO" <?php if ($solicitud['estado'] == 'EN PROCESO') echo 'selected'; ?>>En Proceso</option>
                                                            <option value="FINALIZADO" <?php if ($solicitud['estado'] == 'FINALIZADO') echo 'selected'; ?>>Finalizado</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="concepto" class="form-label">Observaciones</label>
                                                        <input type="text" name="concepto" class="form-control" value="<?php echo $solicitud['concepto']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="editar" class="btn btn-success">Guardar Cambios</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Botón Eliminar -->
                                <!-- Botón Eliminar -->
<button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmarEliminarModal<?php echo $solicitud['id']; ?>">Eliminar</button>

<!-- Modal de Confirmación para Eliminar -->
<div class="modal fade" id="confirmarEliminarModal<?php echo $solicitud['id']; ?>" tabindex="-1" aria-labelledby="confirmarEliminarModalLabel<?php echo $solicitud['id']; ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarEliminarModalLabel<?php echo $solicitud['id']; ?>">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Seguro que quieres eliminar esta solicitud?
            </div>
            <div class="modal-footer">
                <!-- Formulario para eliminar solicitud -->
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $solicitud['id']; ?>">
                    <button type="submit" name="eliminar" class="btn btn-danger">Eliminar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
</body>

</html>