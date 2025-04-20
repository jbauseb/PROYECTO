<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../modelos/UserModel.php';
require_once __DIR__ . '/../configTest.php'; 

class UserModelTest extends TestCase
{
    protected $db;

    //Creamos la conexión a la base de datos de prueba y llamamos al modelo
    protected function setUp(): void
    {
        $this->db = new PDO('mysql:host=127.0.0.1;dbname=' . DBNAME, DBUSER, DBPWD);
        $this->userModel = new UserModel($this->db);
    }

    //Test para comprobar que hay usuarios
    public function testGetAllUsers()
    {
        $users = $this->userModel->getAllUsers(); //Llamamos al método del modelo
        $this->assertNotEmpty($users);
    }

    //Test negativo para comprobar que no se puede insertar un usuario con datos erróneos
    public function testInsertUser()
    {
        $dni = '88555222X';
        $nombre = null; //Dato erróneo. No puede ser null
        $email = 'user_test@email.com';
        $usuario = 'USER_TEST';
        $password = 1234;
        $telefono = 874563214;
        $rol = 'Técnico';
        $sede = 'Sevilla';
        $fecha_alta = '2025-04-15';

        $resultado = $this->userModel->insertUser($dni, $nombre, $email, $usuario, $password, $telefono, $rol, $sede, $fecha_alta);
        $this->assertNull($resultado);
    }

    //Test de eliminacion de usuario
    public function testDeleteUser()
    {
        $id = 1;
        $resultado = $this->userModel->deleteUser($id);
        $this->assertTrue($resultado);
    }
}
