<?php
//Se incluyen los archivos de configuración y la gestión de sesiones
require_once __DIR__ . "/../config.php";
require_once BASE_PATH . "sesion.php";  //INCLUYE SESIÓN
require_once BASE_PATH . 'modelos/RequestModel.php';
require_once BASE_PATH . 'modelos/MaterialModel.php';
require_once BASE_PATH . "recursos/funciones.php";
require_once BASE_PATH . "recursos/ubicaciones.php";

//Verifica si el usuario ha iniciado sesión; si no, lo redirige a la página de inicio
if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

/**
 * Clase RequestController
 * Controlador encargado de gestionar la lógica de las solicitudes de material.
 */
class RequestController
{
    private $requestModel;

    /**
     * Constructor de la clase
     */
    public function __construct()
    {
        $this->requestModel = new RequestModel();
    }

    /**
     * Actualiza los materiales transportados: Este método actualiza la tabla "material" para que en caso de realizar
     * una nueva solicitud, el material esté actualizado en la base de datos.
     * Obtiene las solicitudes para mostrarlas.
     * Muestra la página con todas las solicitudes.
     */
    public function index()
    {
        actualizaMaterialTransportado();
        $requests = $this->requestModel->getAllRequests();
        require BASE_PATH . 'vistas/requests/index.php';
    }

    /**
     * Muestra el formulario para crear una nueva solicitud.
     * Obtiene todos los partnumber con la suma de stock de los materiales de todos los almacenes.
     */
    public function create()
    {
        $materialModel = new MaterialModel();
        $materiales = $materialModel->getPartnumberWithStock();
        require BASE_PATH . "vistas/requests/add.php";
    }

    /**
     * Muestra el formulario de edición de una solicitud existente.
     * Obtiene los detalles de la solicitud y las cantidades de materiales solicitados.
     * @param int $id Identificador de la solicitud a editar.
     */
    public function edit($id)
    {
        $request = $this->requestModel->getRequestDetails($id);
        $materialController = new MaterialController();

        //Inicializamos arrays para almacenar seleccionados y sus cantidades
        $cantidades = [];

        //Mapeamos las cantidades de los materiales en la solicitud
        foreach ($request as $dato) {
            $cantidades[$dato['id_material']] = $dato['cantidad'];
        }

        require BASE_PATH . "vistas/requests/edit.php";
    }

    /**
     * Actualiza las cantidades de materiales de una solicitud existente.
     */
    public function update()
    {
        if (
            isset($_POST['id_solicitud'], $_POST['id_material'], $_POST['cantidad']) &&
            is_array($_POST['id_material']) &&
            is_array($_POST['cantidad']) &&
            count($_POST['id_material']) === count($_POST['cantidad'])
        ) {
            $id_solicitud = intval($_POST['id_solicitud']);
            $id_materiales = array_map('intval', $_POST['id_material']);
            $cantidades = array_map('intval', $_POST['cantidad']);

            $this->requestModel->updateMaterialToRequest($id_solicitud, $id_materiales, $cantidades);
            $this->index();
        }
    }

    /**
     * Establece el estado de una solicitud ("Pendiente", "En tránsito", "Entregado" o "Finalizada")
     * @param int $id_solicitud Identificador de la solicitud cuya estado será actualizado.
     */
    public function setStatus($id_solicitud)
    {
        $this->requestModel->setStatus($id_solicitud);
    }

    /**
     * Muestra los detalles de una solicitud específica.
     * @param int $id Identificador de la solicitud a mostrar.
     */
    public function show($id)
    {
        $request = $this->requestModel->getRequestDetails($id);
        require BASE_PATH . 'vistas/requests/show.php';
    }

    /**
     * Muestra la confirmación de eliminación de una solicitud.
     * @param int $id Identificador de la solicitud a eliminar.
     */
    public function remove($id)
    {
        if (!$id || !($this->requestModel->getRequestDetails($id))) {
            die("Solicitud no encontrada");
        }
        require BASE_PATH . "vistas/requests/delete.php";
    }

    /**
     * Elimina una solicitud.
     * Si se confirma la eliminación, también elimina las rutas asociadas a la solicitud.
     * @param int $id Identificador de la solicitud a eliminar.
     */
    public function delete($id)
    {
        if (isset($_POST['confirmar'])) {
            if ($_POST['confirmar'] === 'Sí') {
                //Eliminamos la(s) ruta(s) asociada(s) a esa solicitud
                $request = $this->requestModel->getRequestDetails($id); //Obtenemos todos los detalles de la solicitud

                $routeModel = new RouteModel();
                foreach ($request as $ruta) {
                    $id_ruta = $ruta['id_ruta']; //Obtenemos la id de la ruta
                    $routeModel->deleteRoute($id_ruta);
                }
                $this->requestModel->deleteRequest($id);
                $this->index();
            } elseif (isset($_POST['confirmar']) && $_POST['confirmar'] === 'No') {
                $this->show($id);
            }
            exit();
        }
    }

