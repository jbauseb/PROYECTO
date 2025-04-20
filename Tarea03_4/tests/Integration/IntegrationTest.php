<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../modelos/MaterialModel.php';
require_once __DIR__ . '/../../modelos/RequestModel.php';
require_once __DIR__ . '/../../modelos/RouteModel.php';
require_once __DIR__ . '/../../modelos/UserModel.php';
require_once __DIR__ . '/../../recursos/funciones.php';
require_once __DIR__ . '/../configTest.php';

class IntegrationTest extends TestCase
{
    protected $db;

    //Creamos la conexión a la base de datos de prueba y llamamos a los modelos
    protected function setUp(): void
    {
        $this->db = new PDO('mysql:host=127.0.0.1;dbname=' . DBNAME, DBUSER, DBPWD);
        $this->materialModel = new MaterialModel($this->db);
        $this->requestModel = new RequestModel($this->db);
        $this->routeModel = new RouteModel($this->db);
        $this->userModel = new UserModel($this->db);

        $this->db->beginTransaction();
    }
    protected function tearDown(): void
    {
        $this->db->rollBack();
    }

    public function testCrearSolicitudConMateriales()
    {
        //Crear usuario
        $dni = '98765432Z';
        $nombre_empleado = 'Empleado Test';
        $email = 'empleado_test@test.com';
        $usuario = 'empleadotest';
        $password = password_hash('password', PASSWORD_DEFAULT);
        $telefono = '987456321';
        $rol = 'Técnico';
        $sede = 'Madrid';
        $fecha_alta = date('Y-m-d');

        $empleadoId = $this->userModel->insertUser($dni, $nombre_empleado, $email, $usuario, $password, $telefono, $rol, $sede, $fecha_alta);

        //Crear material
        $pn = 'MAT-001';
        $nombre_material = 'Material Test';
        $descripcion = 'Material de prueba';
        $almacen = 'Madrid';
        $stock = 50;
        $umbral_stock = 5;
        $materialId = $this->materialModel->insertMaterial($pn, $nombre_material, $descripcion, $almacen, $stock, $umbral_stock);

        //Crear solicitud
        $solicitudId = $this->requestModel->createRequest($empleadoId);
        //Agregar material a solicitud
        $this->requestModel->addMaterialToRequest($solicitudId, $materialId, 10, 1);

        //Verificaciones
        $solicitud = $this->requestModel->getRequestDetails($solicitudId);
        $this->assertEquals($nombre_empleado, $solicitud[0]['empleado_nombre']);

        $materiales = $this->requestModel->getRequestDetails($solicitudId);
        $this->assertCount(1, $materiales);
        $this->assertEquals(10, $materiales[0]['cantidad']);
    }

    public function testCrearRutaYCambiarEstadoSolicitud()
    {
        //Creamos un usuario
        $dni = '87654321Y';
        $nombre_empleado = 'Empleado Ruta';
        $email = 'empleadoruta@test.com';
        $usuario = 'empleadoruta';
        $password = password_hash('password', PASSWORD_DEFAULT);
        $telefono = '874563210';
        $rol = 'Técnico';
        $sede = 'Zaragoza';
        $fecha_alta = date('Y-m-d');

        $empleadoId = $this->userModel->insertUser($dni, $nombre_empleado, $email, $usuario, $password, $telefono, $rol, $sede, $fecha_alta);

        //Creamos un material
        $pn = 'MAT-002';
        $nombre_material = 'Material Ruta';
        $descripcion = 'Material para rutas';
        $almacen = 'Zaragoza';
        $stock = 30;
        $umbral_stock = 3;

        $materialId = $this->materialModel->insertMaterial($pn, $nombre_material, $descripcion, $almacen, $stock, $umbral_stock);

        //Creamos solicitud
        $solicitudId = $this->requestModel->createRequest($empleadoId);

        //Creamos ruta, y la configuramos para que se encuentre "en tránsito"
        $rutaId = $this->routeModel->insertRoute('Zaragoza', 'Madrid');
        $this->routeModel->updateRoute($rutaId, 'Zaragoza', 'Madrid', date('Y-m-d'), date('H:i:s', strtotime('-1 minute')), date('Y-m-d', strtotime('+1 day')), date('H:i:s'));

        $this->requestModel->addMaterialToRequest($solicitudId, $materialId, 5, $rutaId);
        $this->requestModel->setStatus($solicitudId);

        // Verificar que el estado de la solicitud cambió a "en tránsito"
        $solicitud = $this->requestModel->getRequestDetails($solicitudId);
        $this->assertEquals('En tránsito', $solicitud[0]['estado']);
    }

