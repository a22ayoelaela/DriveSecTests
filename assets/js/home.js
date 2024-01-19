/*
    Realiza una solicitud AJAX para obtener datos relacionados con un proyecto específico.
    Parametro proyecto - El nombre del proyecto para el cual se solicitan datos o "Todos" que devuelve el total.
    Return - No devuelve un valor directamente, pero actualiza la página con los datos obtenidos.
*/

// Se ejecuta esta función cuando la página home.php se carga se carga por primera vez para que muestre los datos de "Todos".
document.addEventListener("DOMContentLoaded", function() {
    obtenerDatosProyecto("Todos");
});

function obtenerDatosProyecto(proyecto) {
    // Realizar una solicitud AJAX para ejecutar la función PHP obtenerCantidadDispositivos() de funciones.php para los obtener datos.
    $.ajax({
        type: 'POST',
        url: 'funciones.php',
        data: {proyecto: proyecto},
        success: function (response) {
            // Manipular los datos devueltos del servidor y convertirlos en JSON, ya que el return es un "string".
            var datosProyecto = JSON.parse(response);

            // Button de diagrama de red que aparece cuando se selecciona un proyecto. onclick="obtenerNodos(\'' + proyecto + '\')"
            var buttonDiagramaRed = '<a class="btn waves-effect btn-flat border-0 my-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom"><img src="./assets/icons/network.png" alt="RED" width="25" height="25"></a>';

            // Actualizar la página con los datos devueltos.
            // Si es todos devolvemos contenido diferente.
            if (proyecto === "Todos") {
                $("#NomProyecto").text("Proyectos");
                $("#numProyectos").text(datosProyecto.total_proyectos);
            } else {
                $("#NomProyecto").text(proyecto);
                $("#numProyectos").html(buttonDiagramaRed);
            }
            $("#numRouters").text(datosProyecto.total_routers);
            $("#numFirewalls").text(datosProyecto.total_firewalls);
            $("#numSwitches").text(datosProyecto.total_switches);

            // Un mensaje en la consola indicando que el proyecto ha sido actualizado.
            //console.log("Proyecto actualizado");

            // Recargar la pagina.
            // location.reload();
        },
        error: function (error) {
            // Manejar errores de la solicitud AJAX e imprimir detalles en la consola
            console.error('Error en la solicitud AJAX: ', error);
        }
    });

    // Esta parte nos filtra el contenido de la tabla según el proyecto seleccionado.
    if (proyecto !== "Todos") {
        $(document).ready(function() {
            $('#datatable').DataTable({
              destroy: true, // Destruye la instancia existente, si existe
              "oLanguage": {
                "sStripClasses": "",
                "sSearch": "",
                "sSearchPlaceholder": "Filtrar resultados...",
                "sInfo": "_START_ -_END_ of _TOTAL_",
                "sLengthMenu": '<span>Filas por página:</span><select class="browser-default">' +
                  '<option value="10">10</option>' +
                  '<option value="20">20</option>' +
                  '<option value="30">30</option>' +
                  '<option value="40">40</option>' +
                  '<option value="50">50</option>' +
                  '<option value="-1">All</option>' +
                  '</select></div>'
              },
              bAutoWidth: false,
              searchCols: [
                null, 
                null,
                {search: proyecto},
                null,
                null,
                null,
                null,
                null,
                null
              ],
              autoWidth: false, // Deshabilita el ajuste automático de ancho
              
            });
          });
    } else {
        $(document).ready(function() {
            $('#datatable').DataTable({
              destroy: true, // Destruye la instancia existente, si existe
              "oLanguage": {
                "sStripClasses": "",
                "sSearch": "",
                "sSearchPlaceholder": "Filtrar resultados...",
                "sInfo": "_START_ -_END_ of _TOTAL_",
                "sLengthMenu": '<span>Filas por página:</span><select class="browser-default">' +
                  '<option value="10">10</option>' +
                  '<option value="20">20</option>' +
                  '<option value="30">30</option>' +
                  '<option value="40">40</option>' +
                  '<option value="50">50</option>' +
                  '<option value="-1">All</option>' +
                  '</select></div>'
              },
              bAutoWidth: false,
              autoWidth: false, // Deshabilita el ajuste automático de ancho
            });
          });
    };
    
      

      
}
