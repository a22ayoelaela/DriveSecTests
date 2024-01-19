<?php
// index.php

// Iniciar sesión si no está iniciada
if (!isset($_SESSION)) {
    session_start();
}

require "./vendor/Authenticator/Authenticator.php";
include 'funciones.php';
include 'rutas.php';
  
 

if (isset($_POST['SaveQR_button'])) {
                    // Actualizar la clave secreta en la base de datos
                    if (actualizarSecretQRDeUsuario($_SESSION['auth_secret'])) {
                        $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Código de 2FA actualizado correctamente.'];
                    } else {
                        $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Error al actualizar al código de 2FA.'];
                    }
                    echo '<meta http-equiv="refresh" content="0;url=index.php">';
                    exit();
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

// Mostrar alerta cuando elCodigo 2FA no valido. Verificar si la variable de sesió existe.  
if (isset($_SESSION['verificacionCode2FA'])) { 
  //Si su valor es falso mostrar la alerta.
  if (!$_SESSION['verificacionCode2FA']) {
    // Muestra la alerta
    mostrarAlerta('danger', 'El código 2FA introducido no es válido.');
    // Elimina la variable de sesión para que no vuelva a saltar la alerta.
    unset($_SESSION['verificacionCode2FA']);
  }
}

// Verificar si la variable de sesió existe.
if (isset($_SESSION['resultCambioPass'])) { 
  //Si su valor es falso mostrar la alerta.
  if (!$_SESSION['resultCambioPass']) {
    // Muestra la alerta
    mostrarAlerta('danger', 'Error al cambiar la contraseña.');
    // Elimina la variable de sesión para que no vuelva a saltar la alerta.
    unset($_SESSION['resultCambioPass']);
  } else {
    mostrarAlerta('success', 'Constraseña actualizada correctamente.');
    unset($_SESSION['resultCambioPass']);
  }
  unset($_SESSION['verificacionCode2FA']);
}

if(isset($_SESSION['alerta'])) {
  // Abre el modal si la variable de sesión es verdadera
  mostrarAlerta($_SESSION['alerta']['tipo'], $_SESSION['alerta']['mensaje']);
  // Elimina la variable de sesión.
  unset($_SESSION['alerta']);
} 

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
  <head>
    <title>BackNet</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./assets/icons/conect.png" type="image/x-icon">

    <!-- Enlace a los archivos CSS de Bootstrap -->
    <link rel="stylesheet" href="./vendor/materialize/css/materialize.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="./vendor/bootstrap-5.3.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/table.css">
    <link rel="stylesheet" href="./assets/css/styles.css">

    
    <!--JS para tablas-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/js/materialize.min.js"></script>
    <!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js"></script>-->
    <script src="./vendor/bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/table.js"></script>
    <!-- <script src="./assets/js/script.js"></script> -->

  </head>
    <!-- Este margin para que el contenido aparezca por debajo de la navbar-->
    <body>
    <?php
    // Contiene las funciones que interactúan con la DB.
    //include 'funciones.php';
    //include 'rutas.php';

    // Al utilizar `ob_start()`, se activa el almacenamiento en búfer de salida. Luego, al requerir el archivo `index.php`, su contenido se almacenará en el búfer de salida en lugar de mostrarse en el navegador. Finalmente, `ob_end_clean()` descarta el contenido almacenado en el búfer de salida, evitando que se muestre en el navegador.
    //ob_start();
    //require './vendor/Authenticator/index.php';
    //ob_end_clean();
    ?>
    

    <nav class="navbar navbar-expand-sm bg-dark navbar-dark shadow-lg fixed-top" id="NavMenu">
        <!-- Brand/logo -->
        <a class="navbar-brand ps-3 elementosMenu" href="?url=">
            <img src="./assets/icons/conect.png" alt="logo" style="width:40px;">
        </a>

        <div class="nav-item dropdown ms-auto form-inline p-4">
            <a class="nav-link opacity-50 pb-lg-5 pe-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="material-icons pt-lg-4">settings</i>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark menu_ajustes p-0">
                <li>
                    <a class="dropdown-item d-flex align-items-center pe-5" data-bs-toggle="modal" data-bs-target="#Modal_ResetPassword">
                        <i class="material-icons">lock_reset</i>&nbsp;Contraseña
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#Modal_Reset2FA">
                        <i class="material-icons">qr_code_2</i>&nbsp;2FA Generar QR
                    </a>
                </li>
                <li>
                        <a class="dropdown-item d-flex align-items-center text-danger pe-4" data-bs-toggle="modal" data-bs-target="#Modal_logout">
                            <i class="material-icons">logout</i>&nbsp;Cerrar sesión&nbsp;&nbsp;&nbsp;
                        </a>
                </li>
            </ul>
        </div>
        
    </nav>

    <!-- Button trigger modal -->


<!-- Modal eliminar configuración -->
<div class="modal fade" id="Modal_logout" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title fw-bold" id="exampleModalLongTitle">¿Desea cerrar sesión?</h5>
              </div>
              <div class="modal-footer text-light">
                <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">No</button>
                <form action="rutas.php" method="POST">
                  <button id="logout_button" name="logout_button" type="submit" class="btn btn-danger" href="#" >Si</button>
                </form>
              </div>
            </div>
          </div>
</div>


<!-- Modals Generar nuevo QR 2FA 1º verificar Password-->
<div class="modal fade" id="Modal_Reset2FA" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Ingrese la constraseña del usuario</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="funciones.php" id="formVerifPass">
      <div class="modal-body">
        
            <input class="text-center" type="password" id="inputVerifPass" name="inputVerifPass" required>
            
      </div>
      <div class="modal-footer">
        <button id="VerifPass_button" name="formVerifPass" type="submit" class="btn btn-primary">Verificar</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Modals Generar nuevo QR 2FA 2º cambiar secret-->
<div class="modal fade" id="Modal_QR" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Nuevo Código QR 2FA</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body container">
        <?php
            $Authenticator = new Authenticator();
            $secret = $Authenticator->generateRandomSecret();
            $_SESSION['auth_secret'] = $secret;

            $qrCodeUrl = $Authenticator->getQR('BackNet', $_SESSION['auth_secret']);
            //var_dump($_SESSION['auth_secret']);
        ?>
            <div class="row justify-content-center align-items-center">
                <div class="col-md-6 text-center">
                  <img class="img-fluid pt-3" src="<?php   echo $qrCodeUrl ?>" alt="Verify this Google Authenticator">       
                </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>&nbsp;
        <form action="" method="POST">
          <button id="SaveQR_button" name="SaveQR_button" type="submit" class="btn btn-primary">Guardar</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Modals cambiar Password  1º verificar 2FA-->
<div class="modal fade" id="Modal_ResetPassword" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Ingrese El Código 2FA</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="funciones.php" id="formVerifCode">
      <div class="modal-body">
        
            <input class="text-center" type="text" id="inputVerifCode" name="inputVerifCode" required>
            
      </div>
      <div class="modal-footer">
        <button id="VerifCode_button" name="formVerifCode" type="submit" class="btn btn-primary">Verificar</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Modals cambiar Password  2º cambiar pass-->
<div id="exampleModalToggle2" class="modal fade" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalToggleLabel2">Ingrese La Nueva Contraseña</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="funciones.php" id="formChangePass">
                <div class="modal-body">
                    <input class="text-center" type="password" id="inputChangePass" name="inputChangePass" required>
                </div>
                <div class="modal-footer">
                    <button id="ChangePass_button" name="formChangePass" type="submit" class="btn btn-primary">Cambiar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!--  JavaScript para manejar la apertura del modal -->
<script>
    // Verifica la variable de sesión al cargar la página
    window.onload = function() {
        <?php
            // Comprueba si la variable de sesión existe y es verdadera
            if(isset($_SESSION['verificacionCode2FA']) && $_SESSION['verificacionCode2FA']) {
                // Abre el modal si la variable de sesión es verdadera
                echo "$('#exampleModalToggle2').modal('show');";
                // Después de mostrar el modal, elimina la variable de sesión (sino al actualizar mostrara el modal).
                unset($_SESSION['verificacionCode2FA']);
            } 
            // Comprueba si la variable de sesión "resultVerifPass" existe y es verdadera
            if(isset($_SESSION['resultVerifPass']) && $_SESSION['resultVerifPass']) {
              // Abre el modal si la variable de sesión es verdadera
              echo "$('#Modal_QR').modal('show');";
              // Después de mostrar el modal, elimina la variable de sesión (sino al actualizar mostrara el modal).
              unset($_SESSION['resultVerifPass']);
          } 
        ?>
    };
</script>



</body>
</html>