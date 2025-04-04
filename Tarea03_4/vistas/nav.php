<?php

//Definimos los elementos del menú según el rol del usuario
$nav_items = [
    'Administrador' => [
        'Empleados' => BASE_URL . 'enrutador.php?controller=UserController&method=index',
        'Rutas logísticas' => BASE_URL . 'enrutador.php?controller=RouteController&method=index',
        'Ingeniería' => [
            'Consultar material' => BASE_URL .  'enrutador.php?controller=MaterialController&method=index',
            'Alta de pieza' => BASE_URL . 'enrutador.php?controller=MaterialController&method=create'
        ],
        'Mantenimiento' => [
            'Solicitudes' => BASE_URL . 'enrutador.php?controller=RequestController&method=index'
        ],
        'Informes' => BASE_URL . 'enrutador.php?controller=PDFController&method=index'
    ],
    'Gestor' => [
        'Rutas logísticas' => BASE_URL .  'enrutador.php?controller=RouteController&method=index',
        'Ingeniería' => [
            'Consultar material' => BASE_URL . 'enrutador.php?controller=MaterialController&method=index',
            'Alta de pieza' => BASE_URL .  'enrutador.php?controller=MaterialController&method=create'
        ],
        'Informes' => BASE_URL . 'enrutador.php?controller=PDFController&method=index'
    ],
    'Técnico' => [
        'Ingeniería' => [
            'Consultar material' => BASE_URL .  'enrutador.php?controller=MaterialController&method=index'
        ],
        'Mantenimiento' => [
            'Solicitudes' => BASE_URL . 'enrutador.php?controller=RequestController&method=index'
        ]
    ]
];

$usuario = $_SESSION['user']['usuario'] ?? 'Usuario desconocido';

//Verifica si el usuario tiene un menú asignado
$nav_html = $nav_items[user_rol()] ?? [];
?>

<!-- Barra de Navegación con Bootstrap -->
<nav class="navbar navbar-expand-lg px-5">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto fs-5">
            <li class="nav-item fs-5">
                <a class="nav-link" href="<?=BASE_URL?>home.php">Inicio</a>
            </li>
            <?php foreach ($nav_html as $label => $link): ?>
                <?php if (is_array($link)): ?>
                    <!-- Dropdown para submenús -->
                    <li class="nav-item dropdown fs-5">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown<?= $label; ?>" role="button" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($label); ?>
                        </a>
                        <ul class="dropdown-menu fs-5">
                            <?php foreach ($link as $sub_label => $sub_link): ?>
                                <li><a class="dropdown-item" href="<?= htmlspecialchars($sub_link); ?>"><?= htmlspecialchars($sub_label); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Opción normal sin dropdown -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars($link); ?>"><?= htmlspecialchars($label); ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>

            <!-- Botón de Logout -->
            <li class="nav-item">
                <a id="logout" class="nav-link btn px-3" href="<?= BASE_URL ?>logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<!-- Bootstrap JS (Para que los dropdowns funcionen ok) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>