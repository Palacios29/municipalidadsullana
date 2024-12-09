<?php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario y protegerlos contra inyecciones SQL
    $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
    $correo = mysqli_real_escape_string($db, $_POST['correo']);
    $queja = mysqli_real_escape_string($db, $_POST['queja']);
    
    // Insertar la queja en la base de datos usando una sentencia preparada
    $sql = "INSERT INTO foro (nombre, correo, queja) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($db, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $nombre, $correo, $queja);
        
        if (mysqli_stmt_execute($stmt)) {
            // Enviar correo electrónico
            $destinatario = "foromunisullana@gmail.com";
            $asunto = "Nuevo comentario recibido";
            $mensaje = "Has recibido un nuevo comentario:\n\n" .
                       "Nombre: $nombre\n" .
                       "Correo: $correo\n" .
                       "Comentario: $queja\n" .
                       "Fecha: " . date('Y-m-d H:i:s');
            $cabeceras = "From: no-reply@tu-dominio.com";

            if (mail($destinatario, $asunto, $mensaje, $cabeceras)) {
                $success = "Comentario enviado y almacenado con éxito.";
            } else {
                $error = "El comentario fue almacenada, pero no se pudo enviar el correo.";
            }
        } else {
            $error = "Error al almacenar el comentario: " . mysqli_error($db);
        }

        mysqli_stmt_close($stmt);
    } else {
        $error = "Error en la preparación de la consulta SQL.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro de Quejas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilo personalizado para centrar el formulario en el medio de la pantalla */
        body {
            background-image: url('../img/fondoreclamo.png');
            background-size: cover;  /* La imagen de fondo cubre toda la pantalla */
            background-position: center;
            background-repeat: no-repeat;
        }

        .forum-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            border: 2px solid #ccc;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.8); /* Fondo blanco semi-transparente */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .forum-container h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }
        .forum-container .alert {
            margin-bottom: 20px;
        }

        /* Estilos para la imagen de reclamo fuera del formulario */
        .reclamo {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 550px;  /* Aumentamos el tamaño de la imagen */
            height: auto;
            z-index: 9999;  /* Asegúrate de que la imagen quede por encima de otros elementos */
        }
        .back-button {
    width: 180px;
    padding: 14px;
    background-color: #007bff;
    color: white;
    border: 1px solid #0056b3;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: absolute;
    top: 50px;
    right: 20px;
    z-index: 1000;
}

.back-button:hover {
    background-color: #0056b3;
    border-color: #004085;
}
    </style>
</head>
<body>
<a href="inicio.php" class="btn back-button">Volver a inicio</a>

<div class="container mt-5">
    <div class="forum-container">
        <h1 class="text-center">Foro de Comentarios</h1>
        
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <div class="mb-3">
                <label for="queja" class="form-label">Dejanos un comentario</label>
                <textarea class="form-control" id="queja" name="queja" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
</div>

<!-- Imagen de reclamo fuera del formulario, en la esquina inferior derecha -->
<img src="../img/reclamo1.png" alt="Imagen de Reclamo" class="reclamo">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

