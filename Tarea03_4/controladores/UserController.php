<?php
//Se incluyen los archivos de configuración y la gestión de sesiones
require_once __DIR__ . "/../config.php";
require_once BASE_PATH . "sesion.php";
require BASE_PATH . 'modelos/UserModel.php';
require_once BASE_PATH . "recursos/funciones.php";

//Verifica si el usuario ha iniciado sesión; si no, lo redirige a la página de inicio
if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

/**
 * Clase UserController
 * Controlador encargado de gestionar la lógica de los usuarios.
 * Se encarga de, entre otros métodos, listar, buscar, crear, actualizar y eliminar usuarios.
 */
class UserController
{
    private $userModel;

    /**
     * Constructor de la clase
     */
    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Obtiene todos los usuarios y los muestra en la página
     */
    public function index()
    {
        $users = $this->userModel->getAllUsers();
        require BASE_PATH . 'vistas/users/index.php';
    }


    /**
     * Muestra los detalles de un usuario concreto.
     * @param $id Identificador del usuario
     */
    public function show($id)
    {
        $user = $this->userModel->selectUserBy('id', $id);
        require BASE_PATH . 'vistas/users/show.php';
    }

    /**
     * Busca a un usuario según los filtros indicados, que podrán ser uno o más de uno,
     * y lo muestra en pantalla.
     */
    public function searchUser()
    {
        $filtros = [
            'id'      => $_POST['id_empleado'] ?? '',
            'nombre'  => $_POST['nombre_empleado'] ?? '',
            'dni'     => $_POST['dni'],
            'rol'     => $_POST['rol'] ?? '',
            'sede'    => $_POST['sede'] ?? '',
        ];

        $users = $this->userModel->searchUser($filtros);

        require BASE_PATH . "vistas/users/index.php";
    }


    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        require BASE_PATH . 'vistas/users/add.php';
    }

    /**
     * Obtiene los datos de un formulario y crea con esos datos un nuevo usuario.
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dni = htmlspecialchars($_POST['dni']);
            $nombre = htmlspecialchars($_POST['nombre']);
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : null;
            $usuario = htmlspecialchars($_POST['usuario']);
            $telefono = htmlspecialchars($_POST['telefono']);
            $rol = htmlspecialchars($_POST['rol']);
            $sede = htmlspecialchars($_POST['sede']);
            $fecha_alta = htmlspecialchars($_POST['fecha_alta']);
            $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        if ($this->userModel->insertUser($dni, $nombre, $email, $usuario, $passwordHash, $telefono, $rol, $sede, $fecha_alta)) {
            $this->index();
        } else {
            echo "<p>No se ha podido guardar el nuevo empleado</p>";
        }
    }

    /**
     * Muestra el formulario para editar a un usuario concreto, obtenido su identificador por post
     */
    public function edit()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            die("Id no proporcionado.");
        }
        $user = $this->userModel->selectUserBy('id', $id);
        if (!$user) {
            die("Usuario no encontrado");
        }
        require BASE_PATH . 'vistas/users/edit.php';
    }
    /**
     * Actualiza al usuario según los datos obtenidos del formulario, y dirige al detalle.
     * @param $id identificador del usuario a actualizar
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dni = htmlspecialchars($_POST['dni']);
            $nombre = htmlspecialchars($_POST['nombre']);
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : null;
            $usuario = htmlspecialchars($_POST['usuario']);
            $telefono = htmlspecialchars($_POST['telefono']);
            $rol = htmlspecialchars($_POST['rol']);
            $sede = htmlspecialchars($_POST['sede']);
            $fecha_alta = htmlspecialchars($_POST['fecha_alta']);
            $passwordHash = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

            if ($this->userModel->updateUser($id, $dni, $nombre, $email, $usuario, $telefono, $rol, $sede, $fecha_alta, $passwordHash)) {

                //Redirige a la vista del usuario
                $this->show($id);
            } else {
                echo "Error al actualizar el usuario.";
            }
        }
    }

    /**
     * Muestra el formulario para eliminar a un usuario concreto, según el id obtenido por 
     * post desde un formulario
     */
    public function remove()
    {
        $id = $_POST['id'] ?? null;
        if (!$id || !($this->userModel->selectUserBy('id', $id))) {
            die("Usuario no encontrado");
        }
        require BASE_PATH . 'vistas/users/delete.php';
    }

    /**
     * Elimina a un usuario concreto si se ha confirmado. En caso contrario, vuelve al detalle del usuario.
     * @param $id Identificador del usuario a eliminar
     */
    public function delete($id)
    {
        if (isset($_POST['confirmar'])) {
            if ($_POST['confirmar'] === 'Sí') {
                $this->userModel->deleteUser($id);
                $this->index();
            } elseif (isset($_POST['confirmar']) && $_POST['confirmar'] === 'No') {
                $this->show($id);
            }
            exit();
        }
    }
}
