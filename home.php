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


include 'modificar.php';
include 'agregar.php';
include './vendor/d3-network/index.php';
?>
<div class="container-fluid pt-5  w-100">
  <div class="row mt-custom mt-5 text-center text-dark fw-bold justify-content-center align-items-center">
    <div class="col-sm-2 m-4">
      <div class="card shadow-none p-2 bg-body border-0 rounded-3 overflow-visible">
        <div class="card-body d-flex flex-column align-items-center justify-content-center overflow-visible">
          <h1 class="card-text display-4 fw-bold overflow-visible" id="numProyectos"></h1>
          <div class=" d-flex justify-content-center">
                        <div class="dropdown-center ">
                            <a class="card-text dropdown-toggle font-monospace text-black-50 text-dark fw-bold border-0 text-decoration-none text-uppercase" href="#" id="NomProyecto" data-bs-toggle="dropdown">
                                PROYECTOS
                            </a>
                            <ul class="dropdown-menu mt-1" id="proyectosDropdown">
                                <li><a class='dropdown-item' href='#' onclick='obtenerDatosProyecto("Todos")'>Todos</a></li>
                                <?php
                                //obtenerNombreProyectos() esta en funciones.php y devuelve nombre de los proyectos.
                                $proyectos_equipos = obtenerNombreProyectos();

                                while ($fila = mysqli_fetch_assoc($proyectos_equipos)) {
                                    echo "<li><a class='dropdown-item' href='#' onclick='obtenerDatosProyecto(\"" . $fila['proyecto'] . "\")'>" . $fila['proyecto'] . "</a></li>";
                                }
                                mysqli_free_result($proyectos_equipos);
                                ?>
                            </ul>
                            <!-- Archivo JS que contiene la funcion obtenerDatosProyecto() la cual se ejecuta al pulsar un "dropdown-item" -->
                            <script src="./assets/js/home.js"></script>
                        </div>
                    </div>
        </div>
      </div>
    </div>
    <div class="col-sm-2 m-4">
      <div class="card shadow-none p-2 bg-body border-0 rounded-3">
        <div class="card-body d-flex flex-column align-items-center justify-content-center">
          <h1 class="card-text display-4 fw-bold" id="numRouters"></h1>
          <p class="card-text font-monospace text-black-50">ROUTERS</p>
        </div>
      </div>
    </div>
    <div class="col-sm-2 m-4">
      <div class="card shadow-none p-2 bg-body border-0 rounded-3">
        <div class="card-body d-flex flex-column align-items-center justify-content-center">
          <h1 class="card-text display-4 fw-bold" id="numFirewalls"></h1>
          <p class="card-text font-monospace text-black-50">FIREWALLS</p>
        </div>
      </div>
    </div>
    <div class="col-sm-2 mx-4 my-4">
      <div class="card shadow-none p-2 bg-body border-0 rounded-3">
        <div class="card-body d-flex flex-column align-items-center justify-content-center">
          <h1 class="card-text display-4 fw-bold" id="numSwitches"></h1>
          <p class="card-text font-monospace text-black-50 text-uppercase">Switches</p>
        </div>
      </div>
    </div>
</div>

<div class="offcanvas offcanvas-bottom" style="height: 70vh;" tabindex="-1" id="offcanvasBottom" data-bs-backdrop="static" aria-labelledby="offcanvasBottomLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title text-wrap text-capitalize fs-3 fw-bold" id="offcanvasBottomLabel"></h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" id="closeButton"></button>
  </div>
  <div class="offcanvas-body small" id="divDiagramaRed">
    <?php $contenidoHTML = generarContenidoHTML(); echo $contenidoHTML  ?>    
  </div>
</div>

