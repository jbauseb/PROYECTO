<?php
//Verifica el rol del usuario
if (!in_array(user_rol(), ["Administrador", "Técnico"])):
    echo "<div class='alert alert-danger text-center'>Acceso denegado. Solo autorizado para Administradores y Técnicos.</div>";
    exit();
endif;

include BASE_PATH . 'vistas/header.php';
include BASE_PATH . 'vistas/nav.php';
?>

<div class="container mt-5">
    <div class="card shadow p-4 text-center">
        <h2 class="text-danger">Confirmar eliminación</h2>
        <p class="fs-5">¿Está seguro de que desea eliminar la solicitud nº <strong><?= htmlspecialchars($id); ?></strong>?</p>
        <p class="fs-5"><i class="fas fa-info-circle"></i> Se eliminarán las rutas asociadas a esa solicitud</p>
        
        <form action="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=delete&id=<?= htmlspecialchars($id); ?>" method="post">
            <button type="submit" name="confirmar" value="Sí" class="btn btn-danger me-2">Sí, eliminar</button>
            <a href="<?= BASE_URL ?>enrutador.php?controller=RequestController&method=index" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php include BASE_PATH . "vistas/footer.php";
