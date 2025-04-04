<?php
//Requerimos archivos necesarios
require_once "./sesion.php";
require_once "./config.php";
require_once BASE_PATH . "/recursos/funciones.php";
require_once BASE_PATH . "/modelos/UserModel.php";
include BASE_PATH . "/vistas/header.php";

//Si no se cerró sesión, envía a home.php
if (isset($_SESSION['user'])) {
    header("Location: ./home.php");
    exit();
}

//Genera token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

//Procesa formulario
$error = null;
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    //Valida CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Token CSRF inválido o expirado.");
    }

    //Valida campos
    if (!empty($_POST['user']) && !empty($_POST['password'])) {
        $user = filter_var($_POST['user'], FILTER_SANITIZE_SPECIAL_CHARS);
        $password = $_POST['password'];

        $userModel = new UserModel();
        $error = login($user, $password, $userModel);
    } else {
        $error = "Por favor, complete todos los campos.";
    }
}
?>

<div class="login container-fluid">
    <div class="row vh-100">
        <!-- Sección con la imagen de fondo -->
        <div class="col-md-8 d-none d-md-block"
            style="background: url('recursos/imagenes/fondo.jpg') center/cover no-repeat;">
        </div>

        <!-- Contenedor del formulario -->
        <div class="col-md-4 d-flex flex-column justify-content-center mt-n4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <h1 class="text-center mb-3">Iniciar sesión</h1>

                        <!-- Mensaje de error -->
                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario -->
                        <form method="post" class="border p-4 rounded shadow-lg bg-white">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class=" d-flex flex-column align-items-center justify-content-start p-2">
                                <div class="mb-3 text-center">
                                    <label for="user" class="form-label fs-5"><i class="fas fa-user"></i></label>
                                    <input type="text" id="user" name="user" class="form-control ">

                                    <label for="password" class="form-label fs-5">Password</label>
                                    <input type="password" id="password" name="password" class="form-control ">


                                </div><button type="submit" class="btn btn-dark w-100 fs-5">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . "/vistas/footer.php"; ?>