<?php
include BASE_PATH . "vistas/header.php";
include BASE_PATH . "vistas/nav.php";

//Verifica sesión iniciada
if (!isset($_SESSION['user'])) :
    header("Location: " . BASE_URL . "index.php");
    exit();
endif;

//Verifica el rol del usuario
if (user_rol() !== "Administrador"):
    echo "<div class='container mt-5'><div class='alert alert-danger'>Acceso denegado. Solo autorizado a Administradores.</div></div>";
    exit();
endif;
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-white-50  text-center">
            <h4>Búsqueda de empleados</h4>
        </div>
        <div class="card-body ">
            <form id="searchForm" action="<?= BASE_URL ?>enrutador.php?controller=UserController&method=searchUser" method="post">
                <div class="row d-flex justify-content-center align-items-center">
                    <div class="col-md-1">
                        <label class="form-label">ID:</label>
                        <input type="text" id="id_empleado" name="id_empleado" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nombre:</label>
                        <input type="text" id="nombre_empleado" name="nombre_empleado" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">DNI:</label>
                        <input type="text" id="dni_search" name="dni" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rol:</label>
                        <select id="rol" name="rol" class="form-select">
                            <option value="">Seleccionar rol</option>
                            <?php foreach (["Administrador", "Gestor", "Técnico"] as $rol): ?>
                                <option value="<?= $rol; ?>"><?= $rol; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sede:</label>
                        <select id="sede" name="sede" class="form-select">
                            <option value="">Seleccionar sede</option>
                            <?php foreach (["Madrid", "León", "Zaragoza", "Albacete", "Sevilla"] as $sede): ?>
                                <option value="<?= $sede; ?>"><?= $sede; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-start mt-2 mb-3">
        <a href="<?= BASE_URL ?>enrutador.php?controller=UserController&method=create" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Agregar empleado
        </a>
    </div>

    <?php if ($_POST): ?>
        <h2 class="mb-4 text-center">Lista de empleados</h2>

        <?php try {
            if (!empty($users)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>DNI</th>
                                <th>Rol</th>
                                <th>Sede</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']); ?></td>
                                    <td><?= htmlspecialchars($user['nombre']); ?></td>
                            
                                    <td><?= htmlspecialchars($user['dni']); ?></td>
                                    <td><?= htmlspecialchars($user['rol']); ?></td>
                                    <td><?= htmlspecialchars($user['sede']); ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>enrutador.php?controller=UserController&method=show&id=<?= urlencode($user['id']); ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Detalles
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">No existen empleados registrados</div>
            <?php endif; ?>
    <?php
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
            exit();
        }
    endif; ?>
</div>

<!-- Validaciones -->
<script src="<?= BASE_URL . 'recursos/js/user.js' ?>"></script>

<?php include BASE_PATH . "vistas/footer.php";
