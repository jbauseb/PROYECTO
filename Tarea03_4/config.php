<?php
/*Para mostrar errores*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/** */

/*Localización base del proyecto*/
// BASE_PATH: Ruta absoluta en el sistema de archivos
define("BASE_PATH", __DIR__ . "/");

// BASE_URL: Directorio base para despliegue local
define("BASE_URL", "http://localhost/PROYECTO/Tarea03_4/");
// BASE_URL: Directorio base para despliegue en Infinityfree
//define("BASE_URL", "http://bautistasebastiao.ct.ws/PROYECTO/Tarea03_4/");

/*Para conexión a BaseDatos en host local*/
define('DBUSER', 'administrador');
define('DBPWD', '123');
define('DBNAME', 'alm_system');

/*Para conexión a BaseDatos en Infinityfree*/
// define('DBUSER', 'if0_38497619');
// define('DBPWD', 's2oIAXn3u1iWuq');
 //define('DBNAME', 'if0_38497619_alm_system');


/**
 * Conecta a la base de datos. Se indican los parámetros según usemos un host local o el host de InfinityFree
 * 
 * @return la conexión
*/
function conectar()
{
    // Crea la conexión
    try {
        $conn = new PDO('mysql:host=localhost;dbname=' . DBNAME, DBUSER, DBPWD);
        // $conn = new PDO('mysql:host=sql206.infinityfree.com;dbname=' . DBNAME, DBUSER, DBPWD);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("set names utf8");
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        exit();
    }
    return $conn;
}