<?php

  $dispositivos = obtenerTodosDispositivos();

  echo '<div class="row">';
  echo '<div id="admin" class="col s12">';
  echo '<div class="card material-table">';
  echo '<div class="table-header">';
  //echo '<span class="table-title">Lista de Dispositivos</span>';
  echo '<div class="actions">';

  echo '<a class="btn waves-effect btn-flat nopadding modal-trigger" data-bs-toggle="offcanvas" data-bs-target="#staticBackdropAdd" aria-controls="staticBackdropAdd"><i class="material-icons">add</i></a>';
  echo '<div class="offcanvas offcanvas-bottom" style="height: 55vh;" tabindex="-1" id="staticBackdropAdd" aria-labelledby="staticBackdropLabel">
              <div class="offcanvas-header">
                  <h3 class="offcanvas-title text-wrap text-capitalize fs-3 fw-bold" id="staticBackdropLabel">Agregar dispositivo</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
              </div>
              <div class="offcanvas-body">
                  <div class="col-md-12">';
                  formulario_agregar_dispositivo();
  echo '      </div>
              </div>
          </div>';



  echo '<a href="#" class="search-toggle waves-effect btn-flat nopadding"><i class="material-icons">search</i></a>';

  echo '</div>';
  echo '</div>';

  echo '<table id="datatable" class="table table-hover">';
  echo '<thead>';
  echo '<tr>';
  echo '<th class="text-dark fw-bold">Nombre</th>';
  echo '<th class="text-dark fw-bold">Tipo</th>';
  echo '<th class="text-dark fw-bold">Proyectos</th>';
  echo '<th class="text-dark fw-bold">MAC</th>';
  echo '<th class="text-dark fw-bold">IP</th>';
  echo '<th class="text-dark fw-bold">Protocolo de acceso</th>';
  echo '<th class="text-dark fw-bold">Informacion adicional</th>';
  echo '<th class="text-dark fw-bold">Fecha de registro</th>';
  echo '<th class="text-dark fw-bold text-center">Acciones</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';

  if ($dispositivos && $dispositivos->num_rows > 0) {
      while ($fila = $dispositivos->fetch_assoc()) {
          echo '<tr>';
          echo '<td>' . htmlspecialchars($fila['nombre']) . '</td>';
          echo '<td>' . htmlspecialchars($fila['tipo']) . '</td>';
          echo '<td>' . htmlspecialchars($fila['proyecto']) . '</td>';
          
          /* echo '<td><a data-bs-toggle="modal" data-bs-target="#staticBackdrop_' . $fila['proyecto'] . '">' . htmlspecialchars($fila['proyecto']) . '</a></td>';

          echo '<!-- Modal -->
          <div class="modal fade" id="staticBackdrop_' . $fila['proyecto'] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
              <div class="modal-dialog modal-fullscreen" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLongTitle">Diagrama de Red: ' . $fila['proyecto'] . '</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                      '.$contenidoHTML = generarContenidoHTML(); echo $contenidoHTML.'
                      </div>
                  </div>
              </div>
          </div>'; */

          echo '<td>' . htmlspecialchars($fila['direccion_mac']) . '</td>';
          echo '<td>' . htmlspecialchars($fila['direccion_ip']) . '</td>';
          echo '<td>' . htmlspecialchars($fila['protocolo_acceso']) . '</td>';
          echo '<td>' . htmlspecialchars($fila['informacion_adicional']) . '</td>';
          echo '<td>' . htmlspecialchars($fila['fecha_registro']) . '</td>';
          echo '<td>';
              echo '<div class="btn-group">';
                  echo '<a class="btn btn-dark border-opacity-0 btn-sm p-1 rounded-start border-black" href="?url=deviceInfo&device=' . $fila['id'] . '"><i class="material-icons p-0 text-primary">info</i></a>';
                  
                  echo '<a class="btn btn-dark btn-sm p-1 border-black" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop_' . $fila['id'] . '" aria-controls="staticBackdrop"><i class="material-icons p-0 text-warning">edit</i></a>';
                  echo '<div class="offcanvas offcanvas-bottom" style="height: 60vh;" tabindex="-1" id="staticBackdrop_' . $fila['id'] . '" aria-labelledby="staticBackdropLabel">';
                      echo '<div class="offcanvas-header">';
                          echo '<h3 class="offcanvas-title text-wrap text-capitalize fs-3 fw-bold" id="staticBackdropLabel">Actualizar datos</h3>';
                          echo '<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
                      echo '</div>';
                      echo '<div class="offcanvas-body">';
                          echo '<div class="col-md-12">';
                              mostrarFormulario($fila['id']);
                          echo '</div>';
                      echo '</div>';
                  echo '</div>';

                  echo '<a class="btn btn-dark btn-sm p-1 rounded-end border-black" data-bs-toggle="modal" data-bs-target="#exampleModalCenter_' . $fila['id'] . '"><i class="material-icons p-0 text-danger">delete</i></a>
                      <!-- Modal -->
                      <div class="modal fade" id="exampleModalCenter_' . $fila['id'] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title fw-bold" id="exampleModalLongTitle">¿Desea eliminar este dispositivo?</h5>
                                  </div>
                                  <div class="modal-body">
                                    También se eliminarán todas sus configuraciones.
                                  </div>
                                  <div class="modal-footer text-light">
                                      <a type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">No</a>
                                      <a type="button" class="btn btn-danger" href="?url=eliminar&id=' . $fila['id'] . '">Si </a>
                                  </div>
                              </div>
                          </div>
                      </div>
                      ';
                  
                  echo '</div>';
          echo '</td>';
          echo '</tr>';
      }
  }

  echo '</tbody>';
  echo '</table>';
  echo "</div>";
  echo '</div>';
  echo '</div>';

  //$nombreProyectoTodos = "Proyecto2";
  //$cantidadDispositivosTodos = obtenerCantidadDispositivos($nombreProyectoTodos);
  
  // Imprimir resultados para "Todos" (puedes hacer lo que desees con estos valores)
?>

