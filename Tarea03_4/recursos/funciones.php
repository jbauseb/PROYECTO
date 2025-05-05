<?php

/**
 * Gestiona el acceso de un usuario
 * @param string $user Usuario que accede al login
 * @param string $password Contraseña del usuario.
 * @param class $userModel Permite obtener los datos del usuario.
 * @return mixed Redigire al usuario a home si es válido o mensaje de error en caso de fallo.
 */
function login($user, $password, $userModel)
{
    try {
        $empleado = $userModel->selectUserBy('usuario', $user);

        //Contraseñas seguras
        if ($empleado && password_verify($password, $empleado['password'])) {
            session_regenerate_id(true); //Evita el secuestro de la sesión
            $_SESSION['user'] = $empleado;
            header("Location:" . BASE_URL . "home.php"); //Si el usuario y password son correctos, redirige al HOME.
            exit();
        } else {
            return "Credenciales no válidas."; //Mensaje de error
        }
    } catch (PDOException $e) {
        error_log("Error en Login:" . $e->getMessage()); //Registro de errores
        return "Error en el servidor. Contacte con un administrador.";
    }
}
/**
 * Función que retorna el rol de un usuario
 * @return string Rol del usuario: Administrador, Gestor o Técnico
 */
function user_rol()
{
    return isset($_SESSION) && isset($_SESSION['user']['rol']) ? htmlspecialchars($_SESSION['user']['rol']) : null;
}

/**
 * Función que retorna la sede del usuario
 * @return string Sede del usuario: Albacete, León, Madrid, Sevilla o Zaragoza
 */
function user_sede()
{
    return isset($_SESSION) && isset($_SESSION['user']['sede']) ? htmlspecialchars($_SESSION['user']['sede']) : null;
}

/**
 * Función que devuelve el nombre de la página actual.
 * @return string Nombre del archivo actual.
 */
function current_file()
{
    return basename($_SERVER['REQUEST_URI']);
}

/**
 * Función para dar formato español a las fechas (dia-mes-año)
 * Si la fecha proporcionada no es válida, devuelve null
 * @param string $date Fecha que se va a formatear. 
 * @return string|null La fecha en formato "dia-mes-año" o nulo si no hay fecha
 */
function fdate($date)
{
    if (empty($date)) {
        return null;
    }
    $fecha = new DateTime($date);
    return $fecha->format('d-m-Y');
}

/**
 * Función para comparar fecha y hora con la fecha y hora actual.
 * @param string $date Fecha a comparar.
 * @param string $time Hora a comparar.
 * @return bool True si la fecha actual es posterior a $date, y false en caso contrario.
 */
function cdate($date, $time)
{
    //Combina la fecha y hora de salida
    $datetime = new DateTime("$date $time");

    //Obtiene la fecha y hora actual
    $actual = new DateTime();

    //Compara si la fecha dada es anterior a la actual, es decir, está en el pasado
    return $actual > $datetime; //1=true
}

/**
 * Función que genera tablas para mostrar las rutas que existen en la base de datos según el criterio de fechas.
 * La ruta puede estar:
 *               en origen (no tiene fecha de salida o todavía no salió).
 *               en tránsito (ya salió del origen pero no llegó a destino).
 *               en destino (la fecha de llegada es igual o anterior a la actual).
 * @param string $texto Con el título de la ruta
 * @param array $rutas Con los datos de las rutas correspondientes
 */
