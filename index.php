<?php
// Incluir la configuración de la base de datos
require_once 'config/database.php';

// Comprobar si se ha enviado el controlador y la acción
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'login';
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Incluir el controlador correspondiente
if ($controller == 'login') {
    require_once 'controllers/LoginController.php';
    $controllerObj = new LoginController($db);
    $controllerObj->$action();
} else {
    // Si no se encuentra el controlador, redirigir al login
    header("Location: ?controller=login&action=login");
}
?>