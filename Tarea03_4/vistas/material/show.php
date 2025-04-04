<?php
include BASE_PATH . "vistas/header.php";
include BASE_PATH . "vistas/nav.php";

//Verifica si el parámetro 'id_material' está en la URL
if (!isset($id) || empty($material)) {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'><i class='fas fa-exclamation-circle'></i> No se ha proporcionado un ID válido o el material no existe.</div></div>";
    include BASE_PATH . "vistas/footer.php";
    exit();
}
?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white text-center">
            <h4><i class="fas fa-box-open"></i> Detalles del material con id <?= htmlspecialchars($id); ?></h4>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item"><strong>Part Number:</strong> <?= htmlspecialchars($material['partnumber']); ?></li>
                <li class="list-group-item"><strong>Nombre:</strong> <?= htmlspecialchars($material['nombre']); ?></li>
                <li class="list-group-item"><strong>Descripción:</strong> <?= htmlspecialchars($material['descripcion']); ?></li>
                <li class="list-group-item"><strong>Almacén:</strong> <?= htmlspecialchars($material['almacen']); ?></li>
                <li class="list-group-item"><strong>Stock: </strong>
                    <?php
                    if ($material['stock'] > 0) :
                        if ($material['stock'] <= $material['umbral_stock']) :
                            echo htmlspecialchars($material['stock']) . " <i class='fa fa-exclamation-triangle text-warning'></i> (Stock por debajo de umbral)";
                        else:
                            echo htmlspecialchars($material['stock']);
                        endif;
                    else:
                        echo " <i class='fa fa-exclamation-triangle text-danger'></i> Sin stock";
                    endif;?></li>
                <li class="list-group-item"><strong>Umbral de Stock:</strong> <?= htmlspecialchars($material['umbral_stock']); ?></li>
            </ul>

            <div class="text-center mt-4">
                <?php if (in_array(user_rol(), ["Administrador", "Gestor"])) : ?>
                    <form action="<?= BASE_URL ?>enrutador.php?controller=MaterialController&method=edit" method="post" class="d-inline">
                        <input type="hidden" name="id_material" value="<?= htmlspecialchars($id); ?>">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Material
                        </button>
                    </form>
                <?php endif; ?>
                <a href='<?= BASE_URL ?>enrutador.php?controller=MaterialController&method=index' class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . "vistas/footer.php";
