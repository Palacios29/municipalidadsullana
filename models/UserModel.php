<?php
class UserModel {
    private $db;

    // Constructor recibe la conexión a la base de datos
    public function __construct($db) {
        $this->db = $db;
    }

    // Función para verificar las credenciales de login
    public function checkLogin($username, $password) {
        // Sanear los inputs para evitar inyecciones SQL
        $username = mysqli_real_escape_string($this->db, $username);
        $password = mysqli_real_escape_string($this->db, $password);

        // Query SQL para comprobar el usuario y la contraseña
        $query = "SELECT * FROM login WHERE usuario = '$username' AND contraseña = '$password'";

        // Ejecutamos la consulta
        $result = mysqli_query($this->db, $query);

        // Si se encontró un usuario con esos datos
        if (mysqli_num_rows($result) > 0) {
            return true;
        }
        return false;
    }
}
?>
 