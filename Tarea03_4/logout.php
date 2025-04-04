<?php
//Continuamos la sesión
session_start();

//Terminamos la sesión y eliminamos todas las variables
session_unset();
session_destroy();

//Redirigimos a la página principal
header("Location: ./index.php");