    /**
     * Agrega materiales a una solicitud.
     * Busca el almacén con stock suficiente y asigna rutas logísticas basadas en distancia o tiempo, según 
     * el criterio seleccionado por el usuario.
     */
    public function addMaterial()
    {
        global $ubicaciones;
        
        $materialModel = new MaterialModel();
        $rutaModel = new RouteModel();

        //Valida los datos del formulario
        if (
            isset($_POST['pn_material'], $_POST['cantidad'], $_POST['criterio']) &&
            is_array($_POST['pn_material']) &&
            is_array($_POST['cantidad']) &&
            count($_POST['pn_material']) === count($_POST['cantidad'])
        ) {
            $pn_materiales = array_map('strval', $_POST['pn_material']);
            $cantidades = array_map('intval', $_POST['cantidad']);
            $sede_usuario = $_SESSION['user']['sede'];
            $criterio = $_POST['criterio']; //"distancia" o "tiempo"

            $rutas = []; //Se evitan rutas duplicadas
            $material_encontrado = false; //Monitor para verificar si hay stock en algún almacén

            foreach ($pn_materiales as $index => $pn_material) {
                $cantidad_solicitada = intval($cantidades[$index]);

                if ($cantidad_solicitada > 0) {
                    $origen_optimo = null;
                    $valor_minimo = PHP_INT_MAX; //Se inicializa con un valor muy alto

                    //Busca el almacén con stock suficiente
                    foreach ($ubicaciones as $sede => $coordenadas) {
                        if ($sede !== $sede_usuario) { //Evita que el origen sea la propia sede del usuario
                            $material = $materialModel->getMaterialByPartnumberAndAlmacen($pn_material, $sede);

                            if ($material && $material['stock'] >= $cantidad_solicitada) {
                                if ($criterio === 'distancia') {
                                    $valor_actual = calcularDistancia($ubicaciones[$sede_usuario], $coordenadas);
                                } else { //"tiempo"
                                     $valor_actual = calcularTiempoViaje($ubicaciones[$sede_usuario], $coordenadas);
                                }

                                if ($valor_actual < $valor_minimo) {
                                    $valor_minimo = $valor_actual;
                                    $origen_optimo = $sede;
                                }
                            }
                        }
                    }
                    //Si encontramos un almacén con stock suficiente
                    if ($origen_optimo) {
                        $material_encontrado = true;
                        break; //No seguimos buscando, ya sabemos que hay stock
                    }
                }
            }

            //Si no se encontró stock en ningún almacén, mostramos un mensaje y salimos
            if (!$material_encontrado) {
                echo "<script>alert('No se encontró stock suficiente en ningún almacén para los materiales solicitados.');</script>";
                $this->index();
                exit();
            }

            //Se crea la solicitud solo si hay material disponible
            $id_solicitud = $this->requestModel->createRequest($_SESSION['user']['id']);

            //Ahora otra vez a procesar los materiales para agregarlos a la solicitud
            foreach ($pn_materiales as $index => $pn_material) {
                $cantidad_solicitada = intval($cantidades[$index]);

                if ($cantidad_solicitada > 0) {
                    $origen_optimo = null;
                    $valor_minimo = PHP_INT_MAX;

                    foreach ($ubicaciones as $sede => $coordenadas) {
                        if ($sede !== $sede_usuario) {
                            $material = $materialModel->getMaterialByPartnumberAndAlmacen($pn_material, $sede);

                            if ($material && $material['stock'] >= $cantidad_solicitada) {
                                if ($criterio === 'distancia') {
                                    $valor_actual = calcularDistancia($ubicaciones[$sede_usuario], $coordenadas);
                                } else {
                                    $valor_actual = calcularTiempoViaje($ubicaciones[$sede_usuario], $coordenadas);
                                }

                                if ($valor_actual < $valor_minimo) {
                                    $valor_minimo = $valor_actual;
                                    $origen_optimo = $sede;
                                }
                            }
                        }
                    }
                    //Si se ha encontrado un origen óptimo se mostrará un mensaje en función de si se estableció por distancia o por tiempo
                    if ($origen_optimo) {
                        $mensaje_alerta = ($criterio === 'distancia')
                            ? "El origen más cercano con stock para el material {$pn_material} es {$origen_optimo} a " . round($valor_minimo, 1) . " km de distancia."
                            : "El destino más rápido con stock para el material {$pn_material} es {$origen_optimo} con un tiempo de viaje de " . round($valor_minimo, 2) . " horas.";

                        echo "<script>alert('$mensaje_alerta');</script>";

                        //Se crea un array que representa una ruta única, y se codifica en json para su uso posterior
                        $ruta_clave = json_encode([$origen_optimo, $sede_usuario]);

                        //Si la ruta todavía no exite, se crea
                        if (!isset($rutas[$ruta_clave])) {
                            $rutas[$ruta_clave] = $rutaModel->insertRoute($origen_optimo, $sede_usuario);
                        }
                        //Obtenemos la id de la ruta
                        $id_ruta = $rutas[$ruta_clave];
                        //Obtenemos el material a transportar
                        $material = $materialModel->getMaterialByPartnumberAndAlmacen($pn_material, $origen_optimo);

                        //Agregamos a la tabla "solicitud_material" los datos de la solicitud
                        $this->requestModel->addMaterialToRequest($id_solicitud, $material['id_material'], $cantidad_solicitada, $id_ruta);
                    }
                }
            }
            //Redirecciona al index de la solicitud
            $this->index();
            exit();
        } else {
            echo "<script>alert('Error en los datos introducidos.');</script>";
        }
    }

    /**
     * Finaliza una solicitud.
     * Marca la solicitud como finalizada y actualiza el stock de los materiales.
     * @param int $id Identificador de la solicitud a finalizar.
     */
    public function finalizeRequest($id)
    {
        $materialModel = new MaterialModel();

        actualizaMaterialTransportado(); //Actualizamos el material en la base de datos
        $this->requestModel->finalizeRequest($id); //Finalizamos la solicitud

        //Obtenemos los datos de toda la solicitud
        $solicitud = $this->requestModel->getRequestDetails($id);

        //Obtenemos los datos de los materiales transportados que se encuentran ya en destino
        foreach ($solicitud as $material) {
            $materiales[] = $materialModel->getMaterialByPartnumberAndAlmacen($material['material_pn'], $material['sede']);
        }

        //Ponemos el stock a 0
        foreach ($materiales as $material) {
            $materialModel->setStock($material['id_material'], 0);
        }

        $this->index();
    }
}
