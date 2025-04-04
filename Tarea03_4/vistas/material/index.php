<?php
include BASE_PATH . "vistas/header.php";
include BASE_PATH . "vistas/nav.php";
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-white-50  text-center">
            <h4>Búsqueda de material</h4>
        </div>
        <div class="card-body ">
            <form id="searchForm" action="<?= BASE_URL ?>enrutador.php?controller=MaterialController&method=buscaMaterial" method="post">
                <div class="row d-flex justify-content-center align-items-center">
                    <div class="col-md-1">
                        <label class="form-label">ID:</label>
                        <input type="text" id="id_material" name="id_material" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Part Number:</label>
                        <input type="text" id="partnumber" name="partnumber" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Almacén:</label>
                        <select id="almacen" name="almacen" class="form-select">
                            <option value="">Seleccionar almacén</option>
                            <?php foreach (["Madrid", "Zaragoza", "Sevilla", "León", "Albacete"] as $almacen): ?>
                                <option value="<?= $almacen; ?>"><?= $almacen; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php if ($_POST): ?>
        <h2 class="text-center">Listado de materiales</h2>
        <?php if (!empty($materiales)): ?>
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Part Number</th>
                        <th>Nombre</th>
                        <th>Almacén</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materiales as $material):
                        $avisoStock = "";
                        if ($material['stock'] == 0) :
                            $avisoStock = "<i class='fas fa-exclamation-triangle text-danger'></i>";
                        elseif ($material['stock'] <= $material['umbral_stock']):
                            $avisoStock = "<i class='fas fa-exclamation-triangle text-warning'></i>";
                        endif;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($material['id_material']); ?></td>
                            <td><?= htmlspecialchars($material['partnumber']); ?></td>
                            <td><?= htmlspecialchars($material['nombre']); ?></td>
                            <td><?= htmlspecialchars($material['almacen']); ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>enrutador.php?controller=MaterialController&method=show&id=<?= $material['id_material']; ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-info-circle"></i> Detalles
                                </a>
                                <?= $avisoStock; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <div class="alert alert-warning text-center mt-3">
                <i class="fas fa-exclamation-triangle"></i> No se encontraron resultados.
            </div>
    <?php endif;
    endif; ?>
</div>

<?php
include BASE_PATH . "vistas/footer.php";
