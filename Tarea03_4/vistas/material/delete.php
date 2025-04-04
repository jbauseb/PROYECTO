<?php
include BASE_PATH . 'vistas/header.php';
include BASE_PATH . 'vistas/nav.php';

//Verifica el rol del usuario
if (user_rol() !== "Administrador") {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'><i class='fas fa-user-shield'></i> Acceso denegado. Solo permitido a Administradores.</div></div>";
    header("Refresh: 3; URL=" . BASE_URL . "home.php"); // Redirige después de 3 segundos
    include BASE_PATH . 'vistas/footer.php';
    exit();
}

//Valida que el ID esté presente y sea numérico
if (!isset($id) || !is_numeric($id)) {
    echo "<div class='container mt-5'><div class='alert alert-warning text-center'><i class='fas fa-exclamation-circle'></i> ID de empleado inválido.</div></div>";
    include BASE_PATH . 'vistas/footer.php';
    exit();
}
?>

<div class="container mt-5">
    <div class="card shadow-lg border-danger">
        <div class="card-header bg-danger text-white text-center">
            <h4><i class="fas fa-exclamation-triangle"></i> Confirmar eliminación</h4>
        </div>
        <div class="card-body text-center">
            <p class="fs-5">¿Seguro que deseas eliminar el material con <strong>ID <?= htmlspecialchars($id); ?></strong>?</p>
            <p class="text-danger fw-bold"><small>Esta acción es irreversible</small></p>

            <form method="post">
                <button type="submit" name="confirmar" value="Sí" class="btn btn-danger me-2 btn-lg">
                    <i class="fas fa-trash"></i> Sí, eliminar
                </button>
                <a href="<?= BASE_URL ?>enrutador.php?controller=MaterialController&method=show&id=<?= urlencode($id) ?>" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </form>
        </div>
    </div>
</div>

<?php include BASE_PATH . 'vistas/footer.php'; 