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

function form_agregar_config($idDispositivo) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifica el campo oculto para determinar qué formulario se envió
        if (isset($_POST['formulario'])) {
            $formulario = $_POST['formulario'];

            // Ejecuta la lógica del formulario correspondiente
            if ($formulario === 'form_agregar_config') {
                // Recuperar datos del formulario
                $idDispositivoConfig = $idDispositivo;
                $nombre_config = $_POST['nombre_config'];
                $configuracion = $_POST['configContent'];

                $resultadoAgregarConfig = agregarConfigDispositivo($idDispositivoConfig, $nombre_config, $configuracion);

                if ($resultadoAgregarConfig) {
                    // Almacena el mensaje de alerta en una sesión
                    $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Configuración agregada correctamente'];

                    // Redirige al usuario a la misma página después de procesar el formulario
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit();
                } else {
                    // Almacena el mensaje de alerta en una sesión
                    $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Error al agregar la configuración'];
                }
            }
        }
    }

    // Muestra el formulario
    echo '
    <div class="container mt-4">
        <form method="POST" action="">
            <input type="hidden" name="formulario" value="form_agregar_config">

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="nombre_config" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre_config" name="nombre_config" placeholder="Ingrese el nombre" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="configContent" class="form-label">Configuración</label>
                    <textarea class="form-control" id="configContent" name="configContent" placeholder="Ingrese el contenido de la configuración" textarea rows="10" style="height:100%;" required></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-dark ">Agregar</button>
                </div>
            </div>
        </form>
    </div>';
}



?>
