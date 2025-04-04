<?php
//Se incluyen los archivos de configuración y la gestión de sesiones
require_once $_SERVER['DOCUMENT_ROOT'] . "/PROYECTO/Tarea03_4/config.php";
require_once BASE_PATH . "sesion.php";  //Incluir sesión
require BASE_PATH . 'modelos/RouteModel.php';
require_once BASE_PATH . 'modelos/MaterialModel.php';
require_once BASE_PATH . "recursos/funciones.php";

//Verifica si el usuario ha iniciado sesión; si no, lo redirige a la página de inicio
if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

/**
 * Clase RoutetController
 * Controlador encargado de gestionar la lógica de las rutas logísticas.
 */
class RouteController
{
    private $routeModel;

    /**
     * Contructor de la clase
     */
    public function __construct()
    {
        $this->routeModel = new RouteModel();
    }

    /**
     * Actualiza los materiales transportados: Este método actualiza la tabla "material" para que en caso de 
     * generarse una nueva ruta, el material esté actualizado en la base de datos.
     * Obtiene las rutas para mostrarlas.
     * Elimina las rutas con más de 365 días desde su llegada
     * Muestra un menú inicial con los tres tipos de rutas: En origen, en tránsito y en destino.
     * Se muestra cada tabla según se seleccione en el menú
     */
    public function index()
    {
        actualizaMaterialTransportado();
        $rutas = $this->routeModel->getAllRoutes();
        $rutas_origen = [];
        $rutas_transito = [];
        $rutas_destino = [];

        require BASE_PATH . 'vistas/routes/index.php';
    }

    /**
     * Elimina rutas con más de 365 días desde su finalización
     * @param $fecha: fecha para comparar con la actual
     * @param $id: Identificador de la ruta a eliminar
     */
    public function deleteOldRoute($fecha, $id)
    {
        $this->routeModel->deleteOldRoute($fecha, $id);
    }

    /**
     * Muestra los detalles de una ruta concreta, incluyendo los materiales que transporta.
     * @param $id: Identificador de la ruta a mostrar
     * 
     */
    public function show($id)
    {
        $ruta = $this->routeModel->selectRoute($id);

        //se obtienen materiales de la ruta
        $materialController = new MaterialController();
        $materiales = $materialController->getMaterialsByRoute($id);

        //si la ruta aún no ha salido, se considera "editable" por encontrarse en "origen" 
        $rutaEditable = empty($ruta['hora_salida']) || !cdate($ruta['fecha_salida'], $ruta['hora_salida']);
        require BASE_PATH . 'vistas/routes/show.php';
    }


    /**
     * Guarda los datos de una ruta una vez que ha sido editada.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $origen = htmlspecialchars($_POST['origen']);
            $destino = htmlspecialchars($_POST['destino']);
            $fecha_salida = htmlspecialchars($_POST['fecha_salida']);
            $hora_salida = htmlspecialchars($_POST['hora_salida']);
            $fecha_llegada = htmlspecialchars($_POST['fecha_llegada']);
            $hora_llegada = htmlspecialchars($_POST['hora_llegada']);
        }
        if ($this->routeModel->insertRoute($origen, $destino, $fecha_salida, $hora_salida, $fecha_llegada, $hora_llegada)) {
            $this->index();
        } else {
            echo "No se ha podido guardar la nueva ruta.";
        }
    }

    /**
     * Muestra un formulario de edición de una ruta cuyo Identificador obtenemos con post
     * o la elimina, según la acción recogida del formulario
     */
    public function edit()
    {
        $id_ruta = $_POST['id_ruta'];
        $accion = $_POST['accion'];

        if ($accion === "editar") {
            $ruta = $this->routeModel->selectRoute($id_ruta);
            if (!$ruta) {
                die("Ruta no encontrada");
            }
            require BASE_PATH . 'vistas/routes/edit.php';
        } else {
            $this->routeModel->deleteRoute($id_ruta);
            $this->index();
        }
    }

    /**
     * Actualiza una ruta concreta, y dirige al detalle de la ruta
     * @param $id: Identificador de la ruta a actualizar
     */
    //Actualiza la ruta y dirige al detalle
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $origen = htmlspecialchars($_POST['origen']);
            $destino = htmlspecialchars($_POST['destino']);
            $fecha_salida = htmlspecialchars($_POST['fecha_salida']);
            $hora_salida = htmlspecialchars($_POST['hora_salida']);
            $fecha_llegada = htmlspecialchars($_POST['fecha_llegada']);
            $hora_llegada = htmlspecialchars($_POST['hora_llegada']);

            if ($this->routeModel->updateRoute($id, $origen, $destino, $fecha_salida, $hora_salida, $fecha_llegada, $hora_llegada)) {
                $this->show($id);
            }
        }
    }

    /**
     * Finaliza una ruta concreta, es decir, su campo "finalizada" lo pone a 1
     * @param $id_ruta: Identificador de la ruta a finalizar
     */
    public function finalizeRoute($id_ruta)
    {
        $this->routeModel->finalizeRoute($id_ruta);
    }
}
