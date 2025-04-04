<?php
require_once 'controladores/UserController.php';
require_once 'controladores/MaterialController.php';
require_once 'controladores/RouteController.php';
require_once 'controladores/RequestController.php';
require_once 'controladores/PDFController.php';

//Obtenemos los controladores y métodos por GET
$controller = isset($_GET['controller']) ? $_GET['controller'] : null;
$method = isset($_GET['method']) ? $_GET['method'] : null;

if ($controller && $method) {
    if (class_exists($controller)) {
        $instance = new $controller();
        
        //Obtiene todos los parámetros adicionales y se pasan dinámicamente
        if (method_exists($instance, $method)) {     
            $params = $_GET; //Aquí obtenemos todos los parámetros de la URL

            //Excluimos los parámetros 'controller' y 'method' que ya fueron procesados
            unset($params['controller'], $params['method']);

            //Llamamos al método con los parámetros restantes
            call_user_func_array([$instance, $method], $params);
        } else {
            die("Error: El método '$method' no existe en '$controller'.");
        }
    } else {
        die("Error: El controlador '$controller' no existe.");
    }
} else {
    die("Error: Parámetros inválidos.");
}
