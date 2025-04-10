<?php

use PHPUnit\Framework\TestCase;

require_once '../../PROYECTO/Tarea03_4/modelos/MaterialModel.php';

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

class MaterialModelTest extends TestCase
{
    protected $db;

    //Creamos la conexión a la base de datos de prueba y llamamos al modelo
    protected function setUp(): void
    {
        $this->db = new PDO('mysql:host=127.0.0.1;dbname=' . DBNAME, DBUSER, DBPWD);
        $this->materialModel = new MaterialModel($this->db);
    }

    public function testGetAllMaterials()
    {
        $materials = $this->materialModel->getAllMaterials(); //Llamamos al método del modelo
        $this->assertIsArray($materials);
        $this->assertNotEmpty($materials); //Al menos que exista 1 material
    }
    public function testSearchMaterialEncontrado()
    {
        $filtros = ['id_material' => '6', 'partnumber' => 'P1002', 'nombre' => 'Material 2'];
        $material = $this->materialModel->searchMaterial($filtros);
        $this->assertNotEmpty($material); //Existe ese material buscado
    }
    public function testSearchMaterialNoEncontrado()
    {
        $filtros = ['id_material' => '-1'];
        $material = $this->materialModel->searchMaterial($filtros);
        $this->assertEmpty($material); //No existe ese material buscado
    }
    public function testSelectMaterialExistente()
    {
        $id = '1';
        $material = $this->materialModel->selectMaterial($id);
        $this->assertNotEmpty($material); //Existe ese material
    }

    public function testInsertMaterial()
    {
        $pn = 'P5432';
        $nombre = 'Material test';
        $descripcion = 'Descripción test';
        $almacen = 'Madrid';
        $stock = 10;
        $umbral_stock = 2;

        $this->assertTrue($this->materialModel->insertMaterial($pn, $nombre, $descripcion, $almacen, $stock, $umbral_stock));
    }


    public function testDeleteMaterial()
    {
        $id = '16';
        $this->assertTrue($this->materialModel->deleteMaterial($id));
    }
    public function testUpdateMaterial()
    {
        $id = '20';
        $pn = 'P999Test';
        $nombre = 'MATERIAL TEST';
        $descripcion = 'UPDATE MATERIAL TEST';
        $almacen = 'ZARAGOZA';
        $stock = 100;
        $umbral_stock = 20;
        $this->assertTrue($this->materialModel->updateMaterial($id, $pn, $nombre, $descripcion, $almacen, $stock, $umbral_stock));
    }
}
