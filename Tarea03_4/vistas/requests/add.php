<?php
if (!in_array(user_rol(), ["Administrador", "Técnico"])):
    echo "<div class='alert alert-danger text-center'>Acceso denegado. Solo autorizado a Administradores y Técnicos.</div>";
    exit();
endif;

include BASE_PATH . "vistas/header.php";
include BASE_PATH . "vistas/nav.php";
?>
<div class="container mt-4">
    <h2 class="text-center">Nueva solicitud</h2>

    <!-- Formulario para agregar materiales -->
    <div class="card p-3">
        <h4 class="card-title">Agregar materiales a la solicitud</h4>
        <form action="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=addMaterial" method="post">
            <input type="hidden" id="id_empleado" name="id_empleado" value="<?= htmlspecialchars($_SESSION['user']['id']) ?>">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>P/N</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materiales as $material) :
                            $cantidad_max = $material['total_stock'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($material['partnumber']); ?></td>
                                <input type="hidden" name="pn_material[]" value="<?= htmlspecialchars($material['partnumber']); ?>">
                                <td><?= htmlspecialchars($material['nombre']); ?></td>
                                <td><?= htmlspecialchars($material['descripcion']); ?></td>
                                <td>
                                    <input type="number" name="cantidad[]" value="0" min="0" max="<?= htmlspecialchars($cantidad_max); ?>" class="form-control">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <label class="form-label" for="criterio">Optimizar por: </label>
            <select name="criterio" id="criterio">
                <option value="distancia">Menor distancia</option>
                <option value="tiempo">Menor tiempo</option>
            </select>
            <button type="submit" class="btn btn-success">Solicitar</button>
        </form>
    </div>

    <br>
    <a href="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=index" class="btn btn-warning">Volver</a>
</div>

<?php include BASE_PATH . "vistas/footer.php"; ?>