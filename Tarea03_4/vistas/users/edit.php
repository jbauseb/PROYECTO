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
        <div class="card-header bg-primary text-white text-center">
            <h4>Edición del empleado - ID <?= htmlspecialchars($id); ?></h4>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>enrutador.php?controller=UserController&method=update&id=<?= urlencode($user['id']); ?>" method="post">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id); ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><strong>Nombre:</strong></label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($user['nombre']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><strong>DNI:</strong></label>
                            <input type="text" name="dni" class="form-control" value="<?= htmlspecialchars($user['dni']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><strong>Email:</strong></label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><strong>Usuario:</strong></label>
                            <input type="text" name="usuario" class="form-control" value="<?= htmlspecialchars($user['usuario']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Password:</strong> <small>(Dejar en blanco para no cambiar)</small></label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><strong>Teléfono:</strong></label>
                            <input type="tel" name="telefono" class="form-control" value="<?= htmlspecialchars($user['telefono']); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><strong>Fecha de alta:</strong></label>
                            <input type="date" name="fecha_alta" class="form-control" value="<?= htmlspecialchars($user['fecha_alta']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><strong>Rol:</strong></label>
                            <select name="rol" class="form-select" required>
                                <?php foreach (['Administrador', 'Gestor', 'Técnico'] as $rol) : ?>
                                    <option value="<?= $rol ?>" <?= $user['rol'] === $rol ? 'selected' : ''; ?>><?= $rol ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><strong>Sede:</strong></label>
                            <select name="sede" class="form-select" required>
                                <?php foreach (['Madrid', 'Zaragoza', 'Sevilla', 'León', 'Albacete'] as $sede) : ?>
                                    <option value="<?= $sede ?>" <?= $user['sede'] === $sede ? 'selected' : ''; ?>><?= $sede ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
                    <a href="<?= BASE_URL ?>enrutador.php?controller=UserController&method=show&id=<?= urlencode($user['id']); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include BASE_PATH . "vistas/footer.php";
