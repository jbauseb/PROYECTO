<?php
require_once BASE_PATH . 'vistas/header.php';
require_once BASE_PATH . 'vistas/nav.php';

//Verifica el rol del usuario
if (user_rol() !== "Administrador") :
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'>Acceso denegado. Sólo autorizado a Administradores.</div></div>";
    exit();
endif;
?>

<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-user"></i> Ficha del empleado con id: <?= htmlspecialchars($id ?? 'Desconocido'); ?></h5>
        </div>
        <div class="card-body">
            <?php if (!empty($user)) : ?>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Usuario:</strong> <?= htmlspecialchars($user['usuario']); ?></li>
                    <li class="list-group-item"><strong>Nombre:</strong> <?= htmlspecialchars($user['nombre']); ?></li>
                    <li class="list-group-item"><strong>DNI:</strong> <?= htmlspecialchars($user['dni']); ?></li>
                    <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></li>
                    <li class="list-group-item"><strong>Teléfono:</strong> <?= htmlspecialchars($user['telefono']); ?></li>
                    <li class="list-group-item"><strong>Rol:</strong> <?= htmlspecialchars($user['rol']); ?></li>
                    <li class="list-group-item"><strong>Sede:</strong> <?= htmlspecialchars($user['sede']); ?></li>
                    <li class="list-group-item"><strong>Fecha de alta:</strong> <?= fdate(htmlspecialchars($user['fecha_alta'])); ?></li>
                </ul>

                <!-- Botones de acción -->
                <div class="text-center mt-4">
                    <form action="<?= BASE_URL ?>enrutador.php?controller=UserController&method=edit" method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($id); ?>">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar</button>
                    </form>

                    <form action="<?= BASE_URL ?>enrutador.php?controller=UserController&method=remove" method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($id); ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </form>

                    <a href="<?= BASE_URL ?>enrutador.php?controller=UserController&method=index" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

            <?php else : ?>
                <div class="alert alert-warning text-center">No se pueden mostrar los detalles de este empleado.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include BASE_PATH . "vistas/footer.php";