    public function testEliminarMaterialDisparaTriggerEliminarSolicitud()
    {
        //Creamos un usuario
        $dni = '11223344X';
        $nombre_empleado = 'Empleado Trigger';
        $email = 'trigger@test.com';
        $usuario = 'triggeruser';
        $password = password_hash('password', PASSWORD_DEFAULT);
        $telefono = '601111111';
        $rol = 'Técnico';
        $sede = 'León';
        $fecha_alta = date('Y-m-d');
        $empleadoId = $this->userModel->insertUser($dni, $nombre_empleado, $email, $usuario, $password, $telefono, $rol, $sede, $fecha_alta);

        //Creamos un material
        $pn = 'MAT-003';
        $nombre_material = 'Material Trigger';
        $descripcion = 'Material que dispara trigger';
        $almacen = 'Albacete';
        $stock = 20;
        $umbral_stock = 2;
        $materialId = $this->materialModel->insertMaterial($pn, $nombre_material, $descripcion, $almacen, $stock, $umbral_stock);

        //Creamos solicitud
        $solicitudId = $this->requestModel->createRequest($empleadoId);

        //Asignamos una ruta
        $rutaId = 1;
        //Agregamos el material
        $this->requestModel->addMaterialToRequest($solicitudId, $materialId, 5, $rutaId);

        //Eliminamos el material de la solicitud poniendo la cantidad a 0
        $materiales = [$materialId];
        $cantidades = [0];
        $this->requestModel->updateMaterialToRequest($solicitudId, $materiales, $cantidades);

        //Intentamos recuperar la solicitud
        $solicitud = $this->requestModel->getRequestDetails($solicitudId);
        $this->assertEmpty($solicitud);
    }

    public function testSolicitudReduceStockMaterial()
    {
        //Creamos un usuario
        $dni = '99887766W';
        $nombre_empleado = 'Empleado Stock';
        $email = 'stock@test.com';
        $usuario = 'stockuser';
        $password = password_hash('password', PASSWORD_DEFAULT);
        $telefono = '622222222';
        $rol = 'Técnico';
        $sede = 'Sevilla';
        $fecha_alta = date('Y-m-d');
        $empleadoId = $this->userModel->insertUser($dni, $nombre_empleado, $email, $usuario, $password, $telefono, $rol, $sede, $fecha_alta);

        //Creamos un material
        $pn = 'MAT-004';
        $nombre_material = 'Material Strock';
        $descripcion = 'Material para pruebas stock';
        $almacen = 'Sevilla';
        $stock = 15;
        $umbral_stock = 5;
        $materialId = $this->materialModel->insertMaterial($pn, $nombre_material, $descripcion, $almacen, $stock, $umbral_stock);

        //Creamos solicitud
        $solicitudId = $this->requestModel->createRequest($empleadoId);

        //Asignamos una ruta
        $rutaId = 1;
        //Agregamos el material
        $this->requestModel->addMaterialToRequest($solicitudId, $materialId, 5, $rutaId);

        // Simular entrega => reducir stock manualmente
        $this->materialModel->setStock($materialId, 10);

        //Comprobamos que el nuevo stock coincide con 10
        $stock_material = $this->materialModel->getStock($materialId);
        $this->assertEquals(10, $stock_material);
    }

    public function testFinalizarSolicitud()
    {
        //Creamos un usuario
        $dni = '99887766V';
        $nombre_empleado = 'Empleado Finalizar';
        $email = 'finalizar@test.com';
        $usuario = 'finalizaruser';
        $password = password_hash('password', PASSWORD_DEFAULT);
        $telefono = '633333333';
        $rol = 'Técnico';
        $sede = 'Albacete';
        $fecha_alta = date('Y-m-d');
        $empleadoId = $this->userModel->insertUser($dni, $nombre_empleado, $email, $usuario, $password, $telefono, $rol, $sede, $fecha_alta);

        //Creamos un material
        $pn = 'MAT-005';
        $nombre_material = 'Material Finalizar';
        $descripcion = 'Material para pruebas finalizar';
        $almacen = 'León';
        $stock = 7;
        $umbral_stock = 2;
        $materialId = $this->materialModel->insertMaterial($pn, $nombre_material, $descripcion, $almacen, $stock, $umbral_stock);

        //Creamos solicitud
        $solicitudId = $this->requestModel->createRequest($empleadoId);
        //Asignamos una ruta
        $rutaId = 1;
        
        //Agregamos el material
        $this->requestModel->addMaterialToRequest($solicitudId, $materialId, 5, $rutaId);
        $this->requestModel->finalizeRequest($solicitudId);
        $solicitud = $this->requestModel->getRequestDetails($solicitudId);

        $this->assertEquals('Finalizada', $solicitud[0]['estado']);
        $this->assertNotNull($solicitud[0]['fecha_fin']);
        $this->assertNotNull($solicitud[0]['hora_fin']);
    }
}
