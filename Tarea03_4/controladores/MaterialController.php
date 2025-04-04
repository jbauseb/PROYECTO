<?php
//Se incluyen los archivos de configuración y la gestión de sesiones
require_once $_SERVER['DOCUMENT_ROOT'] . "/PROYECTO/Tarea03_4/config.php";
require_once BASE_PATH . "sesion.php";  

//Verifica si el usuario ha iniciado sesión; si no, lo redirige a la página de inicio
if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

//Se incluyen el modelo de Materiales y funciones necesarias
require BASE_PATH . 'modelos/MaterialModel.php';
require_once BASE_PATH . "recursos/funciones.php";

/**
 * Clase MaterialController
 * Controlador encargado de gestionar la lógica de los materiales.
 * Se encarga de, entre otros métodos, listar, buscar, crear, actualizar y eliminar materiales.
 */
class MaterialController
{
    private $materialModel; //Se instancia del modelo de Materiales

    /**
     * Constructor de la clase
     */
    public function __construct()
    {
        $this->materialModel = new MaterialModel();
    }

    /**
     * Método index
     * Muestra la lista de materiales, actualizando previamente su estado.
     */
    public function index()
    {
        actualizaMaterialTransportado(); //Actualiza los materiales desde el fichero de funciones
        $materiales = $this->getAllMaterials();
        require BASE_PATH . 'vistas/material/index.php';
    }

    /**
     * Método getAllMaterials
     * Obtiene todos los materiales desde el modelo.
     * @return array Lista de materiales
     */
    public function getAllMaterials()
    {
        return $this->materialModel->getAllMaterials();
    }

    /**
     * Método getPartNumberWithStock
     * Obtiene los materiales que tienen stock disponible.
     * @return array Lista de materiales con stock
     */
    public function getPartNumberWithStock()
    {
        return $this->materialModel->getPartNumberWithStock();
    }

    /**
     * Método buscaMaterial
     * Realiza la búsqueda de materiales con los filtros proporcionados por el usuario.
     */
    public function buscaMaterial()
    {
        $filtros = [
            'id_material' => $_POST['id_material'] ?? '',
            'partnumber'  => $_POST['partnumber'] ?? '',
            'nombre'      => $_POST['nombre'] ?? '',
            'almacen'     => $_POST['almacen'] ?? '',
        ];

        $materiales = $this->materialModel->searchMaterial($filtros);

        //Carga la vista con los materiales encontrados
        require BASE_PATH . "vistas/material/index.php";
    }

    /**
     * Método show
     * Muestra los detalles de un material específico.
     * @param int $id ID del material a mostrar
     */
    public function show($id)
    {
        $material = $this->materialModel->selectMaterial($id);
        require BASE_PATH . 'vistas/material/show.php';
    }

    /**
     * Método create
     * Muestra el formulario de creación de un nuevo material.
     */
    public function create()
    {
        require BASE_PATH . 'vistas/material/add.php';
    }

    /**
     * Método store
     * Almacena un nuevo material o actualiza el stock si ya existe en el sistema.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pn = htmlspecialchars($_POST['partnumber']);
            $nombre = htmlspecialchars($_POST['nombre']);
            $descripcion = htmlspecialchars($_POST['descripcion']);
            $almacen = htmlspecialchars($_POST['almacen']);
            $stock = htmlspecialchars($_POST['stock']);
            $umbral_stock = htmlspecialchars($_POST['umbral_stock']);

            //Verifica si el material ya existe en el almacén
            $materialExistente = $this->materialModel->getMaterialByPartnumberAndAlmacen($pn, $almacen);

            if ($materialExistente) {
                //Si el material existe, actualiza su stock
                $nuevo_stock = $materialExistente['stock'] + $stock;
                $actualizacion = $this->materialModel->updateMaterialStock($materialExistente['id_material'], $nuevo_stock, $nombre, $descripcion, $umbral_stock);

                if (!$actualizacion) {
                    echo "Error al actualizar el material.";
                }
            } else {
                //Si el material no existe, lo inserta en la base de datos
                $insercion = $this->materialModel->insertMaterial($pn, $nombre, $descripcion, $almacen, $stock, $umbral_stock);
                if (!$insercion) {
                    echo "No se ha podido guardar el nuevo material.";
                }
            }
            //Redirige al índice
            $this->index();
        }
    }

    /**
     * Método edit
     * Muestra el formulario de edición de un material.
     */
    public function edit()
    {
        $id = $_POST['id_material'];

        if (!$id) {
            die("Id no proporcionado.");
        }

        $material = $this->materialModel->selectMaterial($id);
        if (!$material) {
            die("Material no encontrado");
        }

        require BASE_PATH . 'vistas/material/edit.php';
    }

    /**
     * Método update
     * Actualiza los datos de un material o lo elimina si el stock es 0.
     * @param int $id ID del material a actualizar
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $partnumber = htmlspecialchars($_POST['partnumber']);
            $nombre = htmlspecialchars($_POST['nombre']);
            $descripcion = htmlspecialchars($_POST['descripcion']);
            $almacen = htmlspecialchars($_POST['almacen']);
            $stock = intval($_POST['stock']);
            $umbral_stock = intval($_POST['umbral_stock']);

            //Si el stock es 0, solicita confirmación para eliminar el material
            if ($stock === 0) {
                if (isset($_POST['confirmar'])) {
                    if ($_POST['confirmar'] === 'Sí') {
                        if ($this->delete($id)) {
                            $this->index();
                            exit();
                        } else {
                            echo "<p>Error al eliminar el material.</p>";
                            exit();
                        }
                    } elseif ($_POST['confirmar'] === 'No') {
                        header("Location: " . BASE_URL . "enrutador.php?controller=MaterialController&method=show&id=" . urlencode($id));
                        exit();
                    }
                } else {
                    require BASE_PATH . "vistas/material/delete.php";
                    exit();
                }
            } else {
                //Si no se elimina, actualiza los datos del material
                if ($this->materialModel->updateMaterial($id, $partnumber, $nombre, $descripcion, $almacen, $stock, $umbral_stock)) {
                    header("Location: " . BASE_URL . "enrutador.php?controller=MaterialController&method=show&id=" . urlencode($id));
                    exit();
                } else {
                    echo "Error al actualizar el material.";
                }
            }
        } else {
            echo "No se han recibido los datos del formulario.";
        }
    }

    /**
     * Método delete
     * Elimina un material de la base de datos.
     * @param int $id ID del material a eliminar
     * @return bool Indica si la eliminación fue correcta
     */
    public function delete($id)
    {
        return $this->materialModel->deleteMaterial($id);
    }

    /**
     * Método getMaterialsByRoute
     * Obtiene los materiales asociados a una ruta específica.
     * @param int $id_ruta ID de la ruta
     * @return array Lista de materiales por ruta
     */
    public function getMaterialsByRoute($id_ruta)
    {
        return $this->materialModel->getMaterialsByRoute($id_ruta);
    }

    /**
     * Método getStock
     * Obtiene el stock de un material específico.
     * @param int $id_material ID del material
     * @return int Stock del material
     */
    public function getStock($id_material)
    {
        return $this->materialModel->getStock($id_material);
    }
}
