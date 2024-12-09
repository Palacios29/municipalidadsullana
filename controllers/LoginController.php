<?php
require_once 'models/UserModel.php';  // Ajusta la ruta si es necesario

class LoginController {
    private $model;

    // Constructor recibe la conexión a la base de datos
    public function __construct($db) {
        $this->model = new UserModel($db);
    }

    // Función que maneja el login
    public function login() {
        session_start();  // Iniciamos la sesión

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Recibimos los datos del formulario
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Lógica personalizada para redirigir cuando el usuario es "GTSV" y la contraseña es "123"
            if ($username === "GTSV" && $password === "123") {
                $_SESSION["id"] = 1;  // Asignamos una ID ficticia a la sesión (puedes usar cualquier valor)
                header("Location: views/paneladmin.php");  // Redirigimos a la página de registrar
                exit();
            }

            // Si no es "GTSV" con contraseña "123", usamos el modelo para verificar la autenticación
            $isLoggedIn = $this->model->checkLogin($username, $password);

            // Preparamos el mensaje para la vista
            if ($isLoggedIn) {
                $_SESSION["id"] = $username;  // Puedes guardar más información en la sesión si es necesario
                // Si el login es exitoso, redirigimos al usuario
                header("Location: views/inicio.php");  // Redirige a registrar.php
                exit();
            } else {
                // Si las credenciales son incorrectas, mostramos el error
                $message = [
                    'type' => 'text-danger',
                    'content' => 'Nombre de usuario o contraseña incorrectos.'
                ];
            }
        }

        // Cargamos la vista y pasamos el mensaje (si existe)
        include 'views/login.php';
    }
}
?>