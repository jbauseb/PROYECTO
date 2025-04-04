<?php
include BASE_PATH . 'vistas/header.php';
include BASE_PATH . 'vistas/nav.php';

//Verifica el rol del usuario
if (user_rol() !== "Administrador") :
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'>Acceso denegado. Sólo autorizado a Administradores.</div></div>";
    exit();
endif;
?>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-danger text-white text-center">
            <h4><i class="fas fa-exclamation-triangle"></i> Confirmar eliminación</h4>
        </div>
        <div class="card-body text-center">
            <p class="fs-5">¿Seguro que deseas eliminar al empleado con <strong>ID <?= htmlspecialchars($id); ?></strong>?</p>
            <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>

            <form action="<?= BASE_URL ?>enrutador.php?controller=UserController&method=delete&id=<?= urlencode($id) ?>" method="post">
                <button type="submit" name="confirmar" value="Sí" class="btn btn-danger me-2">
                    <i class="fas fa-trash"></i> Sí, eliminar
                </button>
                <a href="<?= BASE_URL ?>enrutador.php?controller=UserController&method=show&id=<?= urlencode($id)?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </form>
        </div>
    </div>
</div>

<?php include BASE_PATH . "vistas/footer.php"; 
