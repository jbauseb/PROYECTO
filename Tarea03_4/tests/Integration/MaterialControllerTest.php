<?php

use PHPUnit\Framework\TestCase;

require_once '../../PROYECTO/Tarea03_4/controladores/MaterialController.php';
require_once '../../PROYECTO/Tarea03_4/modelos/MaterialModel.php';

class MaterialControllerTest extends TestCase
{
    protected $db;
    protected $materialController;
    protected $materialModel;

    protected function setUp(): void
    {
        // Configuramos la base de datos y los objetos necesarios
        $this->db = new PDO('mysql:host=127.0.0.1;dbname=testdb_alm_system', 'administrador', '123');
        $this->materialModel = new MaterialModel($this->db);
        $this->materialController = new MaterialController();
    }

    public function testStoreMaterialInsert()
    {
        // Datos de un material que no existe
        $materialData = [
            'partnumber' => 'P1234',
            'nombre' => 'Material Test',
            'descripcion' => 'Descripción de prueba',
            'almacen' => 'Almacén 1',
            'stock' => 10,
            'umbral_stock' => 5
        ];

        // Simulamos la solicitud POST
        $_POST['partnumber'] = $materialData['partnumber'];
        $_POST['nombre'] = $materialData['nombre'];
        $_POST['descripcion'] = $materialData['descripcion'];
        $_POST['almacen'] = $materialData['almacen'];
        $_POST['stock'] = $materialData['stock'];
        $_POST['umbral_stock'] = $materialData['umbral_stock'];

        // Llamamos al método `store()` del controlador
        $this->materialController->store();

        // Verificamos si el material ha sido insertado en la base de datos
        $stmt = $this->db->prepare("SELECT * FROM material WHERE partnumber = ?");
        $stmt->execute([$materialData['partnumber']]);
        $material = $stmt->fetch(PDO::FETCH_ASSOC);

        // Comprobamos que el material haya sido insertado
        $this->assertNotEmpty($material);
        $this->assertEquals($materialData['partnumber'], $material['partnumber']);
        $this->assertEquals($materialData['stock'], $material['stock']);
    }

    public function testStoreMaterialUpdate()
    {
        // Datos de un material existente
        $materialData = [
            'partnumber' => 'P1234',
            'nombre' => 'Material Test',
            'descripcion' => 'Descripción de prueba',
            'almacen' => 'Almacén 1',
            'stock' => 5,
            'umbral_stock' => 2
        ];

        // Primero insertamos el material
        $this->materialModel->insertMaterial(
            $materialData['partnumber'],
            $materialData['nombre'],
            $materialData['descripcion'],
            $materialData['almacen'],
            $materialData['stock'],
            $materialData['umbral_stock']
        );

        // Simulamos la solicitud POST con nuevos datos de stock
        $_POST['partnumber'] = $materialData['partnumber'];
        $_POST['nombre'] = $materialData['nombre'];
        $_POST['descripcion'] = $materialData['descripcion'];
        $_POST['almacen'] = $materialData['almacen'];
        $_POST['stock'] = 10;  // Nuevo stock
        $_POST['umbral_stock'] = $materialData['umbral_stock'];

        // Llamamos al método `store()` del controlador para actualizar
        $this->materialController->store();

        // Verificamos si el stock se ha actualizado correctamente
        $stmt = $this->db->prepare("SELECT stock FROM material WHERE partnumber = ?");
        $stmt->execute([$materialData['partnumber']]);
        $material = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(15, $material['stock']);  // El nuevo stock debe ser 10 + 5 = 15
    }
    public function testDeleteMaterial()
    {
        // Datos de un material a eliminar
        $materialData = [
            'partnumber' => 'P1235',
            'nombre' => 'Material a Eliminar',
            'descripcion' => 'Descripción para eliminar',
            'almacen' => 'Almacén 1',
            'stock' => 5,
            'umbral_stock' => 3
        ];

        // Insertamos el material
        $this->materialModel->insertMaterial(
            $materialData['partnumber'],
            $materialData['nombre'],
            $materialData['descripcion'],
            $materialData['almacen'],
            $materialData['stock'],
            $materialData['umbral_stock']
        );

        // Obtenemos el ID del material insertado
        $stmt = $this->db->prepare("SELECT id_material FROM material WHERE partnumber = ?");
        $stmt->execute([$materialData['partnumber']]);
        $material = $stmt->fetch(PDO::FETCH_ASSOC);

        // Llamamos al método delete() del controlador
        $result = $this->materialController->delete($material['id_material']);

        // Verificamos que el material haya sido eliminado
        $this->assertTrue($result);

        // Comprobamos que el material ya no existe en la base de datos
        $stmt = $this->db->prepare("SELECT * FROM material WHERE partnumber = ?");
        $stmt->execute([$materialData['partnumber']]);
        $material = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEmpty($material);  // El material debería haber sido eliminado
    }
}
