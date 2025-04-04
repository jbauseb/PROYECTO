<?php
if (!isset($_SESSION['user'])) {
    header("Location: ./index.php");
    exit();
}

//Verifica el rol del usuario
if (!in_array(user_rol(), ["Administrador", "Gestor"])) {
    echo '<div class="alert alert-danger text-center">Acceso denegado.</div>';
    exit();
}

include BASE_PATH . "vistas/header.php";
include BASE_PATH . "vistas/nav.php";

//if ($accion === "editar") {
?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">Editar ruta <?= htmlspecialchars($id_ruta) ?></h4>
                    </div>
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>enrutador.php?controller=RouteController&method=update&id=<?= urlencode($id_ruta) ?>" method="post">
                            <div class="mb-3">
                                <label for="origen" class="form-label">Origen:</label>
                                <select class="form-select" id="origen" name="origen" required>
                                    <?php
                                    $ciudades = ["Madrid", "Zaragoza", "Sevilla", "León", "Albacete"];
                                    foreach ($ciudades as $ciudad) {
                                        $selected = ($ruta['origen'] === $ciudad) ? 'selected' : '';
                                        echo "<option value='$ciudad' $selected>$ciudad</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="destino" class="form-label">Destino:</label>
                                <select class="form-select" id="destino" name="destino" required>
                                    <?php
                                    foreach ($ciudades as $ciudad) {
                                        $selected = ($ruta['destino'] === $ciudad) ? 'selected' : '';
                                        echo "<option value='$ciudad' $selected>$ciudad</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="fecha_salida" class="form-label">Fecha de salida:</label>
                                <input type="date" class="form-control" id="fecha_salida" name="fecha_salida" value="<?= htmlspecialchars($ruta['fecha_salida']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="hora_salida" class="form-label">Hora de salida:</label>
                                <input type="time" class="form-control" id="hora_salida" name="hora_salida" value="<?= htmlspecialchars($ruta['hora_salida']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="fecha_llegada" class="form-label">Fecha prevista de llegada:</label>
                                <input type="date" class="form-control" id="fecha_llegada" name="fecha_llegada" value="<?= htmlspecialchars($ruta['fecha_llegada']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="hora_llegada" class="form-label">Hora prevista de llegada:</label>
                                <input type="time" class="form-control" id="hora_llegada" name="hora_llegada" value="<?= htmlspecialchars($ruta['hora_llegada']) ?>" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
                                <a href="<?= BASE_URL ?>enrutador.php?controller=RouteController&method=show&id=<?= htmlspecialchars($id_ruta) ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Validaciones -->
<script src="<?= BASE_URL . 'recursos/js/route.js' ?>"></script>
<?php
 //}

include BASE_PATH . "vistas/footer.php";
