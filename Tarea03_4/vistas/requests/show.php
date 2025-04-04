<?php
//Verifica rol del usuario
if (!in_array(user_rol(), ["Administrador", "Técnico"])) :
    echo "<div class='alert alert-danger text-center'>Acceso denegado. Solo autorizado para Administradores y Técnicos.</div>";
    exit();
endif;

include BASE_PATH . "vistas/header.php";
include BASE_PATH . "vistas/nav.php";

if ($request): ?>
    <div class="container mt-4">

        <!-- Tabla de información de la solicitud -->
        <div class="card p-3 mb-4 shadow">
            <h4 class="card-title text-center">Detalles de la solicitud nº <?= htmlspecialchars($id) ?></h4>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha solicitud</th>
                        <th>Empleado</th>
                        <th>Rol</th>
                        <th>Sede</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <!-- Puede modificar la solicitud un Administrador o un empleado de la misma sede -->
                        <?php if ($request[0]['sede'] === user_sede() || user_rol() === "Administrador"):
                            // Si no está finalizada, se puede interactuar con la solicitud
                            if ($request[0]['estado'] === "Pendiente" || $request[0]['estado'] === "Entregado"): ?>
                                <th>Acciones</th>
                            <?php endif;
                            if ($request[0]['estado'] === "Finalizada"): ?>
                                <th>Fecha fin</th>
                        <?php endif;
                        endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= fdate(htmlspecialchars($request[0]['fecha_inicio'])) . "<br>" . htmlspecialchars($request[0]['hora_inicio']); ?></td>
                        <td><?= htmlspecialchars($request[0]['empleado_nombre']); ?></td>
                        <td><?= htmlspecialchars($request[0]['rol']); ?></td>
                        <td><?= htmlspecialchars($request[0]['sede']); ?></td>
                        <td><?= htmlspecialchars($request[0]['email']); ?></td>
                        <td><?= htmlspecialchars($request[0]['estado']); ?></td>
                        <?php
                        if ($request[0]['sede'] === user_sede() || user_rol() === "Administrador"):
                            //Si está "Pendiente" todavía se puede editar o eliminar
                            if ($request[0]['estado'] === "Pendiente"): ?>
                                <td>
                                    <a href="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=edit&id=<?= htmlspecialchars($id) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=remove&id=<?= htmlspecialchars($id) ?>" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </td>
                            <?php endif;

                            //Si el estado es "Entregado", sólo la podemos "Finalizar". Es decir, la pieza ha llegado y el técnico la ha usado para reparar algo.
                            if ($request[0]['estado'] === "Entregado"): ?>
                                <td>
                                    <a href="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=finalizeRequest&id=<?= htmlspecialchars($id) ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-flag-checkered"></i> Finalizar
                                    </a>
                                </td>
                            <?php endif;
                            if ($request[0]['estado'] === "Finalizada"): ?>
                                <td><?= fdate(htmlspecialchars($request[0]['fecha_fin'])) . "<br>" . htmlspecialchars($request[0]['hora_fin']); ?></td>
                        <?php endif;
                        endif; ?>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tabla de materiales incluidos -->
        <div class="card p-3 shadow">
            <h4 class="card-title text-center">Materiales incluidos</h4>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Part Number</th>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Ruta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($request as $dato):
                        $total += $dato['cantidad']; ?>
                        <tr>
                            <td><?= htmlspecialchars($dato['material_pn']); ?></td>
                            <td><?= htmlspecialchars($dato['material_nombre']); ?></td>
                            <td><?= htmlspecialchars($dato['cantidad']); ?></td>
                            <td>
                                <?php if (!empty($dato['id_ruta'])): ?>
                                    <a href="<?= BASE_URL ?>enrutador.php?controller=RouteController&method=show&id=<?= htmlspecialchars($dato['id_ruta']) ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-truck"></i> Ruta <?= htmlspecialchars($dato['id_ruta']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Sin ruta</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" style="text-align: right">Total de materiales incluidos</td>
                        <td><strong><?= htmlspecialchars($total); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Botón de volver -->
        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
<?php else: ?>
    <div class='alert alert-danger text-center'>No existe una solicitud con ese id</div>
<?php endif; ?>

<?php include BASE_PATH . "vistas/footer.php"; ?>