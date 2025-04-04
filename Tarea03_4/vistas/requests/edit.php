<?php
//Verifica el rol del usuario
if (!in_array(user_rol(), ["Administrador", "Técnico"])):
    echo "<div class='alert alert-danger text-center'>Acceso denegado. Solo autorizado para Administradores y Técnicos.</div>";
    exit();
endif;

require_once BASE_PATH . 'vistas/header.php';
require_once BASE_PATH . 'vistas/nav.php';


?>

<div class="container mt-4">
    <h2 class="text-center text-primary">Edición de la solicitud nº <?= htmlspecialchars($id); ?></h2>

    <form action="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=update" method="post">
        <input type="hidden" name="id_solicitud" value="<?= htmlspecialchars($id); ?>">

        <div class="card p-3">
            <h4 class="card-title">Modifique la cantidad de los materiales</h4>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>P/N</th>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($request as $dato):
                        //Calculamos la cantidad máxima (stock) de cada material
                        $cantidad_max = $materialController->getStock($dato['id_material']);
                    ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($dato['id_material']); ?>
                                <input type="hidden" name="id_material[]" value="<?= htmlspecialchars($dato['id_material']); ?>">
                            </td>
                            <td><?= htmlspecialchars($dato['material_pn']); ?></td>
                            <td><?= htmlspecialchars($dato['material_nombre']); ?></td>
                            <td>
                                <input type="number" class="form-control" name="cantidad[]" value="<?= htmlspecialchars($cantidades[$dato['id_material']]); ?>" min="0" max="<?= htmlspecialchars($cantidad_max); ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
            <a href="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=show&id=<?= htmlspecialchars($id); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>   Volver
            </a>
        </div>
    </form>
</div>

<?php include BASE_PATH . "vistas/footer.php";
