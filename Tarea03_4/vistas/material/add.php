<?php
require_once BASE_PATH . 'vistas/header.php';
require_once BASE_PATH . 'vistas/nav.php';

//Verifica permisos de usuario
if (!in_array(user_rol(), ["Administrador", "Gestor"])) {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'><i class='fas fa-exclamation-triangle'></i> Acceso denegado. Solo Administradores y Gestores pueden acceder.</div></div>";
    require_once BASE_PATH . 'vistas/footer.php';
    exit();
}
?>

<div class="container mt-4 d-flex justify-content-center">
    <div class="card shadow" style="max-width: 70%; width: 100%;"> <!-- Ancho máximo de 500px -->
        <div class="card-header bg-success text-white text-center">
            <h4>Alta de material</h4>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>enrutador.php?controller=MaterialController&method=store" method="post">

                <!-- Part Number -->
                <div class="mb-3">
                    <label for="partnumber" class="form-label">Part Number:</label>
                    <input type="text" id="partnumber" name="partnumber" class="form-control" required>
                </div>

                <!-- Nombre -->
                <div class="mb-3">
                    <label for="nombre" class="form-label">Denominación:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>

                <!-- Descripción -->
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" required></textarea>
                </div>

                <!-- Almacén -->
                <div class="mb-3">
                    <label for="almacen" class="form-label">Almacén:</label>
                    <select id="almacen" name="almacen" class="form-select" required>
                        <option value="">Seleccione almacén</option>
                        <?php foreach (['Madrid', 'Zaragoza', 'Sevilla', 'León', 'Albacete'] as $almacen): ?>
                            <option value="<?= htmlspecialchars($almacen); ?>"><?= htmlspecialchars($almacen); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Cantidad -->
                <div class="mb-3">
                    <label for="stock" class="form-label">Cantidad:</label>
                    <input type="number" id="stock" name="stock" class="form-control" min="0" required>
                </div>

                <!-- Umbral de alerta -->
                <div class="mb-3">
                    <label for="umbral_stock" class="form-label">Umbral de alerta de stock:</label>
                    <input type="number" id="umbral_stock" name="umbral_stock" class="form-control" min="0" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="<?= BASE_URL ?>home.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . 'vistas/footer.php';
