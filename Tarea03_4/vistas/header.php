<?php
ob_start(); //Inicia un buffer de salida. Evita conflicto con la barra nav
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ALM System</title>

    <!-- Font Awesome para mostrar iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="96x96" href="<?= BASE_URL ?>/recursos/imagenes/favicon.png">

    <!-- CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?= BASE_URL ?>recursos/css/styles.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body>
    <header class="header bg-light py-2 shadow-sm">
        <div class="container-fluid d-flex justify-content-between align-items-center px-3">
            <div class="imagen"><img src="<?= BASE_URL ?>recursos/imagenes/logo.png" width="150"></div>
            <!-- Contenedor para la fecha, nombre de usuario y rol -->
            <div class="d-flex flex-column align-items-center gap-1">
                <!-- Fecha -->
                <div class="p-1 fecha">
                    <h6 class="text-secondary m-0">
                        <?php
                        setlocale(LC_TIME, 'es_ES.UTF-8'); // Ya no es necesario con IntlDateFormatter
                        $formatter = new IntlDateFormatter(
                            'es_ES',
                            IntlDateFormatter::FULL,
                            IntlDateFormatter::NONE,
                            'Europe/Madrid',
                            IntlDateFormatter::GREGORIAN,
                            "EEEE, d 'de' MMMM 'de' Y"
                        );
                        echo ucfirst($formatter->format(new DateTime()));
                        ?>
                    </h6>
                </div>
                <!-- Nombre y rol del usuario -->
                <div class="nombre">
                    <?php if (current_file() != 'home.php'):
                        if (isset($_SESSION['user'])): ?>
                            <h6 class="text-secondary m-0 text-start">
                                <i class="fas fa-user"></i> <?php echo $_SESSION['user']['nombre']; ?><br>
                                <i class="fas fa-briefcase"></i> <?php echo user_rol() ?><br>
                                <i class="fas fa-building"></i> <?php echo user_sede(); ?>
                            </h6>
                    <?php endif;
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </header>
