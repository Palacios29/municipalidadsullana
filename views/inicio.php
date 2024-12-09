<?php
session_start();

// Verificar si el usuario no está autenticado
if (empty($_SESSION["id"])) {
    header("Location: /appmuni");
    exit();
}

// Agregar lógica para cerrar sesión
if (isset($_GET['logout'])) {
    session_destroy();  // Destruir la sesión
    header("Location: /appmuni");  // Redirigir al inicio o página de login
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/inicio4.css" rel="stylesheet"> <!-- Archivo CSS externo -->
</head>

<body>
<!-- Barra lateral -->
<div class="sidebar">
        <h3 class="text-center text-white">| STySV |</h3>
        <a href=""></a>
        <a href="consulta.php">Consultar solicitud</a>
        <a href="foro.php">Dejar un comentario</a>
        <a href="https://munisullana.gob.pe/consultas_online/infracciones.php">Pagar papeleta</a>
        <a href="https://www.gob.pe/institucion/munisullana/institucional">Sobre nosotros</a>
        
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <div class="header text-center">
            <img src="../img/logo.png" alt="Logo GTSV">
            <h1>SubGerencia de Tránsito y Seguridad Vial – MPS</h1>
        </div>
        <p class="text-center">La Subgerencia de Tránsito y Seguridad Vial se encarga de dirigir, programar, coordinar y ejecutar una amplia gama de acciones relacionadas con el tránsito</p>

        <div class="faq">
            <h5>Preguntas Frecuentes</h5>
            <div>
                <h6 class="faq-question">1. ¿Dónde puedo pagar mi papeleta?</h6>
                <p class="answer">Acércate a oficina de Gerencia con el Sr. Jose Flores para que te genere código y puedas pagar tu papeleta.</p>
            </div>
            <div>
                <h6 class="faq-question">2. ¿Cómo saber si tengo papeletas?</h6>
                <p class="answer">Acércate a oficina de sub Gerencia con el Sr. Luis Davila para que te brinde esa información.</p>
            </div>
            <div>
                <h6 class="faq-question">3. ¿Con quien puedo comunicarme para hacer un reclamo sobre papeleta impuesta?</h6>
                <p class="answer">Presenta tu solicitud detallada en oficina de sub Gerencia de transito con la Sra. Yasmin Tadeo.</p>
            </div>
        </div>

        <button id="consult-button" class="btn btn-primary mt-4">CONSULTAR ESTADO DE SOLICITUD</button>

        <!-- Botón "FORO DE QUEJAS" agregado -->
        <button id="forum-button" class="btn btn-secondary mt-4">FORO DE QUEJAS</button>

        <div class="form-container" id="form-container">
            <h5 class="mt-4">Consulta de Solicitud</h5>
            <form id="expedient-form">
                <div class="form-group">
                    <label for="expedient-number">Número de Solicitud:</label>
                    <input type="text" class="form-control" id="expedient-number" placeholder="Ingrese el número de solicitud" required>
                </div>
                <button type="submit" class="btn btn-success">Consultar</button>
            </form>
            <div id="expedient-data" class="mt-4" style="display: none;"></div>
        </div>
    </div>

    <img src="../img/footer.png" alt="Footer Image" class="bottom-image">

    <!-- Botón para cerrar sesión -->
    <button class="logout-button" onclick="window.location.href='?logout=true';">Cerrar sesión</button>

    <footer>
        <p>Contacto: munisullana@gtsv.com | Teléfono: (073) 502730</p>
        <p>Dirección: Calle Bolívar N° 160, Sullana, Perú</p>
    </footer>

    <script>
        // Redirigir al hacer clic en el botón de "Consultar Estado de Solicitud"
        document.getElementById('consult-button').addEventListener('click', function() {
            window.location.href = 'consulta.php'; // Redirige a consulta.php
        });

        // Redirigir al hacer clic en el botón de "FORO DE QUEJAS"
        document.getElementById('forum-button').addEventListener('click', function() {
            window.location.href = 'foro.php'; // Redirige a foro.php
        });

        // Redirigir al enviar el formulario de consulta
        document.getElementById('expedient-form').onsubmit = function(event) {
            event.preventDefault();  // Evita el comportamiento por defecto del formulario

            // Redirige a la página 'consulta.php' después de procesar el formulario
            window.location.href = 'consulta.php';
        };

        // Manejar el despliegue de respuestas con animación
        const faqQuestions = document.querySelectorAll('.faq-question');
        faqQuestions.forEach(function(question) {
            question.addEventListener('click', function() {
                const answer = this.nextElementSibling;
                const isAnswerVisible = answer.style.maxHeight !== '0px';

                // Ocultar todas las respuestas
                document.querySelectorAll('.answer').forEach(function(ans) {
                    ans.style.maxHeight = '0';
                    ans.style.opacity = '0';
                });

                // Si la respuesta no estaba visible, mostrarla con animación
                if (!isAnswerVisible) {
                    answer.style.maxHeight = '500px';
                    answer.style.opacity = '1';
                }
            });
        });
    </script>
</body>
</html>
