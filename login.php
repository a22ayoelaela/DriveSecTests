<?php
// Iniciar sesión
if (!isset($_SESSION)) {
    session_start();
}

// Conexión a la base de datos
$db = mysqli_connect('localhost', 'root', '', 'db_dispositivos_red');

$errors = [];

// Si se ha enviado el formulario
if (isset($_POST['login_button'])) {
    // Verificar la respuesta del CAPTCHA
    if (isset($_POST['cf-turnstile-response'])) {
        $captchaResponse = $_POST['cf-turnstile-response'];

        // Verificar la respuesta del CAPTCHA utilizando la API de Cloudflare
        $ch = curl_init("https://challenges.cloudflare.com/turnstile/v0/siteverify");

        // Configurar opciones para la solicitud POST
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'secret' => '0x4AAAAAAAPJ9zkniw5pvrUQ0fD8S8UwJQs', // Reemplaza con tu clave secreta del sitio
            'response' => $captchaResponse,
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        // Manejo de errores de cURL
        if ($response === false) {
            die("Error en la solicitud cURL: " . curl_error($ch));
        }

        // Cierra la sesión cURL
        curl_close($ch);

        // Continuar con el manejo de la respuesta
        $responseData = json_decode($response, true);

        // Manejo de errores de json_decode
        if ($responseData === null) {
            // Mostrar un mensaje de error o lograr la respuesta completa para su análisis
            die("Error al decodificar la respuesta JSON. Respuesta completa: $response");
        }

        if ($responseData['success']) {
            // El CAPTCHA se ha verificado correctamente
            // Continuar con la verificación de las credenciales del usuario
            $username = mysqli_real_escape_string($db, $_POST['username']);
            $password = mysqli_real_escape_string($db, $_POST['password']);

            // Validación y Sanitización
            if (empty($username) || empty($password)) {
                $errors[] = "Por favor, complete todos los campos.";
            } else {
                // Comprobar si el nombre de usuario es válido
                $query = "SELECT * FROM users WHERE username='$username'";
                $results = mysqli_query($db, $query);

                if (mysqli_num_rows($results) == 1) {
                    // Nombre de usuario válido, verificar contraseña
                    $row = mysqli_fetch_assoc($results);
                    if (password_verify($password, $row['password'])) {
                        // Inicio de sesión válido
                        $_SESSION['username'] = $username;
                        header('location: ./vendor/Authenticator/index.php');
                        exit(); // Asegurar que se detenga la ejecución después de redirigir
                    } else {
                        // Contraseña inválida
                        $errors[] = "Usuario/contraseña inválidos.";
                    }
                } else {
                    // Nombre de usuario inválido
                    $errors[] = "Usuario/contraseña inválidos.";
                }
            }
        } else {
            // El CAPTCHA no se ha verificado correctamente
            // Mostrar un mensaje de error o redirigir al usuario
            $errors[] = "Por favor, complete el CAPTCHA correctamente.";
        }
    } else {
        // No se ha proporcionado una respuesta de CAPTCHA
        // Mostrar un mensaje de error o redirigir al usuario
        $errors[] = "Por favor, complete el CAPTCHA correctamente.";
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signin · BackNet</title>
    <link rel="icon" href="./assets/icons/conect.png" type="image/x-icon">

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script src="./vendor/bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js"></script>

    <link href="./vendor/bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/sign-in.css" rel="stylesheet">
</head>

<body class="d-flex align-items-center py-4">
    <main class="form-signin w-100 m-auto">
        <form action="login.php" method="POST" autocomplete="off">
            <img class="mb-5" src="./assets/icons/conect.png" alt="BackNet">
            <h1 class="h3 mb-3 fw-normal">Iniciar sesión</h1>

            <!--Mostrar ERROR del login-->
            <?php
            if (count($errors) > 0) {
                echo "<div class='alert alert-danger' role='alert'>";
                foreach ($errors as $error) {
                    echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "<br>";
                }
                echo "</div>";
            }
            ?>

            <div class="form-floating">
                <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Usuario" required>
                <label for="floatingInput">Usuario</label>
            </div>
            <div class="form-floating">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword">Contraseña</label>
            </div>

            <br>
            <button name="login_button" class="btn btn-primary w-100 py-2" type="submit" value="Acceder">Iniciar Sesión</button>

            <div class="cf-turnstile mt-4" data-sitekey="0x4AAAAAAAPJ91o8Ao2OOurr" data-callback="javascriptCallback" data-language="es"></div>

            <p class="mt-5 mb-3">&copy; 2023-2024 BackNet</p>
        </form>
    </main>
</body>

</html>