<?php
function conectar() {
    try {
        //Crea base de datos en memoria
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //Crea tabla 'material'
        $pdo->exec("
            CREATE TABLE material (
                id_material INTEGER PRIMARY KEY AUTOINCREMENT,
                partnumber TEXT NOT NULL,
                nombre TEXT NOT NULL,
                descripcion TEXT,
                almacen TEXT NOT NULL,
                stock INTEGER DEFAULT 0,
                umbral_stock INTEGER DEFAULT 0
            );
        ");

        //Crea tabla 'solicitud_material'
        $pdo->exec("
            CREATE TABLE solicitud_material (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                id_material INTEGER NOT NULL,
                id_ruta INTEGER NOT NULL,
                cantidad_solicitada INTEGER DEFAULT 0,
                FOREIGN KEY (id_material) REFERENCES material(id_material)
            );
        ");

        //Inserta datos de prueba en 'material'
        $pdo->exec("
            INSERT INTO material (partnumber, nombre, descripcion, almacen, stock, umbral_stock) VALUES
            ('PN-001', 'Tornillo M4', 'Tornillo de acero inoxidable', 'Almacen A', 100, 20),
            ('PN-002', 'Tuerca M4', 'Tuerca de acero', 'Almacen A', 200, 30),
            ('PN-003', 'Arandela M4', 'Arandela plana', 'Almacen B', 150, 25)
        ");

        //Inserta datos de prueba en 'solicitud_material'
        $pdo->exec("
            INSERT INTO solicitud_material (id_material, id_ruta, cantidad_solicitada) VALUES
            (1, 101, 10),
            (2, 101, 5),
            (3, 102, 15)
        ");

        return $pdo;
    } catch (PDOException $e) {
        echo "Errorrrrrr al conectar o crear la base de datos: " . $e->getMessage();
        return null;
    }
}
?>
