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
    <h2 class="text-center mb-4">Dar de alta a empleado</h2>

    <form id="addForm" action="<?= BASE_URL ?>enrutador.php?controller=UserController&method=store" method="post" class="p-4 border rounded bg-light shadow">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label" title="Apellido1 Apellido2, Nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" title="12345678X">DNI:</label>
                <input type="text" id="dni" name="dni" class="form-control" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class=" col-md-6">
                <label class="form-label" title="sólo letras y números">Usuario:</label>
                <input type="text" id="usuario" name="usuario" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" title="correo@dominio.com">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label" title="máximo 9 dígitos">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Rol:</label>
                <select name="rol" class="form-select" required>
                    <option value="">Seleccione rol</option>
                    <?php foreach (['Administrador', 'Gestor', 'Técnico'] as $rol): ?>
                        <option value="<?= $rol ?>"><?= $rol ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Password:</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary" id="alternaPassword">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmar password:</label>
                <div class="input-group">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary" id="alternaConfirmPassword">
                        <i class="fas fa-eye" id="eyeConfirmIcon"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Sede:</label>
                <select name="sede" class="form-select" required>
                    <option value="">Seleccione sede</option>
                    <?php foreach (['Madrid', 'Zaragoza', 'Sevilla', 'León', 'Albacete'] as $sede): ?>
                        <option value="<?= $sede ?>"><?= $sede ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Fecha de alta:</label>
                <input type="date" name="fecha_alta" class="form-control" required>
            </div>
        </div>
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
            <a href="<?= BASE_URL ?>enrutador.php?controller=UserController&method=index" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
        </div>
    </form>
</div>

<!-- Validaciones -->
<script src="<?= BASE_URL . 'recursos/js/user.js' ?>"></script>

<?php include BASE_PATH . "vistas/footer.php";
