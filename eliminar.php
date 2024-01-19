<?php
// index.php

if (!isset($_SESSION)) {
    session_start();
}


// Si la sesión está iniciada, mostrar el contenido de index.php
if (isset($_SESSION['username'])) {
    // Puedes incluir aquí el contenido específico de index.php
    // echo "Bienvenido, ".$_SESSION['username']."<br>";
} else {
    // Si no, redirigir al formulario de inicio de sesión
    header('location: login.php');
    exit();
}
?>

<?php 
    $resultadoEliminacion = eliminarDespositivoByID($_GET['id']);

    if ($resultadoEliminacion) {
        mostrarAlerta('success', 'Dispositivo eliminado correctamente');
    } else {
        mostrarAlerta('danger', 'Error al eliminar el dispositivo');
    }

    require 'home.php';
?>