function tablaRutas($texto, $rutas)
{
    if (empty($rutas)) {
        echo "<div class='alert alert-warning text-center'><h4>No existen {$texto}</h4></div>";
        return;
    }
?>
    <!-- Contenedor principal de la tabla -->
    <div class="container mt-4">
        <div class="alert alert-info">
            <!-- Título de la tabla -->
            <h3 class="text-center mb-3"><?= $texto ?> </h3>
        </div>

        <div class="table">
            <table class="table table-striped table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>Núm. ruta</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha de salida</th>
                        <th>Hora de salida</th>
                        <th>Fecha prevista de llegada</th>
                        <th>Hora prevista de llegada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rutas as $ruta) :
                        /**Verificamos si el usuario tiene permiso para ver la tabla.
                         * Puede verla si su sede es la misma que el origen o destino de la ruta,o si es administrador
                         */
                        if (in_array(user_sede(), [$ruta['origen'], $ruta['destino']]) || user_rol() === "Administrador") : ?>
                            <tr>
                                <td><?= htmlspecialchars($ruta['id_ruta']) ?></td>
                                <td><?= htmlspecialchars($ruta['origen']) ?></td>
                                <td><?= htmlspecialchars($ruta['destino']) ?></td>
                                <td><?= fdate($ruta['fecha_salida']) ?></td>
                                <td><?= $ruta['hora_salida'] ?></td>
                                <td><?= fdate($ruta['fecha_llegada']) ?></td>
                                <td><?= $ruta['hora_llegada'] ?></td>
                                <td>
                                    <!-- Botón para ver los detalles de la ruta -->
                                    <a href="<?= BASE_URL ?>enrutador.php?controller=RouteController&method=show&id=<?= urlencode($ruta['id_ruta']); ?>"
                                        class="btn btn-primary btn-sm"><i class="fas fa-info-circle"></i> Detalles</a>
                                </td>
                            </tr>
                    <?php endif;
                    endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}

/**
 * Función que muestra los materiales transportados por una ruta concreta
 * @param array $materiales Contiene los datos de los materiales transportados por una ruta.
 */
function materialesRuta($materiales)
{
    if (empty($materiales)) {
        echo "<tr><td colspan='4'>No se encontraron materiales para esta ruta.</td></tr>";
        return;
    }
    $cantidad_total = 0;
    // Se crean una fila por cada material, con las correspondientes columnas 
    foreach ($materiales as $material) {
        echo "<tr>
            <td>" . htmlspecialchars($material['partnumber']) . "</td>
            <td>" . htmlspecialchars($material['material_nombre']) . "</td>
            <td>" . htmlspecialchars($material['material_descripcion']) . "</td>
            <td>" . htmlspecialchars($material['cantidad']) . "</td>";
        echo "</tr>";
        // Suma de la cantidad de materiales transportados
        $cantidad_total += $material['cantidad'];
    }
    echo "<tr><td><strong>Cantidad total transportada:</strong> $cantidad_total</td></tr>";
}

/**
 * Función que mueve el material de una ubicación a otra en función del estado de la ruta y la solicitud.
 * Se resta del stock en almacén de origen.
 * Se crea o se suma stock en almacén de destino.
 */
function actualizaMaterialTransportado()
{
    $routeModel = new RouteModel();
    $materialModel = new MaterialModel();

    //OBtenemos todas las rutas
    $rutas = $routeModel->getAllRoutes();

    //Por cada ruta, vemos donde se encuentra (origen, tránsito o destino)
    foreach ($rutas as $ruta) {

        //Si está en destino (función compara fecha)
        if (!empty($ruta['fecha_llegada']) && cdate($ruta['fecha_llegada'], $ruta['hora_llegada'])) {

            //actualizamos los materiales (sede y stock)
            $materialesEnRuta = $materialModel->getMaterialsByRoute($ruta['id_ruta']);

            foreach ($materialesEnRuta as $material) {

                //Si la ruta no está "finalizada" (bandera a 0)
                if (!$ruta['finalizada']) {

                    //Datos necesario para la actualización
                    $partnumber = $material['partnumber'];
                    $nombre = $material['material_nombre'];
                    $descripcion = $material['material_descripcion'];
                    $cantidad = $material['cantidad'];
                    $umbral_stock = $material['umbral_stock'];
                    $destino = $ruta['destino'];

                    //Restamos stock del origen
                    $stock = $materialModel->getStock($material['id_material']);
                    $nuevo_stock = $stock - $cantidad;
                    $materialModel->setStock($material['id_material'], $nuevo_stock);

                    //Sumamos stock al destino
                    //Si la pieza ya existe en destino, se suma
                    $pieza = $materialModel->getMaterialByPartnumberAndAlmacen($partnumber, $destino);
                    if ($pieza) {
                        $nuevo_stock = $pieza['stock'] + $material['cantidad'];
                        $materialModel->setStock($pieza['id_material'], $nuevo_stock);
                    } else {
                        //Si la pieza no exite en destino, se crea
                        $materialModel->insertMaterial($partnumber, $nombre, $descripcion, $destino, $cantidad, $umbral_stock);
                    }
                    //Finalizamos la ruta
                    $routeModel->finalizeRoute($ruta['id_ruta']);
                }
            }
        }
    }
}

