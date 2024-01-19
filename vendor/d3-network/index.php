
<?php
function generarContenidoHTML() {

    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <link rel="stylesheet" href="./vendor/d3-network/style.css">
      <link rel="icon" href="./assets/icons/conect.png" type="image/x-icon">
    </head>
    <body>
    <div id="cuerpo" style="position: absolute;">
    
    <div style="position: fixed; bottom: 0; right: 2%;">
        <div class="accordion col-2" id="accordionConexiones" style="margin-top: auto; width: 350px;">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button bg-light bg-gradient" type="button" data-bs-toggle="collapse" data-bs-target="#accordionConexiones-collapseOne" aria-expanded="true" aria-controls="accordionConexiones-collapseOne">
                        Conexiones
                    </button>
                </h2>
                <div id="accordionConexiones-collapseOne" class="accordion-collapse collapse show" style="transform-origin: 100% 100%;">
                    <div class="accordion-body">
                        <div class="list-group" id="divConexiones" style="max-height: 300px; overflow-y: auto;">
                            <!-- Contenido del list-group -->
                        </div>
                    
                        <div class="d-flex justify-content-center align-items-center pt-3">
                            <button type="button" class="btn btn-danger btn-sm" id="borrarBtn"><i class="material-icons">close</i></button>
                            <button type="button" class="btn btn-success btn-sm ms-2" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="material-icons">add</i></button>
                        </div>

                        <div class="collapse container" id="collapseExample">
                            <div class="card card-body border-0 shadow-none" style="height: 130px;">
                                <div class="row" id="selectNodos">
                                    <!-- Contenido del selectNodos -->
                                </div>
                                <button type="button" class="btn btn-success btn-sm" id="agregarBtn"><i class="material-icons">check</i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://d3js.org/d3.v3.js"></script>
    <script src="./vendor/d3-network/script.js"></script>
    </div>
    </body>
    </html>';

    return $html;
}
?>

