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

    function mostrarFormulario($id_dispositivo) {
    
    $DatosDelDispositivo = obtenerDatosDelDispositivo($id_dispositivo);
    
    echo '
            <div class="container  mt-1">
            <form method="POST" action="">
            <div class="row">
                 <input type="hidden" name="formulario" value="formulario_modificar">

                <div class="mb-3">
                    <!-- ID -->
                    <label for="id" class="form-label">ID</label>
                    <input type="text" class="form-control ps-3" id="id-visible" name="id-visible" value="'.$id_dispositivo.'" disabled>
                    <input class="form-control visually-hidden" id="id" name="id" value="'.$id_dispositivo.'">
                </div>

                <div class="col-md-4 mb-3">
                    <!-- Proyecto -->
                    <label for="proyecto" class="form-label">Proyecto</label>
                    <input type="text" class="form-control" id="proyecto" name="proyecto" placeholder="Ingrese el proyecto">
                </div>

                <div class="col-md-4 mb-3">
                    <!-- Nombre -->
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese el nombre">
                </div>

                <div class="col-md-4 mb-3">
                    <!-- Tipo - Lista desplegable -->
                    <label for="tipo" class="form-label">Tipo</label>
                    <select class="form-select" id="tipo" name="tipo" REQUIRED>
                        <option value="" disabled selected>Selecciona el tipo</option>
                        <option value="Firewall">Firewall</option>
                        <option value="Router">Router</option>
                        <option value="Switch">Switch</option>
                    </select>
                </div>

            </div>

            <div class="row">

                <div class="col-md-4 mb-3">
                    <!-- Dirección MAC -->
                    <label for="direccion_mac" class="form-label">Dirección MAC</label>
                    <input type="text" class="form-control" id="direccion_mac" name="direccion_mac" placeholder="Ingrese la dirección MAC">
                </div>

                <div class="col-md-4 mb-3">
                    <!-- Dirección IP -->
                    <label for="direccion_ip" class="form-label">Dirección IP</label>
                    <input type="text" class="form-control" id="direccion_ip" name="direccion_ip" placeholder="Ingrese la dirección IP">
                </div>

                <div class="col-md-4 mb-3">
                    <!-- Protocolo de Acceso -->
                    <label for="protocolo_acceso" class="form-label">Protocolo de Acceso</label>
                    <input type="text" class="form-control" id="protocolo_acceso" name="protocolo_acceso" placeholder="Ingrese el protocolo de acceso">
                </div>
            </div>


            <div class="row">
                <div class="col-md-4 mb-3">
                    <!-- Ubicación -->
                    <label for="ubicacion" class="form-label">Ubicación</label>
                    <input type="text" class="form-control" id="ubicacion" name="ubicacion" placeholder="Ingrese la ubicación del equipo">
                </div>
                <div class="col-md-8 mb-3">
                    <!-- Información Adicional -->
                    <label for="informacion_adicional" class="form-label">Información Adicional</label>
                    <textarea class="form-control" id="informacion_adicional" name="informacion_adicional" rows="3" placeholder="Ingrese información adicional"></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12"> 
                    <p>* Solo debe rellenar los campos que desea modificar.</p>
                    <button type="submit" class="btn btn-dark ">Actualizar</button>
                </div>
            </div>
            </form>
        </div>


    ';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifica el campo oculto para determinar qué formulario se envió
        if (isset($_POST['formulario'])) {
            $formulario = $_POST['formulario'];
            // Limpiar cualquier salida de búfer anterior
            ob_clean();
            // Ejecuta la lógica del formulario correspondiente
            if ($formulario === 'formulario_modificar') {
                $proyecto = $_POST['proyecto'];
                $nombre = $_POST['nombre'];
                $tipo = $_POST['tipo'];
                $direccion_mac = $_POST['direccion_mac'];
                $direccion_ip = $_POST['direccion_ip'];
                $protocolo_acceso = $_POST['protocolo_acceso'];
                $ubicacion = $_POST['ubicacion'];
                $informacion_adicional = $_POST['informacion_adicional'];
    
    
                $resultadoModificacion = modificarDispositivo($_POST['id'], $proyecto, $nombre, $tipo, $direccion_mac, $direccion_ip, $protocolo_acceso, $ubicacion, $informacion_adicional);
                
                if ($resultadoModificacion) {
                    mostrarAlerta('success', 'Datos actualizados correctamente');
                } else {
                    mostrarAlerta('danger', 'Error al actualizar los datos');
                }
                
            }
        }
    }
?>