/**
 * Función que calcula la distancia por carretera entre dos coordenadas terrestres.
 * Para ello se usa el servicio público de API REST de OSRM.
 * @param string $coordenadas1, $coordenadas2 Las coordenadas del origen y destino
 * @return float Kilómetros por carretera entre las dos coordenadas.
 */
function calcularDistancia($coordenadas1, $coordenadas2)
{
    list($lat1, $lon1) = $coordenadas1; //Punto de origen
    list($lat2, $lon2) = $coordenadas2; //Punto de destino

    //URL para la solicitud a la API pública de OSRM
    $url = "http://router.project-osrm.org/route/v1/driving/{$lon1},{$lat1};{$lon2},{$lat2}?overview=false";

    //Inicializa cURL
    $ch = curl_init();

    //Configuración opciones de cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //Para que devuelva la respuesta en lugar de imprimirla
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  //Sigue redirecciones si las hay
    
    //Ejecuta la solicitud y obtiene la respuesta
    $response = curl_exec($ch);

    //Verifica si hubo errores en la solicitud
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "Error en la solicitud cURL: " . $error_msg;
    }

    //Cierra la sesión cURL
    curl_close($ch);

    //Decodifica la respuesta JSON
    $data = json_decode($response, true);

    //Verifica si la API respondió correctamente
    if (isset($data['routes'][0]['distance'])) {
        //Extrae la distancia en metros y convrtimos a kilómetros
        $distancia = $data['routes'][0]['distance'] / 1000;
        return round($distancia, 2); //Se redondea a 2 decimales
    } else {
        return "Error al obtener la distancia.";
    }
}


/**
 * Igual pero sin usar la API REST de OSRM. 
 * La ventaja es el tiempo de cálculo es mucho menor, y que se puede usar en InfinityFree
 * La desventaja es que no lo calcula en tiempo real, teniendo en cuenta las variaciones en las carreteras.
 * @param string $origen de la ruta
 * @param string $destino de la ruta
 * @param int $distancias entre el origen y la ruta, obtenidos del fichero ubicaciones.php
 * @return int Distancia directa o inversa entre el origen y el destino
 */
// function calcularDistancia($origen, $destino, $distancias)
// {
//     //Establecemos una clave directa y otra inversa, ya que se pueden establecer los dos trayectos
//     $clave_directa = "{$origen}-{$destino}";
//     $clave_inversa = "{$destino}-{$origen}";

//     //Busca la combinación adecuada en el array $distancias
//     if (isset($distancias[$clave_directa])) {
//         return $distancias[$clave_directa];

//         //Si no encuentra la clave directa, busca la clave inversa
//     } elseif (isset($distancias[$clave_inversa])) {
//         return $distancias[$clave_inversa];

//         //En otro caso, retorna un valor muy grande
//     } else {
//         return PHP_INT_MAX;
//     }
// }

/**
 * Función que calcula el tiempo que se tarda en recorrer la distancia entre el origen y el destino o viceversa
 * Nos valemos de la funcion calcularDistancia, es decir, de la API de OSRM.
 * @param string $coordenadas1, $coordenadas2 de origen y destino
 * @return int Horas que se tarda en recorrer la distancia
 */

function calcularTiempoViaje($coordenadas1, $coordenadas2)
{
    $distancia = calcularDistancia($coordenadas1, $coordenadas2);

    if (!is_numeric($distancia)) {
        return $distancia; //Devuelve el mensaje de error si hubo un problema
    }
    $velocidadMedia = 80; //km/h
    $tiempo = $distancia / $velocidadMedia;
    return round($tiempo, 2);
}
