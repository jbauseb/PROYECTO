<?php

use PHPUnit\Framework\TestCase;

//Incluimos el archivo del modelo que vamos a probar
require_once __DIR__ . '/../../modelos/MaterialModel.php';
require_once __DIR__ . '/../configTest.php'; 


//Clase de prueba para el modelo MaterialModel
class MaterialModelTest extends TestCase
{
    protected $db;

    //Método que se ejecuta antes de cada test
    protected function setUp(): void
    {
        //Creamos la conexión a la base de datos
        $this->db = new PDO('mysql:host=127.0.0.1;dbname=' . DBNAME, DBUSER, DBPWD);
        //Instanciamos el modelo con esa conexión
        $this->materialModel = new MaterialModel($this->db);
    }

    //Test para verificar que se obtienen todos los materiales
    public function testGetAllMaterials()
    {
        $materials = $this->materialModel->getAllMaterials(); //Llama al método del modelo
        $this->assertNotEmpty($materials);                   //Verifica que no está vacío
    }

    //Test para buscar un material existente usando filtros
    public function testSearchMaterialEncontrado()
    {
        $filtros = ['id_material' => '6', 'partnumber' => 'P1002', 'nombre' => 'Material 2'];
        $material = $this->materialModel->searchMaterial($filtros);
        $this->assertNotEmpty($material); // Verifica que se encontró el material
    }

    //Test para buscar un material que no existe
    public function testSearchMaterialNoEncontrado()
    {
        $filtros = ['id_material' => '-1']; //ID inexistente
        $material = $this->materialModel->searchMaterial($filtros);
        $this->assertEmpty($material); //Verifica que el resultado está vacío
    }

    //Test para seleccionar un material existente por ID
    public function testSelectMaterialExistente()
    {
        $id_material = '1'; //ID de un material que debe existir
        $material = $this->materialModel->selectMaterial($id_material);
        $this->assertNotEmpty($material); //Verifica que se obtuvo un resultado
    }

    //Test para insertar un nuevo material
    public function testInsertMaterial()
    {
        // Datos de prueba
        $pn = 'P5432';
        $nombre = 'Material test';
        $descripcion = 'Descripción test';
        $almacen = 'Madrid';
        $stock = 10;
        $umbral_stock = 2;

        //Verifica que la inserción fue exitosa (retorna true)
        $this->assertTrue($this->materialModel->insertMaterial($pn, $nombre, $descripcion, $almacen, $stock, $umbral_stock));
    }

    //Test para eliminar un material existente
    public function testDeleteMaterial()
    {
        $id = '16'; //ID de un material que debe poder eliminarse
        $this->assertTrue($this->materialModel->deleteMaterial($id)); // Verifica que el borrado fue exitoso
    }

    //Test para actualizar un material existente
    public function testUpdateMaterial()
    {
        //Datos de prueba para actualizar
        $id = '20';
        $pn = 'P999Test';
        $nombre = 'MATERIAL TEST';
        $descripcion = 'UPDATE MATERIAL TEST';
        $almacen = 'ZARAGOZA';
        $stock = 100;
        $umbral_stock = 20;

        //Verifica que la actualización fue exitosa
        $this->assertTrue($this->materialModel->updateMaterial($id, $pn, $nombre, $descripcion, $almacen, $stock, $umbral_stock));
    }
}
