<?php
$to = "victorjuliopalaciosflores@gmail.com";
$subject = "Prueba de correo desde XAMPP";
$message = "Hola, este es un mensaje de prueba.";
$headers = "From: victorjuliopalaciosflores@gmail.com";

if (mail($to, $subject, $message, $headers)) {
    echo "Correo enviado exitosamente.";
} else {
    echo "Error al enviar el correo.";
}
?>
