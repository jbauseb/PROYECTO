<?php
require_once "./sesion.php";
require_once "./config.php";
require_once BASE_PATH . "recursos/funciones.php";

//Si no hay sesión de usuario, deriva al index. 
if (!isset($_SESSION['user'])) :
    header("Location: " . BASE_URL . "index.php");
    exit();
endif;

//Incluimos archivos de vistas
include BASE_PATH . "vistas/header.php";
include BASE_PATH . "vistas/nav.php";
?>
<div class="container-fluid">
    <div class="row vh-100">
        <!-- Sección con la imagen de fondo -->
        <div class="col-md-8 d-none d-md-block"
            style="background: url('recursos/imagenes/fondo.jpg') center/cover no-repeat;">
        </div>
        <div class="col-md-4 d-flex flex-column align-items-start justify-content-start p-4">
            <div class="mt-5 text-start">
                <h5 class="mb-5">Ha iniciado sesión como:</h5>
                <i class="fas fa-user"></i>
                <p class="d-flex flex-column">
                    <strong> <?php echo $_SESSION['user']['nombre']; ?></strong>
                </p>
                <i class="fas fa-briefcase"></i>
                <p class="d-flex flex-column">
                    <strong> <?php echo user_rol(); ?></strong>
                </p>
                <i class="fas fa-building"></i>
                <p class="d-flex flex-column">
                    <strong> <?php echo user_sede(); ?></strong>
                </p>
            </div>
        </div>
    </div>
</div>

<?php
//Se incluye el pie de página
include BASE_PATH . "vistas/footer.php";
