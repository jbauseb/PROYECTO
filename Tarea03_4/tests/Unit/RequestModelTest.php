<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../modelos/RequestModel.php';
require_once __DIR__ . '/../configTest.php';

class RequestModelTest extends TestCase
{
    protected $db;

    //Creamos la conexión a la base de datos de prueba y llamamos al modelo
    protected function setUp(): void
    {
        $this->db = new PDO('mysql:host=127.0.0.1;dbname=' . DBNAME, DBUSER, DBPWD);
        $this->requestModel = new RequestModel($this->db);
    }

    //Test para comprobar que no hay solicitudes
    public function testGetAllRequests()
    {
        $requests = $this->requestModel->getAllRequests(); //Llamamos al método del modelo
        $this->assertEmpty($requests);
    }

    //Test para crear una solicitud
    public function testCreateRequest()
    {
        $empleadoId = 1; //Asumimos que el ID del empleado es 1
        $this->assertNotEmpty($this->requestModel->createRequest($empleadoId));
    }

    //Test para añadir un material a una solicitud
    public function testAddMaterialToRequest()
    {
        //Datos de prueba
        $id_solicitud = 1;
        $id_material = 2;
        $cantidad_solicitada = 5;
        $id_ruta = null;

        //Ejecutamos el método y verificamos que retorne true
        $resultado = $this->requestModel->addMaterialToRequest($id_solicitud, $id_material, $cantidad_solicitada, $id_ruta);
        $this->assertTrue($resultado);
    }

    //Test para actualizar el material de una solicitud
    public function testUpdateMaterialToRequest()
    {
        //Datos de prueba
        $id_solicitud = 1;
        $materiales[] = 2;
        $cantidades[] = 3;

        $resultado = $this->requestModel->updateMaterialToRequest($id_solicitud, $materiales, $cantidades);
        $this->assertTrue($resultado);
    }

    //Test negativo de obtención datos del array de una solicitud
    public function testGetRequestDetails()
    {
        $id_solicitud = -1;

        $resultado = $this->requestModel->getRequestDetails($id_solicitud);
        $this->assertEmpty($resultado);
    }

    //Test para finalizar solicitud
    public function testFinalizeRequest()
    {
        $id_solicitud = 1;

        $resultado = $this->requestModel->finalizeRequest($id_solicitud);
        $this->assertTrue($resultado);
    }

    //Test para eliminar una solicitud
    public function testDeleteRequest()
    {
        $this->assertTrue($this->requestModel->deleteRequest('1'));
    }
}
