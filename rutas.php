<?php

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

require_once 'funciones.php';

// Cerrar sesión si se ha enviado el formulario de cierre de sesión
if (isset($_POST['logout_button'])) {
    // Destruir todas las variables de sesión
    session_unset();
  
    // Destruir la sesión
    session_destroy(); 
  
    // Establecer el tiempo de expiración de la cookie de sesión en el pasado
    setcookie(session_name(), '', time() - 3600, '/');
  
    // Redirigir a la página de inicio de sesión u otra página después de cerrar sesión
    header('location: login.php');
    exit();
}

if ($_SERVER["QUERY_STRING"]=='') {
    include 'home.php';
}

if (isset($_GET['alert'])) {
    mostrarAlerta();
}

if (isset($_GET['opcion'])) {
    $opcion = $_GET['opcion'];
    $cantidadDispositivosTodos = obtenerCantidadDispositivos($opcion);
}
    
if (isset($_GET['url'])) {
    if ($_GET['url']=='agregar') {
        include 'agregar.php';
    } elseif ($_GET['url']=='modificar') {
        include 'modificar.php';
    } elseif ($_GET['url']=='eliminar') {
        include 'eliminar.php';
    } elseif ($_GET['url']=='descargarDatosEquipo') { 
        descargarDatosDispositivo($_GET['id']);
    } elseif ($_GET['url']=='deviceInfo') {
        include 'device_details.php';
    } else {
        include 'home.php';
    }
}
?>