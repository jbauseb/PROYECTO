<?php
include BASE_PATH . "vistas/header.php";
include BASE_PATH . "vistas/nav.php";

if ($rutas) {
    foreach ($rutas as $ruta) {
        // Se borran las rutas más antiguas (+365 días)
        $this->deleteOldRoute($ruta['fecha_llegada'], $ruta['id_ruta']);

        if (empty($ruta['fecha_salida']) || !cdate($ruta['fecha_salida'], $ruta['hora_salida'])) {
            $rutas_origen[] = $ruta;
        } elseif (cdate($ruta['fecha_salida'], $ruta['hora_salida']) && !cdate($ruta['fecha_llegada'], $ruta['hora_llegada'])) {
            $rutas_transito[] = $ruta;
        } else {
            $rutas_destino[] = $ruta;
        }
    }
?>
    <div class="container">
        <!-- Enlaces para cambiar entre tablas -->
        <div class="menu-links">
            <a href="#" onclick="mostrarTabla('origen')" title="Rutas que aún no han salido del almacén de origen">Rutas pendientes de salir</a>
            <a href="#" onclick="mostrarTabla('transito')" title="Rutas que están se encuentran en camino al destino">Rutas en tránsito</a>
            <a href="#" onclick="mostrarTabla('destino')" title="Rutas que ya han llegado a su destino y han entregado el material">Rutas finalizadas</a>
        </div>

        <?php if (user_rol() === "Gestor"): ?>
            <p align="center"><i class='fas fa-info-circle'></i> <span>A los Gestores sólo se les permite ver las rutas con origen o destino en su sede</span></p>        <?php endif; ?>
        <!-- Contenedores de tablas -->
        <div id="tabla_origen" class="tabla" style="display: none;">
            <?php tablaRutas("Rutas en origen  <i class='far fa-building'></i><i class='fas fa-truck'></i>", $rutas_origen); ?>
        </div>

        <div id="tabla_transito" class="tabla" style="display: none;">
            <?php tablaRutas("Rutas en tránsito  <i class='fas fa-shipping-fast'></i>", $rutas_transito); ?>
        </div>

        <div id="tabla_destino" class="tabla" style="display: none;">
            <?php tablaRutas("Rutas finalizadas  <i class='fas fa-truck'></i><i class='far fa-building'></i>", $rutas_destino); ?>
        </div>
    </div>
    <script src="<?= BASE_URL . 'recursos/js/route.js' ?>"></script>

<?php
} else {
    echo '<div class="container mt-4"><div class="alert alert-warning text-center" role="alert">
            No existen rutas para mostrar.
          </div></div>';
}

include BASE_PATH . "vistas/footer.php";
