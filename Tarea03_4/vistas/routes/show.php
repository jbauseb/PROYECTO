<?php
if (!isset($_SESSION['user'])) {
    header("Location:" . BASE_URL . "index.php");
    exit();
}

include BASE_PATH . "vistas/header.php";
include BASE_PATH . "vistas/nav.php";

//Verifica el rol del usuario
if (!in_array(user_rol(), ["Administrador", "Gestor"])) {
    echo "<div class='alert alert-danger text-center'>Acceso denegado. Sólo permitido para Administradores y Gestores</div>";
    exit();
}
?>
<div class="container mt-4">
    <h2 class="mb-3">Ruta <?= htmlspecialchars($id); ?>
        <?php if ($rutaEditable): ?>
            <span class="h4">(en origen)</span>
            <!-- Si la fecha actual es posterior a la de la ruta -->
        <?php elseif (cdate($ruta['fecha_llegada'], $ruta['hora_llegada'])): ?>
            <span class="h4">(en destino)</span>
        <?php else: ?>
            <span class="h4">(en tránsito)</span>
        <?php endif; ?>
    </h2>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Origen:</strong> <?= htmlspecialchars($ruta['origen']) ?></li>
        <li class="list-group-item">
            <strong>Fecha de salida:</strong> <?= fdate(htmlspecialchars($ruta['fecha_salida'] ?? '')) ?>
        </li>
        <li class="list-group-item"><strong>Hora de salida:</strong> <?= htmlspecialchars($ruta['hora_salida'] ?? '') ?></li>
        <li class="list-group-item"><strong>Destino:</strong> <?= htmlspecialchars($ruta['destino']) ?></li>
        <li class="list-group-item"><strong>Fecha prevista de llegada:</strong> <?= fdate(htmlspecialchars($ruta['fecha_llegada'] ?? '')) ?></li>
        <li class="list-group-item"><strong>Hora prevista de llegada:</strong> <?= htmlspecialchars($ruta['hora_llegada'] ?? '') ?></li>
    </ul>

    <form action="<?= BASE_URL ?>enrutador.php?controller=RouteController&method=edit" method="post">
        <input type="hidden" name="id_ruta" value="<?= htmlspecialchars($id) ?>">
        <?php if ($rutaEditable): ?>
            <button type="submit" name="accion" value="editar" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar</button>
        <?php endif; ?>
        <?php if (!$materiales): ?>
            <button type="submit" name="accion" value="eliminar" class="btn btn-danger">
                <i class="fas fa-trash"></i> Eliminar</button>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>enrutador.php?controller=RouteController&method=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Ir a rutas</a>
        <a href="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Ir a la solicitudes</a>
    </form>

    <h3 class="mt-4">Materiales transportados</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Part Number</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <!-- Llamamos al método del archivo funciones.php -->
                <?php materialesRuta($materiales); ?>
            </tbody>
        </table>
    </div>
</div>

<?php include BASE_PATH . "vistas/footer.php"; ?>