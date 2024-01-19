<?php
// index.php
if (!isset($_SESSION)) {
    session_start();
}


// Si la sesión está iniciada, mostrar el contenido de index.php
if (isset($_SESSION['username'])) {

} else {
    // Si no, redirigir al formulario de inicio de sesión
    header('location: login.php');
    exit();
}
?>

<?php
    //Este archivo contiene todas las funciones para interactuar con los datos.
    $conexion = mysqli_connect("localhost", "root", "", "db_dispositivos_red");

    // Función para obtener nombre de los routers desde la base de datos
    function obtenerRoutersDesdeBaseDeDatos() {
        // Se usa global para que la función tenga acceso a las variables globales del archivo.
        global $conexion;
         
        $routers = mysqli_query($conexion, "SELECT id, proyecto, nombre FROM dispositivos WHERE tipo='Router'") or die("Problemas en el select:" . mysqli_error($conexion));

        $resultado = array();

        while ($row = mysqli_fetch_assoc($routers)) {
            // Agrega el array asociativo con nombre y proyecto al array resultado
            $resultado[] = array('id' => $row['id'], 'nombre' => $row['nombre'], 'proyecto' => $row['proyecto']);
        }

        return $resultado;
    }

    // Función para obtener nombre de los switches desde la base de datos
    function obtenerSwitchesDesdeBaseDeDatos() {

        global $conexion;
        
        $switches = mysqli_query($conexion, "SELECT id, nombre FROM dispositivos WHERE tipo='Switch'") or die("Problemas en el select:" . mysqli_error($conexion));

        $resultado = array();

        while ($row = mysqli_fetch_assoc($switches)) {
            $resultado[] = array('id' => $row['id'], 'nombre' => $row['nombre']);
        }

        return $resultado;
    }

    // Función para obtener datos del dispositivo por su nombre
    function obtenerDatosDelDispositivo($id_del_equipo) {
        global $conexion;
        $dispositivoDatos = mysqli_query($conexion, "SELECT * FROM dispositivos WHERE id='$id_del_equipo'") or die("Problemas en el select:" . mysqli_error($conexion));

        $resultado = array();

        while ($row = mysqli_fetch_assoc($dispositivoDatos)) {
            $resultado[] = $row;
        }

        return $resultado;
    }

    // Función para obtener configuraciones de un dispositivo por su id
    function obtenerConfiguraciones($id_del_equipo) {

        global $conexion;
        $configuraciones = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE dispositivo_id='$id_del_equipo'") or die("Problemas en el select:" . mysqli_error($conexion));

        $resultado = array();

        while ($row = mysqli_fetch_assoc($configuraciones)) {
            $resultado[] = $row;
        }

        return $resultado;
    }

    function obtenerTodosDispositivos() {
        global $conexion;
             
        $dispositivos = mysqli_query($conexion, "SELECT * FROM dispositivos") or die("Problemas en el select:" . mysqli_error($conexion));
    
        return $dispositivos;
    }

    function eliminarDespositivoByID($id_del_equipo) {
        global $conexion;
             
        $resultado = mysqli_query($conexion, "DELETE FROM dispositivos WHERE id='$id_del_equipo'") or die("Problemas en el select:" . mysqli_error($conexion));
        
        // Verificar cuántas filas fueron afectadas
        $filas_afectadas = mysqli_affected_rows($conexion);

        if ($filas_afectadas > 0) {
            // La eliminación fue exitosa
            return true;
        } else {
            // No se encontró ninguna fila para eliminar
            return false;
        }
    }

    // configuraciones.php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'eliminarConfig') {
                
            $resultadoDeleteConfig = eliminarConfig($_POST['idConfig']);
            
            if ($resultadoDeleteConfig) {
                // Almacena el mensaje de alerta en una sesión
                $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Configuración eliminada correctamente'];
                return true;
            } else {
                // Almacena el mensaje de alerta en una sesión
                $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Error al eliminar la configuración'];
                return false;
            }
        }
    }


    function eliminarConfig($idConfig) {
        global $conexion;
    
        // Asegúrate de que $idConfig sea un entero antes de usarlo en la consulta
        $idConfig = intval($idConfig);
    
        $consulta = "DELETE FROM configuraciones WHERE id = $idConfig";
        $resultado = mysqli_query($conexion, $consulta) or die("Problemas en el select: " . mysqli_error($conexion));
    
        // Verificar cuántas filas fueron afectadas
        $filas_afectadas = mysqli_affected_rows($conexion);
    
        if ($filas_afectadas > 0) {
            // La eliminación fue exitosa
            return true;
        } else {
            // No se encontró ninguna fila para eliminar
            return false;
        }
    }
    


    //FIXME: El archivo descargado contiene html del index.php
    function descargarDatosDispositivo($id_del_equipo) {
        global $conexion;
    
        // Consulta para obtener los datos del dispositivo
        $consulta = "SELECT * FROM dispositivos WHERE id='$id_del_equipo'";
        $resultado = mysqli_query($conexion, $consulta) or die("Problemas en el select: " . mysqli_error($conexion));
    
        // Verificar cuántas filas fueron afectadas
        $filas_afectadas = mysqli_affected_rows($conexion);
    
        if ($filas_afectadas > 0) {
            // La consulta fue exitosa, descargamos los datos en un archivo txt
            $datos_dispositivo = mysqli_fetch_assoc($resultado);
    
            // Nombre del archivo
            $nombre_archivo = $datos_dispositivo['tipo'] . ' - ' . $datos_dispositivo['direccion_mac'] . '.txt';
    
            // Establecer las cabeceras HTTP para forzar la descarga
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
    
            // Imprimir los datos en el archivo
            foreach ($datos_dispositivo as $campo => $valor) {
                echo "$campo: $valor\n";
            }
    
            // Detener la ejecución del script después de enviar los datos del archivo
            exit();
        } else {
            // No se encontró ninguna fila para descargar
            return false;
        }
    }

    //Recibe datos del formulario para agregar a nuevos dispositivos a la DB.
    function agregarDispositivo($proyecto, $nombre, $tipo, $direccion_mac, $direccion_ip, $protocolo_acceso, $ubicacion, $informacion_adicional) {

        global $conexion;

        $consulta = "INSERT INTO dispositivos (proyecto, nombre, tipo, direccion_mac, direccion_ip, protocolo_acceso, ubicacion, informacion_adicional) VALUES ('$proyecto', '$nombre', '$tipo', '$direccion_mac', '$direccion_ip', '$protocolo_acceso', '$ubicacion', '$informacion_adicional')";
    
        $resultado = mysqli_query($conexion, $consulta ) or die("Problemas en el insert:" . mysqli_error($conexion));

        // Verificar cuántas filas fueron afectadas
        $filas_afectadas = mysqli_affected_rows($conexion);

        if ($filas_afectadas > 0) {
            // La agregación fue exitosa
            return true;
        } else {
            
            return false;
        }
    }

    function agregarConfigDispositivo($idDispositivoConfig, $nombre_config, $configuracion) {

        global $conexion;

        $consulta = "INSERT INTO configuraciones (dispositivo_id, nombre_configuracion, configuracion) VALUES ('$idDispositivoConfig', '$nombre_config', '$configuracion')";

        $resultado = mysqli_query($conexion, $consulta ) or die("Problemas en el insert:" . mysqli_error($conexion));

        // Verificar cuántas filas fueron afectadas
        $filas_afectadas = mysqli_affected_rows($conexion);

        if ($filas_afectadas > 0) {
            // La agregación fue exitosa
            return true;
        } else {
            return false;
        }
    }

    function modificarDispositivo($id_del_equipo, $proyecto, $nombre, $tipo, $direccion_mac, $direccion_ip, $protocolo_acceso, $ubicacion, $informacion_adicional) {
    
        global $conexion;
    
        // Construir la parte SET de la consulta solo para columnas no vacías
        $setPart = '';
        $updateColumns = array(
            'proyecto' => $proyecto,
            'nombre' => $nombre,
            'tipo' => $tipo,
            'direccion_mac' => $direccion_mac,
            'direccion_ip' => $direccion_ip,
            'protocolo_acceso' => $protocolo_acceso,
            'ubicacion' => $ubicacion,
            'informacion_adicional' => $informacion_adicional,
        );
    
        foreach ($updateColumns as $column => $value) {
            if (!empty($value)) {
                $setPart .= "$column = '$value', ";
            }
        }
    
        // Eliminar la coma y espacio adicionales al final de la cadena SET
        $setPart = rtrim($setPart, ', ');
    
        if (!empty($setPart)) {
            // Si $setPart no está vacío, realizar la actualización
            $consulta = "UPDATE dispositivos SET $setPart WHERE id = '$id_del_equipo'";
            $resultado = mysqli_query($conexion, $consulta) or die("Problemas en el update: " . mysqli_error($conexion));
        }

        // Verificar cuántas filas fueron afectadas
        $filas_afectadas = mysqli_affected_rows($conexion);

        if ($filas_afectadas > 0) {
            // La eliminación fue exitosa
            return true;
        } else {
            // No se encontró ninguna fila para eliminar
            return false;
        }
    }
    
    function mostrarAlerta($alertType, $mensaje) {
        echo '<div class="position-fixed top-0 start-50 translate-middle-x mt-5 mx-auto" style="z-index: 10000;">
            <div id="alerta" class="alert alert-' . $alertType . ' mt-4" role="alert">
                ' . $mensaje . '
            </div>
        </div>';
    
        // Javascript para cerrar la alerta después de cierto tiempo.
        echo '<script>
                setTimeout(function() {
                    var alertDiv = document.getElementById("alerta");
                    alertDiv.style.display = "none";
                }, 4000);
              </script>';
    }

    // Función para obtener la clave secreta 2FA de un usuario
    function obtenerSecretDeUsuario($nombreUsuario) {

        global $conexion;

        // Escapar el nombre de usuario para evitar inyecciones SQL (importante)
        $nombreUsuario = $conexion->real_escape_string($nombreUsuario);

        // Realizar la consulta para obtener la clave secreta
        $consulta = "SELECT 2fa_secret FROM users WHERE username = '$nombreUsuario'";
        $resultado = $conexion->query($consulta);

        // Verificar si la consulta tuvo éxito
        if ($resultado) {
            // Obtener el resultado como array asociativo
            $fila = $resultado->fetch_assoc();

            // Verificar si se encontró el usuario y obtener la clave secreta
            if ($fila) {
                return $fila['2fa_secret'];
            } else {
                return null; // Usuario no encontrado
            }
        } else {
            die("Error en la consulta: " . $conexion->error);
        }
    }

    // Función para actualizar la clave secreta 2FA de un usuario
    function actualizarSecretQRDeUsuario($nuevaSecret) {
            global $conexion;
        
            // Escapar la nueva clave secreta para evitar inyecciones SQL (importante)
            $nuevaSecret = $conexion->real_escape_string($nuevaSecret);
        
            // Obtener el nombre de usuario de la sesión
            $nombreUsuario = $_SESSION['username'];
        
            // Realizar la consulta para actualizar la clave secreta
            $consulta = "UPDATE users SET 2fa_secret = '$nuevaSecret' WHERE username = '$nombreUsuario'";
            
            // Ejecutar la consulta
            $resultado = $conexion->query($consulta);
        
            // Verificar si la consulta tuvo éxito
            if ($resultado) {
                return true; // Actualización exitosa
            } else {
                die("Error en la consulta: " . $conexion->error);
            }
    }

    // Comprbación de la solicitud AJAX enviada desde home.js.
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['proyecto'])) {
        // El nombre del proyecto recibido
        $proyecto = $_POST['proyecto'];

    
        // Obtener datos del proyecto y devolverlos como JSON
        $datosProyecto = obtenerCantidadDispositivos($proyecto);
    
        // Devolver datos como JSON
        echo json_encode($datosProyecto);

    } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['red_proyecto']))  {
        // El nombre del proyecto recibido
        $red_proyecto = $_POST['red_proyecto'];
    
        // Obtener nodos y enlaces
        $nodos = ObtenerNodosDB($red_proyecto);
        $enlaces = ObtenerEnlacesDB($red_proyecto);
    
        // Devolver datos como JSON
        echo json_encode(array('nodes' => $nodos, 'links' => $enlaces));

    } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_conexion']))  { 
        
        $id_conexion_a_borrar = $_POST['id_conexion'];

        $borrarConexion = borrarConexionById($id_conexion_a_borrar);

        echo $borrarConexion;

    } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['origenConexion']) && isset($_POST['destinoConexion']) && isset($_POST['proyectoConexion']))  { 
        // Obtén los valores de origen y destino desde la solicitud
        $origen = $_POST['origenConexion'];
        $destino = $_POST['destinoConexion'];
        $proyecto = $_POST['proyectoConexion'];
    
        $agregarConexion = agregarConexion($proyecto, $origen, $destino);

        echo $agregarConexion;

    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['formVerifCode'])) {
            // El formulario 'formVerifCode' es válido, ahora puedes procesar los datos
            if (isset($_POST['inputVerifCode'])) {
                // Obtener el valor del campo 'inputVerifCode'
                $miInputValue = $_POST['inputVerifCode'];
    
                // Mostrar o procesar el valor recibido
                //var_dump($miInputValue);
    
                $verificacionCodigo = verificarCodigo2FA($miInputValue);
    
                // Guardamos el valor en una variable de sesión
                $_SESSION['verificacionCode2FA'] = $verificacionCodigo;
                header('location: ./index.php');
            }
        } elseif (isset($_POST['formChangePass'])) {
            // El formulario 'formChangePass' es válido, ahora puedes procesar los datos
            if (isset($_POST['inputChangePass'])) {
                // Obtener el valor del campo 'inputChangePass'
                $inputNuevaPass = $_POST['inputChangePass'];
                $usuario = $_SESSION['username'];

                $resultCambioPass = cambiarPassword($usuario, $inputNuevaPass);

                // Guardamos el valor en una variable de sesión
                $_SESSION['resultCambioPass'] = $resultCambioPass;
                header('location: ./index.php');
            }

        } elseif (isset($_POST['formVerifPass'])) { 
            // Obtener el valor del campo 'inputVerifPass'
            $inputValueVerifPass = $_POST['inputVerifPass'];
            $usuario = $_SESSION['username'];

            $resultVerifPass = verificarPass($usuario, $inputValueVerifPass);
            
            // Guardamos el valor en una variable de sesión
            $_SESSION['resultVerifPass'] = $resultVerifPass;
            header('location: ./index.php');
        }
    }

    function verificarPass($username, $PassAverificar) {

        global $conexion;

        // Verificar la conexión
        if ($conexion->connect_error) {
            die("Error de conexión a la base de datos: " . $conexion->connect_error);
        }

        // Consulta SQL para obtener la contraseña del usuario
        $consulta = "SELECT password FROM users WHERE username = ?";
        
        // Preparar la consulta
        $stmt = $conexion->prepare($consulta);

        // Verificar si la preparación de la consulta fue exitosa
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $conexion->error);
        }

        // Vincular parámetros y ejecutar la consulta
        $stmt->bind_param("s", $username);
        $stmt->execute();

        // Vincular el resultado
        $stmt->bind_result($passwordHash);

        // Obtener el resultado
        $stmt->fetch();

        // Verificar si la contraseña proporcionada coincide con la almacenada en la base de datos
        $verificacion = password_verify($PassAverificar, $passwordHash);

        // Cerrar la conexión y liberar los recursos
        $stmt->close();

        // Devolver el resultado de la verificación
        return $verificacion;
    }

    //Función encargada de actualizar la contraseña del usuario actual.
    function cambiarPassword($usuario, $nuevaPass) {

        global $conexion;

        // Hash de la nueva contraseña
        $hashNuevaContraseña = password_hash($nuevaPass, PASSWORD_DEFAULT);

        // Preparar la consulta para actualizar la contraseña
        $stmt = $conexion->prepare("UPDATE users SET password = ? WHERE username = ?");

        // Verificar si la preparación de la consulta fue exitosa
        if (!$stmt) {
            // Manejo de error, puedes personalizar según tus necesidades
            return false;
        }

        // Vincular los parámetros
        $stmt->bind_param("ss", $hashNuevaContraseña, $usuario);

        // Ejecutar la consulta
        $stmt->execute();

        // Verificar si se actualizaron filas
        if ($stmt->affected_rows > 0) {
            // Contraseña actualizada con éxito
            // Cerrar la declaración
            $stmt->close();
            return true;
        } else {
            // No se actualizaron filas, es posible que el usuario no exista
            // Cerrar la declaración
            $stmt->close();
            return false;
        }
    }

    function verificarCodigo2FA($inputCode) {

        // Obetner la secret 2FA del usuario.
        $_SESSION['auth_secret'] = obtenerSecretDeUsuario($_SESSION['username']);
    
        // Verificar el codigo $miInputValue con la secret.
        require "./vendor/Authenticator/Authenticator.php";
        $Authenticator = new Authenticator();
        $checkResult = $Authenticator->verifyCode($_SESSION['auth_secret'], $inputCode, 2);

        if ($checkResult) {
           return true;
        } else {
            return false;
        }

    }
    
    

    // Obtener cantidad de cada equipos por proyecto o el total.
    function obtenerCantidadDispositivos($nombreProyecto) {
        // Crear conexión
        global $conexion;
    
        // Verificar la conexión
        if ($conexion->connect_error) {
            die("Conexión fallida: " . $conexion->connect_error);
        }
    
        // Inicializar variables
        $totalProyectos = 0;
        $totalDispositivos = 0;
        $totalRouters = 0;
        $totalFirewalls = 0;
        $totalSwitches = 0;
    
        // Construir la parte de la consulta relacionada con el proyecto
        $proyectoCondicion = ($nombreProyecto === 'Todos') ? '' : "WHERE proyecto = '$nombreProyecto'";
    
        // Consulta para obtener la cantidad total de proyectos y el total de cada tipo de dispositivo para "Todos".
        $sqlTodos = "
            SELECT
                COUNT(DISTINCT proyecto) AS total_proyectos,
                SUM(CASE WHEN tipo = 'Router' THEN 1 ELSE 0 END) AS router,
                SUM(CASE WHEN tipo = 'Firewall' THEN 1 ELSE 0 END) AS firewall,
                SUM(CASE WHEN tipo = 'Switch' THEN 1 ELSE 0 END) AS switch
            FROM dispositivos;
        ";
    
        // Consulta para obtener la cantidad total de proyectos y el total de cada tipo de dispositivo (para un proyecto específico)
        $sqlProyecto = "
            SELECT 
                COUNT(DISTINCT proyecto) as total_proyectos,
                tipo,
                COUNT(*) as cantidad
            FROM 
                dispositivos
            $proyectoCondicion
            GROUP BY 
                proyecto, tipo
        ";
    
        // Seleccionar la consulta según el caso.
        $sql = ($nombreProyecto === 'Todos') ? $sqlTodos : $sqlProyecto;
    
        $result = $conexion->query($sql);
    
        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            // Iterar sobre los resultados y contar la cantidad total de proyectos y el total de cada tipo
            while ($row = $result->fetch_assoc()) {
    
                // Actualizar los contadores según el caso
                if ($nombreProyecto === 'Todos') {
                    $totalProyectos = $row['total_proyectos'];
                    $totalRouters = $row['router'];
                    $totalFirewalls = $row['firewall'];
                    $totalSwitches = $row['switch'];
                } else {
                    $tipo = strtolower($row['tipo']);
                    switch ($tipo) {
                        case 'router':
                            $totalRouters += $row['cantidad'];
                            break;
                        case 'firewall':
                            $totalFirewalls += $row['cantidad'];
                            break;
                        case 'switch':
                            $totalSwitches += $row['cantidad'];
                            break;
                    }
                    // Acumular la cantidad al total general de dispositivos
                    $totalDispositivos += $row['cantidad'];
                }
            }
        }
    
        // Devolver la cantidad total de proyectos y el total de cada tipo de dispositivo
        return [
            'total_proyectos' => $totalProyectos,
            'total_dispositivos' => $totalDispositivos,
            'total_routers' => $totalRouters,
            'total_firewalls' => $totalFirewalls,
            'total_switches' => $totalSwitches,
        ];
    }
    
    // Devuelve nombres de los proyectos.
    function obtenerNombreProyectos() {
        global $conexion;

        $sql = "SELECT DISTINCT proyecto FROM dispositivos";
        $proyectos_nombres = mysqli_query($conexion, $sql) or die("Problemas en el select: " . mysqli_error($conexion));

    
        return $proyectos_nombres ;
    }

    
    function ObtenerNodosDB($proyecto) {
        global $conexion;
        $sql = "SELECT * FROM dispositivos WHERE proyecto = '$proyecto'";
        
        $nodosProyecto = mysqli_query($conexion, $sql);
    
        if (!$nodosProyecto) {
            die("Problemas en el select: " . mysqli_error($conexion));
        }
    
        $resultados = array();
    
        while ($fila = mysqli_fetch_assoc($nodosProyecto)) {
            $resultados[] = $fila;
        }
    
        return $resultados;
    }

    function ObtenerEnlacesDB($proyecto) {
        global $conexion;
        $sql = "SELECT * FROM conexiones WHERE proyecto = '$proyecto'";
    
        $enlacesProyecto = mysqli_query($conexion, $sql);
    
        if (!$enlacesProyecto) {
            die("Problemas en el select: " . mysqli_error($conexion));
        }
    
        $resultados = array();
    
        while ($fila = mysqli_fetch_assoc($enlacesProyecto)) {
            $resultado = array(
                'id' => $fila['id'],
                'source' => $fila['id_dispositivo'],
                'target' => $fila['id_conexion']
            );
    
            $resultados[] = $resultado;
        }
    
        return $resultados;
    }    
    
    function borrarConexionById($idConexion) {
        global $conexion;

        // Preparar la consulta
        $stmt = $conexion->prepare("DELETE FROM conexiones WHERE id = ?");

        // Vincular el parámetro
        $stmt->bind_param("i", $idConexion);

        // Ejecutar la consulta
        $stmt->execute();

        // Verificar si la eliminación fue exitosa
        if ($stmt->affected_rows > 0) {
            return true;
        } else {
            return false;
        }

        // Cerrar la declaración
        $stmt->close();
    }

    function agregarConexion($proyecto, $origen, $destino) {
        global $conexion;

        // Preparar la consulta para agregar la conexión a la tabla
        $stmt = $conexion->prepare("INSERT INTO conexiones (proyecto, id_dispositivo, id_conexion) VALUES (?, ?, ?)");

        // Vincular los parámetros
        $stmt->bind_param("sii", $proyecto, $origen, $destino);

        // Ejecutar la consulta
        $stmt->execute();

        // Verificar si la inserción fue exitosa
        if ($stmt->affected_rows > 0) {
            return true;
        } else {
            return false;
        }

        // Cerrar la declaración
        $stmt->close();
    }
?>