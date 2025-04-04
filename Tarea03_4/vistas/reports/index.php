<?php
/* Para mostrar errores */
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
/** */
include BASE_PATH . "vistas/header.php";
include BASE_PATH . "vistas/nav.php";
?>

<!-- Contenedor principal con Bootstrap -->
<div class="report container mt-5">

    <div class="row">
        <!-- Informe de material -->
        <div class="col-md-3 mb-3">
            <a href="<?= BASE_URL ?>enrutador.php?controller=PDFController&method=materialReport" class="btn btn-block" target="_blank">Informe de material</a>
        </div>

        <!-- Informe de empleados -->
        <div class="col-md-3 mb-3">
            <a href="<?= BASE_URL ?>enrutador.php?controller=PDFController&method=empleadoReport" class="btn btn-block" target="_blank">Informe de empleados</a>
        </div>

        <!-- Informe de solicitudes -->
        <div class="col-md-3 mb-3">
            <a href="<?= BASE_URL ?>enrutador.php?controller=PDFController&method=requestReport" class="btn btn-block" target="_blank">Informe de solicitudes</a>
        </div>
        
        <!-- Informe de rutas -->
        <div class="col-md-3 mb-3">
            <a href="<?= BASE_URL ?>enrutador.php?controller=PDFController&method=routeReport" class="btn btn-block" target="_blank">Informe de rutas</a>
        </div>
    </div>
</div>

<?php
include BASE_PATH . "vistas/footer.php";
?>