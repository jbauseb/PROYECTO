<?php

use PHPUnit\Framework\TestCase;

require_once '../../PROYECTO/Tarea03_4/modelos/RequestModel.php';

// Assert	          Qué verifica	                    Ejemplo
// assertTrue()	      Algo debe ser verdadero	        Después de insertar, filas afectadas > 0
// assertFalse()	  Algo debe ser falso	            Borrado fallido (0 filas)
// assertEquals()	  Que dos valores sean iguales	    El nombre recuperado de la DB es "Test Material"
// assertEmpty()	  Resultado vacío	                Buscar un material inexistente
// assertNotEmpty()	  Resultado no vacío	            Buscar un material que sí existe

/*Para conexión a BaseDatos de prueba en host local*/
define('DBUSER', 'administrador');
define('DBPWD', '123');
define('DBNAME', 'testdb_alm_system');

class RequestModelTest extends TestCase
{
    protected $db;

    //Creamos la conexión a la base de datos de prueba y llamamos al modelo
    protected function setUp(): void
    {
        $this->db = new PDO('mysql:host=127.0.0.1;dbname=' . DBNAME, DBUSER, DBPWD);
        $this->requestModel = new RequestModel($this->db);
    }

    /**
     * No hay solicitudes
     */
    public function testGetAllRequests()
    {
        $requests = $this->requestModel->getAllRequests(); //Llamamos al método del modelo
        $this->assertIsArray($requests);
        $this->assertEmpty($requests);
    }

    public function testCreateRequest()
    {
        $_SESSION['user'] = ['id' => 1]; //Asumimos que el ID del empleado es 1
        $this->assertNotEmpty($this->requestModel->createRequest($_SESSION['user']));
    }

    public function testDeleteRequest()
    {
        $this->assertTrue($this->requestModel->deleteRequest('1'));
    }
}
