<?php
session_start();
require "Authenticator.php";
require "../../funciones.php";


$_SESSION['auth_secret'] = obtenerSecretDeUsuario($_SESSION['username']);


// #FIXME Esto no hace falta ya que se encarga de generar el QR .
$Authenticator = new Authenticator();

//var_dump($_SESSION['auth_secret']);

$qrCodeUrl = $Authenticator->getQR('BackNet', $_SESSION['auth_secret']);

if (!isset($_SESSION['failed'])) {
    $_SESSION['failed'] = false;
}

?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2FA · BackNet</title>
    <link rel="icon" href="../../assets/icons/conect.png" type="image/x-icon">

    
    <link href="../bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/sign-in.css" rel="stylesheet">

    <script src="../../assets/js/script.js"></script>
    <script src="../bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js"></script>
    

</head>

<body class="d-flex align-items-center py-4">

    <main class="form-signin w-100 m-auto">
        <form id="miFormulario" action="check.php" method="post">
            <img class="mb-5" src="../../assets/icons/conect.png" alt="BackNet">
            <h1 class="h3 mb-3 fw-normal text-center">Código 2FA</h1>

            <!--Mostrar ERROR del login-->
            <?php if ($_SESSION['failed']): ?>
                            <div class="alert alert-danger" role="alert">
                                ¡Código inválido!
                            </div>
                            <?php   
                                $_SESSION['failed'] = false;
                            ?>
            <?php endif ?>

            <div class="form-floating d-flex justify-content-center">
                <input id="codeBox1" type="number" name="code1" maxlength="1" onkeyup="onKeyUpEvent(1, event)" onfocus="onFocusEvent(1)"/>
                <input id="codeBox2" type="number" name="code2" maxlength="1" onkeyup="onKeyUpEvent(2, event)" onfocus="onFocusEvent(2)"/>
                <input id="codeBox3" type="number" name="code3" maxlength="1" onkeyup="onKeyUpEvent(3, event)" onfocus="onFocusEvent(3)"/>
                <input id="codeBox4" type="number" name="code4" maxlength="1" onkeyup="onKeyUpEvent(4, event)" onfocus="onFocusEvent(4)"/>
                <input id="codeBox5" type="number" name="code5" maxlength="1" onkeyup="onKeyUpEvent(5, event)" onfocus="onFocusEvent(5)"/>
                <input id="codeBox6" type="number" name="code6" maxlength="1" onkeyup="onKeyUpEvent(6, event)" onfocus="onFocusEvent(6)"/>
            </div>

            <br>
            <button type="submit" onclick="combinarCodigos()" class="btn w-100 btn-primary rounded" style="width: 200px;border-radius: 0px;">Verificar</button>

            <!--<img style="text-align: center;" class="img-fluid" src="<?php   echo $qrCodeUrl ?>" alt="Verify this Google Authenticator"><br><br>   -->     
            <p class="mt-5 mb-3">&copy; 2023-2024 BackNet</p>
        </form>
    </main>
</body>

</html>




