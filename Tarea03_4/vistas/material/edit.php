<?php
include BASE_PATH . "vistas/header.php";
include BASE_PATH . "vistas/nav.php";

//Verifica permisos de usuario
if (!in_array(user_rol(), ["Administrador", "Gestor"])) {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'><i class='fas fa-exclamation-triangle'></i> Acceso denegado. Solo Administradores y Gestores pueden acceder.</div></div>";
    include BASE_PATH . "vistas/footer.php";
    exit();
}

//Verifica si se proporciona un ID de material válido
if (!isset($id) || empty($material)) {
    echo "<div class='container mt-5'><div class='alert alert-warning text-center'><i class='fas fa-exclamation-circle'></i> No se ha proporcionado un ID válido o el material no existe.</div></div>";
    include BASE_PATH . "vistas/footer.php";
    exit();
}
?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4><i class="fas fa-edit"></i> Editar material (ID: <?= htmlspecialchars($id); ?>)</h4>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>enrutador.php?controller=MaterialController&method=update&id=<?= urlencode($id); ?>" method="post">

                <input type="hidden" name="id_material" value="<?= htmlspecialchars($id); ?>">

                <!-- Almacén -->
                <div class="mb-3">
                    <label for="almacen" class="form-label">Almacén:</label>
                    <select id="almacen" name="almacen" class="form-select" required>
                        <option value="">Seleccionar almacén</option>
                        <?php foreach (['Madrid', 'Zaragoza', 'Sevilla', 'León', 'Albacete'] as $almacen) : ?>
                            <option value="<?= $almacen; ?>" <?= ($material['almacen'] === $almacen) ? 'selected' : ''; ?>><?= $almacen; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Part Number -->
                <div class="mb-3">
                    <label for="partnumber" class="form-label">Part Number:</label>
                    <input type="text" id="partnumber" name="partnumber" class="form-control" value="<?= htmlspecialchars($material['partnumber'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <!-- Nombre -->
                <div class="mb-3">
                    <label for="nombre" class="form-label">Denominación:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($material['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <!-- Descripción -->
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" required><?= htmlspecialchars($material['descripcion'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <!-- Stock actual -->
                <div class="mb-3">
                    <label for="stock" class="form-label">Stock actual:</label>
                    <input type="number" id="stock" name="stock" class="form-control" value="<?= htmlspecialchars($material['stock'], ENT_QUOTES, 'UTF-8'); ?>" min="0" required>
                </div>

                <!-- Umbral de stock -->
                <div class="mb-3">
                    <label for="umbral_stock" class="form-label">Umbral de alerta de stock:</label>
                    <input type="number" id="umbral_stock" name="umbral_stock" class="form-control" value="<?= htmlspecialchars($material['umbral_stock'], ENT_QUOTES, 'UTF-8'); ?>" min="0" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="<?= BASE_URL ?>enrutador.php?controller=MaterialController&method=show&id=<?= $id; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include BASE_PATH . "vistas/footer.php"; 
