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


if(isset($_SESSION['alerta'])) {
    // Abre el modal si la variable de sesión es verdadera
    mostrarAlerta($_SESSION['alerta']['tipo'], $_SESSION['alerta']['mensaje']);
    // Elimina la variable de sesión.
    unset($_SESSION['alerta']);
} 


include 'configuraciones.php';




        $idDispositivo = $_GET['device'];


        // Obtener todos los datos del dispositivo y mostrarlos.
        $DatosDelDispositivo = obtenerDatosDelDispositivo($idDispositivo);
        echo '<div class="card sticky-top shadow col-12" style="position: fixed; top: 5%;">';
            echo '<div id="card-informacion" class="card-header">';
                // Mostrar los datos del dispositivo en la página
                echo '<div class="card-body" style="height: 300px;">';
                    echo '<ul class="card-text pt-2">';
                    // El resultado de $DatosDelDispositivo son dos arrays uno dentro de otro por eso usamos [0].
                        foreach ($DatosDelDispositivo[0] as $campo => $valor) {
                            echo '<li><strong>' . $campo . ':</strong> ' . $valor . '</li>';
                        }
                        //Descargar datos del equipo.
                        echo '<br><div class="d-flex justify-content-between">
                                <a href="?url=descargarDatosEquipo&id='. urlencode($DatosDelDispositivo[0]['id']) .'">
                                    <img src="./assets/icons/cloud-download.svg" alt="Descargar">
                                </a>
                                <a class="btn waves-effect btn-flat nopadding modal-trigger" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddConfig" aria-controls="staticBackdropAdd">
                                    <i class="material-icons">add</i>
                                </a>
                        </div>';
                    
                        echo '<div class="offcanvas offcanvas-bottom" style="height: 55vh;" tabindex="-1" id="offcanvasAddConfig" aria-labelledby="staticBackdropLabel">
                                    <div class="offcanvas-header">
                                        <h3 class="offcanvas-title text-wrap text-capitalize fs-4 fw-bold" id="staticBackdropLabel">Agregar Configuración</h3>
                                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body">
                                        <div class="col-md-12">';
                                        form_agregar_config($idDispositivo);
                        echo '      </div>
                                    </div>
                                </div>';


                    echo '</ul>';
                echo '</div>';
            echo '</div>';
        echo '</div><br>';

        // Obtener las configuraciones del equipo pasando su id a la función.
        $ConfiguracionesDelDispositivo = obtenerConfiguraciones($DatosDelDispositivo[0]['id']);
        // Bucle para mostrar todas las configuraciones obtenidas de un equipo.
        foreach ($ConfiguracionesDelDispositivo as $index => $configuracion) {
            echo '<div class="accordion accordion-flush" id="accordionFlushExample" style="position: relative; top: 40%;">';
                echo '<div class="accordion-item" role="tab" id="heading' . $index . '">';
                    echo '<h2 class="accordion-header" id="flush-headingOne">';
                        echo '<button data-bs-toggle="collapse" href="#collapse' . $index . '" aria-expanded="false" aria-controls="collapse' . $index . '" class="accordion-button collapsed">';
                        echo '<a href="#" class="download-link" data-configuracion="' . htmlspecialchars($configuracion['configuracion']) . '">
                                <i class="material-icons p-1 text-secondary fs-5">cloud_download</i>
                            </a>
                            ';
                            echo '<a class="delete-button" data-bs-toggle="modal" data-bs-target="#exampleModalCenter" data-id-config="'.htmlspecialchars($configuracion['id']).'">
        <i class="material-icons p-1 text-secondary fs-5">delete</i>
      </a>';

                        // Mostrar el nombre de la configuración junto a su fecha de creación.
                            echo "&nbsp;&nbsp;" . $configuracion['fecha_creacion'] . " - " . $configuracion['nombre_configuracion'];
                        echo '</button>';
                    echo '</h2>';
                echo '</div>';

                echo '<div id="collapse' . $index . '" class="accordion-collapse collapse" role="tabpanel" aria-labelledby="heading' . $index . '">';
                    
                    echo '<pre class="accordion-body">';
                    echo $configuracion['configuracion'];
                    echo '</pre>';
                    
                echo '</div>';
            echo '</div>';
        }

        echo '
        <!-- Modal eliminar configuración -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title fw-bold" id="exampleModalLongTitle">¿Desea eliminar esta configuración?</h5>
              </div>
              <div class="modal-footer text-light">
                <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">No</button>
                <a type="button" class="btn btn-danger" href="#" id="deleteButton">Si</a>
              </div>
            </div>
          </div>
        </div>
        ';

        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var downloadLinks = document.querySelectorAll(".download-link");
            downloadLinks.forEach(function(link) {
                link.addEventListener("click", function(e) {
                    e.preventDefault();
                    var configuracion = this.getAttribute("data-configuracion");
                    descargarConfiguracion(configuracion);
                });
            });

        function descargarConfiguracion(configuracion) {
            var blob = new Blob([configuracion], { type: "text/plain" });
            var url = URL.createObjectURL(blob);
            var a = document.createElement("a");
            a.href = url;
            a.download = "Configuración - '.$DatosDelDispositivo[0]['nombre'].' - '.$DatosDelDispositivo[0]['direccion_mac'].'.txt";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        var deleteButtons = document.querySelectorAll(".delete-button");
        var deleteButton = document.getElementById("deleteButton");
        var modal = new bootstrap.Modal(document.getElementById("exampleModalCenter")); // Inicializamos el modal
    
        deleteButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                var idConfig = this.getAttribute("data-id-config");
    
                // Limpiamos los eventos clic anteriores del botón de eliminación
                deleteButton.removeEventListener("click", null);
    
                // Configuramos el nuevo evento clic para el botón "Si" del modal
                deleteButton.addEventListener("click", function () {
                    // Realizamos una petición AJAX con jQuery
                    $.ajax({
                        type: "POST",
                        url: "funciones.php",
                        data: { action: "eliminarConfig", idConfig: idConfig },
                        success: function (response) {
                            // Manejamos la respuesta después de la eliminación
                            //location.reload(true);
                            window.location.href = "' . $_SERVER['HTTP_REFERER'] . '";
                        },
                        error: function (error) {
                            console.error("Error en la petición AJAX", error);
                        }
                    });
                    // Cerramos el modal después de la petición
                    modal.hide();
                });
            });
        });
    });
</script>';

?>