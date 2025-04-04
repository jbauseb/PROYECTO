<?php
include BASE_PATH . "vistas/header.php";
include BASE_PATH . "vistas/nav.php";

//Verifica rol del usuario
if (!in_array(user_rol(), ["Administrador", "Técnico"])) :
    echo "<div class='alert alert-danger text-center'>Acceso denegado. Solo autorizado a Administradores y Técnicos.</div>";
    exit();
endif;

if ($requests) {
    //Actualiza el estado de todas las solicitudes antes de mostrar la tabla
    foreach ($requests as $request) {
        $this->setStatus($request['id_solicitud']);
    }
?>
    <div class="container mt-4">
        <?php if (user_rol() === "Técnico"): ?>
            <p align="center"><i class='fas fa-info-circle'></i> <span>Los Técnicos pueden editar, eliminar y finalizar solicitudes de su sede.</span></p>
        <?php endif; ?>
        <h2 class="text-center">Solicitudes</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fecha inicio</th>
                        <th>Hora inicio</th>
                        <th>Usuario</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request) : ?>
                        <tr>
                            <td><?= htmlspecialchars($request['id_solicitud']); ?></td>
                            <td><?= fdate(htmlspecialchars($request['fecha_inicio'])); ?></td>
                            <td><?= htmlspecialchars($request['hora_inicio']); ?></td>
                            <td><?= htmlspecialchars($request['empleado_nombre']); ?></td>
                            <td><?= htmlspecialchars($request['estado']); ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=show&id=<?= $request['id_solicitud']; ?>"
                                    class="btn btn-info btn-sm">
                                    <i class="fas fa-info-circle"></i> Detalles
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php
} else {
    echo "<div class='container mt-4'><div class='alert alert-warning text-center'>No hay solicitudes registradas.</div>";
}
    ?>
    <div class="text-start mt-3">
        <a href="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=create" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Nueva solicitud
        </a>
    </div>
    </div>

    <?php
    include BASE_PATH . "vistas/footer.php";
