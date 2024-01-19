# DriveSec
Tabla: https://codepen.io/heiswayi/pen/VvpmaE

echo '<td><a href="#" data-toggle="modal-dialog" data-target="#exampleModalCenter_' . $fila['proyecto'] . '">' . htmlspecialchars($fila['proyecto']) . '</a></td>';

                    // Modal
                    echo '<!-- Modal -->
                    <div class="modal-dialog modal-fullscreen-sm-down fade" id="exampleModalCenter_' . $fila['proyecto'] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">¿Desea eliminar este dispositivo?</h5>
                                </div>
                                <div class="modal-body">
                                    Eliminar este dispositivo también eliminará todas sus configuraciones.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">No</button>
                                    <a type="button" class="btn btn-danger" href="?url=eliminar&id=' . $fila['id'] . '">Si </a>
                                </div>
                            </div>
                        </div>
                    </div>';