<?php
//Se incluyen los archivos de configuración y la gestión de sesiones
require_once $_SERVER['DOCUMENT_ROOT'] . "/PROYECTO/Tarea03_4/config.php";
require_once BASE_PATH . "sesion.php";
require_once BASE_PATH . 'vendor/autoload.php'; //iNSTALACIÓN DE mpdf
require_once BASE_PATH . "modelos/MaterialModel.php";
require_once BASE_PATH . "modelos/UserModel.php";
require_once BASE_PATH . "modelos/RequestModel.php";
require_once BASE_PATH . "modelos/RouteModel.php";

//Verifica si el usuario ha iniciado sesión; si no, lo redirige a la página de inicio
if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

/**
 * Clase PDFController
 * Controlador encargado de generar informes en formato PDF.
 * Los informes hacen referencia a los empleados, materiales, solicitudes y rutas logísticas
 */
class PDFController
{
    private $mpdf;
    private $userName;

    /**
     * Constructor de la clase
     */
    public function __construct()
    {
        $this->userName = $_SESSION["user"]["nombre"];

        $this->mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P'
        ]);
    }

    /**
     * Método index
     * Muestra la página index, que permite seleccionar el tipo de informe
     */
    public function index()
    {
        require BASE_PATH . "vistas/reports/index.php";
    }

    /**
     * Método headerFooter
     * Establece la cabecera (título) y el pie (fecha, hora, autor y núm. página) del informe.
     * @param string $titulo Título del informe
     */
    private function headerFooter($titulo)
    {
        $this->mpdf->SetTitle($titulo);
        // Agregar el logo (como un favicon)
        $logoPath = BASE_URL . 'recursos/imagenes/favicon.png';
        $this->mpdf->SetHTMLHeader('
            <div style="text-align: right; font-weight: bold;">
        <img src="' . $logoPath . '" style="width: 20px; height: 20px; vertical-align: middle;" /> 
        ALM System<br>Departamento de control
            </div>
');
        $this->mpdf->SetHTMLFooter('<table width="100%"><tr>
            <td style="text-align: left;">Emitido el {DATE d-m-Y H:i} por ' . htmlspecialchars($this->userName) . '</td>
            <td style="text-align: right;">Página {PAGENO} de {nbpg}</td>
            </tr></table>');
    }

    /**
     * Método generateTable
     * Genera las tablas del informe.
     * @param array $cabeceras Cabeceras de la tabla
     * @param array $filas CAda una de las filas de la tabla
     * @return array Lista de materiales por ruta
     */
    private function generateTable($cabeceras, $filas)
    {
        $html = "<table border='1' cellpadding='5' cellspacing='0' style='width: 100%; margin-bottom: 20px;'>";
        $html .= "<thead><tr>";
        foreach ($cabeceras as $cabecera) {
            $html .= "<th>{$cabecera}</th>";
        }
        $html .= "</tr></thead><tbody>";

        foreach ($filas as $fila) {
            $html .= "<tr>";
            foreach ($fila as $celda) {
                $html .= "<td>{$celda}</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</tbody></table>";
        return $html;
    }

    /**
     * Método materialReport
     * Genera el Informe completo de materiales.
     */
    public function materialReport()
    {
        $this->headerFooter('Informe de materiales');

        //oBTENEMOS MATERIALES
        $materialModel = new MaterialModel();
        $materiales = $materialModel->getAllMaterials();
        if ($materiales) {
            //Materiales en cada almacén
            $materialesPorAlmacen = [];
            $materialesUnicos = [];
            $totalStock = 0; //Se inicializa antes de sumarlo

            foreach ($materiales as $material) {
                $materialesPorAlmacen[$material['almacen']][] = $material;
                $materialesUnicos[$material['partnumber']] = true;
            }

            //PartNumber únicos
            $totalMaterialesUnicos = count($materialesUnicos);

            $html = "<h2 style='text-align:center'>Informe de material</h2>";

            foreach ($materialesPorAlmacen as $almacen => $materialAlmacen) {
                $html .= "<h3>Almacén de {$almacen}</h3>";
                $html .= "<table border='1' cellpadding='5' cellspacing='0' style='width: 100%; margin-bottom: 20px;'>";

                $partNumberAlmacen = 0;
                $totalStockAlmacen = 0;
                $umbral_stock = 0;
                $material_en_umbral = [];

                foreach ($materialAlmacen as $material) {
                    $partNumberAlmacen++;
                    $totalStockAlmacen += $material['stock'];

                    if ($material['stock'] <= $material['umbral_stock']) {
                        $umbral_stock++;
                        $material_en_umbral[] = $material;
                    }
                }
                $html .= "<tr>
                <td colspan='4' style='text-align: left; font-weight: bold;'>Part Number distintos: {$partNumberAlmacen}</td>
            </tr>
            <tr>
                <td colspan='4' style='text-align: left; font-weight: bold;'>Piezas totales: {$totalStockAlmacen}</td>
            </tr>
            <tr>
                <td colspan='4' style='text-align: left; font-weight: bold;'>Nº piezas en umbral de stock: {$umbral_stock}";

                if ($umbral_stock > 0) {
                    $html .= "<ul>";
                    foreach ($material_en_umbral as $material) {
                        $html .= "<li>Id: {$material['id_material']} - P/N: {$material['partnumber']} - Nombre: {$material['nombre']}</li>";
                    }
                    $html .= "</ul>";
                }
                $html .= "</td></tr></tbody></table>";

                $totalStock += $totalStockAlmacen; //Total piezas en inventario
            }

            $html .= "<h3 style='text-align: left;'>Part Number distintos: {$totalMaterialesUnicos}</h3>";
            $html .= "<h3 style='text-align: left;'>Piezas totales: {$totalStock}</h3>";

            $html .= "<pagebreak>"; //Salto de página

            /**RESTO DEL INFORME */
            //Pieza con más stock. Pueden ser varias con el máximo stock
            $materialMasStock = $materialModel->materialMasStock();

            // Mostrar las piezas con más stock
            $html .= "<h3>Material con más stock</h3>";

            $filas = [];

            foreach ($materialMasStock as $material) {
                $filas[] = [
                    $material['id_material'],
                    $material['partnumber'],
                    $material['nombre'],
                    $material['almacen'],
                    $material['stock'],
                    $material['umbral_stock']
                ];
            }

            $html .= $this->generateTable(['Id', 'P/N', 'Nombre', 'Almacén', 'Stock', 'Umbral'], $filas);

            //Pieza con menos stock
            $materialMenosStock = $materialModel->materialMenosStock();

            //Muestra las piezas con menos stock
            $html .= "<h3>Material con menos stock</h3>";
            $filas = [];
            foreach ($materialMenosStock as $material) {
                $filas[] = [
                    $material['id_material'],
                    $material['partnumber'],
                    $material['nombre'],
                    $material['almacen'],
                    $material['stock'],
                    $material['umbral_stock']
                ];
            }

            $html .= $this->generateTable(['Id', 'P/N', 'Nombre', 'Almacén', 'Stock', 'Umbral'], $filas);


            //Pieza más solicitada
            $materialMasSolicitado = $materialModel->materialMasSolicitado();

            //Muestra la pieza más solicitada
            $html .= "<h3>Material más solicitado</h3>";
            $filas = [];
            foreach ($materialMasSolicitado as $material) {
                $filas[] = [
                    $material['id_material'],
                    $material['partnumber'],
                    $material['nombre'],
                    $material['almacen'],
                    $material['stock'],
                    $material['umbral_stock'],
                    $material['total_cantidad_solicitada']
                ];
            }

            $html .= $this->generateTable(['Id', 'P/N', 'Nombre', 'Almacén', 'Stock', 'Umbral', 'Total solicitado'], $filas);
        } else {
            $html = "<h3>No existen materiales para mostrar.</h3>";
        }
        //Salida en pantalla
        $this->mpdf->WriteHTML($html);
        $this->mpdf->Output('informe_materiales.pdf', 'I');
    }

    /**
     * Método empleadoReport
     * Genera el informe completo de empleados.
     */
    public function empleadoReport()
    {
        $this->headerFooter('Informe de empleados');

        //Iniciamos el modelo 
        $userModel = new UserModel();
        $empleados = $userModel->getAllUsers();
        if ($empleados) {


            /**TOTAL EMPLEADOS Y EMPLEADOS POR ROL */
            $totalEmpleados = count($empleados);
            $empleadosPorRol = [];


            foreach ($empleados as $empleado) {
                $empleadosPorRol[$empleado['rol']][] = $empleado;
            }

            $html = "<h2 style='text-align: center;'>Informe de empleados</h2>
                    <h4>Número total de empleados: {$totalEmpleados}</h4>
                    <h3 style='text-align: center;'>Empleados por rol</h3>";

            foreach ($empleadosPorRol as $rol => $usersRol) {
                $totalEmpleadosPorRol = 0;
                $filas = [];

                $html .= "<h4 style='text-align: center;'>{$rol}</h4>";

                foreach ($usersRol as $empleado) {
                    $totalEmpleadosPorRol++;
                    $filas[] = [$empleado['id'], $empleado['nombre'], $empleado['email'], $empleado['telefono']];
                }

                $html .= $this->generateTable(['ID', 'Nombre', 'Email', 'Teléfono'], $filas);
                $html .= "<h5>Total: {$totalEmpleadosPorRol}</h5>";
            }

            /**EMPLEADOS POR SEDE */
            $empleadosPorSede = [];

            foreach ($empleados as $empleado) {
                $empleadosPorSede[$empleado['sede']][] = $empleado;
            }

            $html .= "<h3 style='text-align: center;'>Empleados por sede</h3>";

            foreach ($empleadosPorSede as $sede => $usersSede) {
                $filas = [];

                $totalEmpleadosPorSede = 0;
                $html .= "<h4 style='text-align: center;'>{$sede}</h4>";

                foreach ($usersSede as $empleado) {
                    $totalEmpleadosPorSede++;
                    $filas[] = [$empleado['id'], $empleado['nombre'], $empleado['email'], $empleado['telefono']];
                }

                $html .= $this->generateTable(['ID', 'Nombre', 'Email', 'Teléfono'], $filas);
                $html .= "<h5>Total: {$totalEmpleadosPorSede}</h5>";
            }

            /**EMPLEADO MÁS ANTIGUO */
            $empleadoMasAntiguo = $userModel->oldestUser();
            $html .= "<h3>Empleado más antiguo</h3>";
            $filas = [];
            foreach ($empleadoMasAntiguo as $empleado) {
                $filas[] = [$empleado['id'], $empleado['nombre'], $empleado['email'], $empleado['sede'], $empleado['fecha_alta']];
            }

            $html .= $this->generateTable(['ID', 'Nombre', 'Email', 'Sede', 'Fecha de alta'], $filas);

            /**EMPLEADO MÁS MODERNO */
            $empleadoMasModerno = $userModel->newestUser();
            $html .= "<h3>Empleado más moderno</h3>";
            $filas = [];
            foreach ($empleadoMasModerno as $empleado) {
                $filas[] = [$empleado['id'], $empleado['nombre'], $empleado['email'], $empleado['sede'], $empleado['fecha_alta']];
            }
            $html .= $this->generateTable(['ID', 'Nombre', 'Email', 'Sede', 'Fecha de alta'], $filas);

            /**EMPLEADOS QUE REALIZAN MÁS SOLICITUDES */
            $empleadosMasSolicitudes = $userModel->userMostRequests();

            $html .= "<h3>Empleados que realizan más solicitudes</h3>";
            $filas = [];
            foreach ($empleadosMasSolicitudes as $empleado) {
                $filas[] = [$empleado['id'], $empleado['nombre'], $empleado['email'], $empleado['sede'], $empleado['fecha_alta'], $empleado['num_solicitudes']];
            }
            $html .= $this->generateTable(['ID', 'Nombre', 'Email', 'Sede', 'Fecha de alta', 'Nº solicitudes'], $filas);

            /**EMPLEADO QUE REALIZA MENOS SOLICITUDES */
            $empleadosMenosSolicitudes = $userModel->userFewerRequests();

            $html .= "<h3>Empleados que realizan menos solicitudes</h3>";
            $filas = [];
            foreach ($empleadosMenosSolicitudes as $empleado) {
                $filas[] = [$empleado['id'], $empleado['nombre'], $empleado['email'], $empleado['sede'], $empleado['fecha_alta'], $empleado['num_solicitudes']];
            }
            $html .= $this->generateTable(['ID', 'Nombre', 'Email', 'Sede', 'Fecha de alta', 'Nº solicitudes'], $filas);

            /**EMPLEADO TÉCNICO QUE NO HA REALIZADO NINGUNA SOLICITUD */
            $empleadosSinSolicitudes = $userModel->userWithoutRequests();
            $html .= "<h3>Técnicos que no han realizado solicitudes</h3>";
            $filas = [];
            foreach ($empleadosSinSolicitudes as $empleado) {
                $filas[] = [$empleado['id'], $empleado['nombre'], $empleado['email'], $empleado['sede'], $empleado['fecha_alta']];
            }
            $html .= $this->generateTable(['ID', 'Nombre', 'Email', 'Sede', 'Fecha de alta'], $filas);
        } else {
            $html = "<h3>No existen empleados para mostrar.</h3>";
        }
        //Salida por pantalla
        $this->mpdf->WriteHTML($html);
        $this->mpdf->Output('informe_empleados.pdf', 'I');
    }

    /**
     * Método requestReport
     * Genera el informe completo de solicitudes.
     */
    public function requestReport()
    {
        $this->headerFooter('Informe de solicitudes');

        //Iniciamos el modelo 
        $requestModel = new RequestModel();
        //Total de solicitudes
        $requests = $requestModel->getAllRequests();

        if ($requests) {
            /**TOTAL SOLICITUDES Y SOLICITUDES POR USUARIO */
            $totalSolicitudes = count($requests);
            $solicitudesPorUsuario = [];

            foreach ($requests as $request) {
                $solicitudesPorUsuario[$request['id_empleado']][] = $request;
            }

            $html = "<h2 style='text-align: center;'>Informe de solicitudes</h2>
        <h4>Número total de solicitudes: {$totalSolicitudes}</h4>
        <h3 style='text-align: start;'>Solicitudes por usuario</h3>";

            $filas = [];

            foreach ($solicitudesPorUsuario as $usuario) {
                $numero = count($usuario);
                $filas[] = [$usuario[0]['empleado_nombre'], $numero];
            }
            $html .= $this->generateTable(['Nombre', 'Solicitudes'], $filas);

            //Solicitud con mayor núm dias para resolver
            $requestMaxDias = $requestModel->requestMaxDias();
            $filas = [];

            foreach ($requestMaxDias as $request) {
                $filas[] = [$request['id_solicitud'], $request['fecha_inicio'], $request['fecha_fin'], $request['id_empleado'], $request['dias_diferencia']];
            }
            $html .= "<h3 style='text-align: start;'>Solicitudes finalizadas con mayor número de días</h3>";
            $html .= $this->generateTable(['Solicitud nº', 'Fecha de inicio', 'Fecha fin', 'Id   Empleado', 'Días'], $filas);

            //Solicitud con menor num días en resolver
            $requestMinDias = $requestModel->requestMinDias();
            $filas = [];

            foreach ($requestMinDias as $request) {
                $filas[] = [$request['id_solicitud'], $request['fecha_inicio'], $request['fecha_fin'], $request['id_empleado'], $request['dias_diferencia']];
            }
            $html .= "<h3 style='text-align: start;'>Solicitudes finalizadas en el menor número de días</h3>";
            $html .= $this->generateTable(['Solicitud nº', 'Fecha de inicio', 'Fecha fin', 'Id   Empleado', 'Días'], $filas);

            //Media de días en finalizar todas las solicitudes
            $totalDias = 0;

            foreach ($requests as $request) {
                $fechaInicio = new DateTime($request['fecha_inicio']);
                $fechaFin = new DateTime($request['fecha_fin']);
                $diferencia = $fechaInicio->diff($fechaFin)->days; //Calcula la diferencia en días
                $totalDias += $diferencia;
            }

            //Calcula la media evitando la división por cero
            $mediaDias = $totalSolicitudes > 0 ? $totalDias / $totalSolicitudes : 0;

            $html .= "<h3 style='text-align: start;'>Promedio de días en finalizar las solicitudes: <strong>" . round($mediaDias, 2) . " días.</strong></h3>";
        } else {
            $html = "<h3>No existen solicitudes para mostrar.</h3>";
        }
        //Salida por pantalla
        $this->mpdf->WriteHTML($html);
        $this->mpdf->Output('informe_solicitudes.pdf', 'I');
    }

    /**
     * Método routeReport
     * Genera el informe completo de rutas logísticas.
     */
    public function routeReport()
    {
        $this->headerFooter('Informe de rutas');

        //Iniciamos el modelo 
        $routeModel = new RouteModel();

        //Total de rutas en el último año (las de más de 365 días se eliminan automáticamente)
        $routes = $routeModel->getFinalizedRoutes();

        if ($routes) {
            $totalRutas = count($routes);
            $html = "<h2 style='text-align: center;'>Informe de Rutas</h2>
                    <h4>Número total de rutas en el último año: {$totalRutas}</h4>";

            //El origen más repetido
            $origenMasRepetido = $routeModel->origenMasRepetido();
            $filas = [];

            foreach ($origenMasRepetido as $origen) {
                $filas[] = [$origen['origen'], $origen['total']];
            };

            $html .= "<h3 style='text-align: start;'>Origen más repetido</h3>";
            $html .= $this->generateTable(['Origen', 'Total'], $filas);

            //El destino más repetido
            $destinoMasRepetido = $routeModel->destinoMasRepetido();
            $filas = [];

            foreach ($destinoMasRepetido as $destino) {
                $filas[] = [$destino['destino'], $destino['total']];
            };

            $html .= "<h3 style='text-align: start;'>Destino más repetido</h3>";
            $html .= $this->generateTable(['Destino', 'Total'], $filas);

            //Ruta con más tiempo en completarse
            $rutaMaxTiempo = $routeModel->rutaTiempo("DESC");
            $ruta = $rutaMaxTiempo[0];
            $filas = [[$ruta['id_ruta'], $ruta['origen'], $ruta['destino'], $ruta['fecha_salida'], $ruta['hora_salida'], $ruta['fecha_llegada'], $ruta['hora_llegada'], $ruta['duracion_horas']]];

            $html .= "<h3 style='text-align: start;'>Ruta completada en mayor tiempo</h3>";
            $html .= $this->generateTable(['Id', 'Origen', 'Destino', 'Fecha de salida', 'Hora de salida', 'Fecha de llegada', 'Hora de llegada', 'Duración (horas)'], $filas);


            //Ruta con menos tiempo en completarse
            $rutaMinTiempo = $routeModel->rutaTiempo("ASC");
            $ruta = $rutaMinTiempo[0];
            $filas = [[$ruta['id_ruta'], $ruta['origen'], $ruta['destino'], $ruta['fecha_salida'], $ruta['hora_salida'], $ruta['fecha_llegada'], $ruta['hora_llegada'], $ruta['duracion_horas']]];

            $html .= "<h3 style='text-align: start;'>Ruta completada en menor tiempo</h3>";
            $html .= $this->generateTable(['Id', 'Origen', 'Destino', 'Fecha de salida', 'Hora de salida', 'Fecha de llegada', 'Hora de llegada', 'Duración (horas)'], $filas);

            //Media de tiempo en completarse de todas las rutas
            $sumaDuraciones = 0;

            foreach ($rutaMaxTiempo as $ruta) {
                $sumaDuraciones += $ruta['duracion_horas'];
            }
            $mediaTiempo = ($totalRutas > 0) ? round($sumaDuraciones / $totalRutas, 2) : 0;

            $html .= "<h4 style='text-align: start;'>Tiempo total de todas las rutas: {$sumaDuraciones} horas</h4>";
            $html .= "<h4 style='text-align: start;'>Promedio duración de todas las rutas: {$mediaTiempo} horas</h4>";

            global $ubicaciones; //Contiene las coordenadas de todos los almacenes
            $totalKm = 0;

            foreach ($routes as $route) {
                if ($route['finalizada']) {
                    $origen = $route['origen'];
                    $destino = $route['destino'];

                    //Obtenemos las coordenadas desde $ubicaciones
                    if (isset($ubicaciones[$origen]) && isset($ubicaciones[$destino])) {
                        $coorOrigen = $ubicaciones[$origen];
                        $coorDestino = $ubicaciones[$destino];

                        $distancia = calcularDistancia($coorOrigen, $coorDestino);
                        $totalKm += $distancia;
                    } else {
                        echo "Error: Coordenadas no encontradas para {$origen} o {$destino}";
                        exit();
                    }
                }
            }
            $kmTotales = round($totalKm, 2);
            $html .= "<h4 style='text-align: start;'>Distancia total recorrida: {$kmTotales} kilómetros</h4>";

            //Promedio kilómetros por ruta
            $mediaKm = ($totalRutas > 0) ? round($totalKm / $totalRutas, 2) : 0;
            $html .= "<h4 style='text-align: start;'>Promedio distancia de todas las rutas: {$mediaKm} kilómetros</h4>";
        } else {
            $html = "<h3>No existen rutas para mostrar.</h3>";
        }
        //Salida por pantalla
        $this->mpdf->WriteHTML($html);
        $this->mpdf->Output('informe_rutas.pdf', 'I');
    }
}
