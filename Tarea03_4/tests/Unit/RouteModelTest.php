<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../modelos/RouteModel.php';
require_once __DIR__ . '/../configTest.php'; 

class RouteModelTest extends TestCase
{
    protected $db;

    //Creamos la conexión a la base de datos de prueba y llamamos al modelo
    protected function setUp(): void
    {
        $this->db = new PDO('mysql:host=127.0.0.1;dbname=' . DBNAME, DBUSER, DBPWD);
        $this->routeModel = new RouteModel($this->db);
    }

    //Test para comprobar que no hay rutas
    public function testGetAllRoutes()
    {
        $routes = $this->routeModel->getAllRoutes(); //Llamamos al método del modelo
        $this->assertEmpty($routes);
    }

    //Test para insertar una ruta nueva
    public function testInsertRoute()
    {
        $origen = 'Sevilla';
        $destino = 'Zaragoza';

        $resultado = $this->routeModel->insertRoute($origen, $destino);
        $this->assertNotEmpty($resultado);
    }

    //Test para finalizar una ruta
    public function testFinalizeRoute()
    {
        $id_ruta = 1;
        $resultado = $this->routeModel->finalizeRoute($id_ruta);
        $this->assertTrue($resultado);
    }
}
