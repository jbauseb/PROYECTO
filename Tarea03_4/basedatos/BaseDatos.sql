-- ELimina la base de datos si existe previamente
DROP DATABASE IF EXISTS alm_system;

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS alm_system;

USE alm_system;

-- Crear tablas
CREATE TABLE empleado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dni VARCHAR(9) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(50) NOT NULL,
    usuario VARCHAR(15) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(15),
    rol ENUM('Administrador', 'Gestor', 'Técnico') NOT NULL,
    sede ENUM(
        'Madrid',
        'Zaragoza',
        'León',
        'Albacete',
        'Sevilla'
    ) NOT NULL,
    fecha_alta DATE NOT NULL
);

CREATE TABLE material (
    id_material INT AUTO_INCREMENT PRIMARY KEY,
    partnumber VARCHAR(20) NOT NULL,
    nombre VARCHAR(25) NOT NULL,
    descripcion TEXT,
    almacen ENUM(
        'Madrid',
        'Zaragoza',
        'León',
        'Albacete',
        'Sevilla'
    ) NOT NULL,
    stock INT NOT NULL,
    umbral_stock INT NOT NULL
);

CREATE TABLE ruta (
    id_ruta INT AUTO_INCREMENT PRIMARY KEY,
    origen ENUM(
        'Madrid',
        'Zaragoza',
        'León',
        'Albacete',
        'Sevilla'
    ) NOT NULL,
    destino ENUM(
        'Madrid',
        'Zaragoza',
        'León',
        'Albacete',
        'Sevilla'
    ) NOT NULL,
    fecha_salida DATE,
    hora_salida TIME,
    fecha_llegada DATE,
    hora_llegada TIME,
    finalizada BOOLEAN DEFAULT false
);

CREATE TABLE solicitud(
    id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    fecha_inicio DATE NOT NULL DEFAULT CURRENT_DATE,
    hora_inicio TIME NOT NULL DEFAULT CURRENT_TIME,
    id_empleado INT NOT NULL,
    estado ENUM('Pendiente', 'En tránsito', 'Entregado', 'Finalizada') NOT NULL DEFAULT 'Pendiente',
    fecha_fin DATE,
    hora_fin TIME,
    FOREIGN KEY (id_empleado) REFERENCES empleado(id) ON DELETE CASCADE
);

CREATE TABLE solicitud_material (
    id_solicitud INT,
    id_material INT,
    cantidad_solicitada INT,
    id_ruta INT,
    PRIMARY KEY (id_solicitud, id_material),
    FOREIGN KEY (id_solicitud) REFERENCES solicitud (id_solicitud) ON DELETE CASCADE,
    FOREIGN KEY (id_material) REFERENCES material(id_material) ON DELETE CASCADE
);

-- Trigger para que se eliminen de la tabla "solicitud" las solicitudes que ya no disponen de material en "solicitud_material"
-- Se usa DELIMITER para agrupar varias instrucciones entre BEGIN y END
DELIMITER //

CREATE TRIGGER after_delete_solicitud_material
AFTER DELETE ON solicitud_material
FOR EACH ROW
BEGIN
    DECLARE count_materials INT;
    
    -- Contar cuántos materiales quedan en la solicitud
    SELECT COUNT(*) INTO count_materials 
    FROM solicitud_material 
    WHERE id_solicitud = OLD.id_solicitud;
    
    -- Si no quedan materiales en la solicitud, se elimina
    IF count_materials = 0 THEN
        DELETE FROM solicitud WHERE id_solicitud = OLD.id_solicitud;
    END IF;
END;
//

DELIMITER ;

-- INSERTAR DATOS
-- Insertar datos iniciales (password=1234)
INSERT INTO
    empleado (
        dni,
        nombre,
        email,
        usuario,
        password,
        telefono,
        rol,
        sede,
        fecha_alta
    )
VALUES
    (
        '12345678A',
        'Rodríguez López, Ana',
        'ana.lopez@email.com',
        'analopez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '654321987',
        'Administrador',
        'Madrid',
        '2025-01-01'
    ),
    (
        '23456789B',
        'Gallardo Pérez, Juan',
        'juan.perez@email.com',
        'juanperez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '678912345',
        'Administrador',
        'Zaragoza',
        '2024-12-15'
    ),
    (
        '34567890C',
        'Gómez Cabañas, María',
        'maria.gomez@email.com',
        'mariagomez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '684257913',
        'Administrador',
        'León',
        '2024-12-20'
    ),
    (
        '45678901D',
        'Martínez Sánchez, Laura',
        'laura.martinez@email.com',
        'lauramartinez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '691234567',
        'Administrador',
        'Sevilla',
        '2025-02-10'
    ),
    (
        '56789012E',
        'Torres Ruiz, Raúl',
        'raul.torres@email.com',
        'raultorres',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '622345678',
        'Administrador',
        'Albacete',
        '2025-01-25'
    ),
    (
        '67890123F',
        'López García, Carlos',
        'carlos.lopez@email.com',
        'carloslopez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '654987321',
        'Gestor',
        'Madrid',
        '2024-11-01'
    ),
    (
        '78901234G',
        'Ramos Fernández, Elisa',
        'elisa.ramos@email.com',
        'elisaramos',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '678123456',
        'Gestor',
        'Zaragoza',
        '2024-10-15'
    ),
    (
        '89012345H',
        'Vázquez Martín, Roberto',
        'roberto.vazquez@email.com',
        'roberto.vazquez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '691234987',
        'Gestor',
        'León',
        '2024-11-05'
    ),
    (
        '90123456I',
        'Hernández Pérez, Isabel',
        'isabel.hernandez@email.com',
        'isabelhernandez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '690987654',
        'Gestor',
        'Sevilla',
        '2024-12-01'
    ),
    (
        '12398765J',
        'García Sánchez, José',
        'jose.garcia@email.com',
        'josegarcia',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '622345789',
        'Gestor',
        'Albacete',
        '2024-09-25'
    ),
    (
        '23456789K',
        'Pérez Rodríguez, Javier',
        'javier.perez@email.com',
        'javierperez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '654123987',
        'Técnico',
        'Madrid',
        '2024-08-20'
    ),
    (
        '34567890L',
        'García López, Ana',
        'ana.garcia@email.com',
        'anagarcia',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '674321987',
        'Técnico',
        'Madrid',
        '2024-07-15'
    ),
    (
        '45678901M',
        'Martínez Gómez, Pablo',
        'pablo.martinez@email.com',
        'pablomartinez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '695432101',
        'Técnico',
        'Zaragoza',
        '2024-06-10'
    ),
    (
        '56789012N',
        'Fernández Sánchez, Lucia',
        'lucia.fernandez@email.com',
        'luciafernandez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '617432109',
        'Técnico',
        'Zaragoza',
        '2024-05-18'
    ),
    (
        '67890123O',
        'Ramírez Martín, Mario',
        'mario.ramirez@email.com',
        'marioramirez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '634567892',
        'Técnico',
        'León',
        '2024-04-22'
    ),
    (
        '78901234P',
        'Blanco García, Beatriz',
        'beatriz.blanco@email.com',
        'beatrizblanco',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '612345678',
        'Técnico',
        'León',
        '2024-03-12'
    ),
    (
        '89012345Q',
        'Martín Ruiz, Sergio',
        'sergio.martin@email.com',
        'sergiomartin',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '687654321',
        'Técnico',
        'Sevilla',
        '2024-02-15'
    ),
    (
        '90123456R',
        'López Díaz, Clara',
        'clara.lopez@email.com',
        'claranlopez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '693212345',
        'Técnico',
        'Sevilla',
        '2024-01-30'
    ),
    (
        '12398765S',
        'González Pérez, Iván',
        'ivan.gonzalez@email.com',
        'ivangonzalez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '654321234',
        'Técnico',
        'Albacete',
        '2024-12-05'
    ),
    (
        '23456789T',
        'Sánchez García, Marta',
        'marta.sanchez@email.com',
        'martasanchez',
        '$2y$10$rR0uovIm6fVNNuQKqD.2r.YILx4lrUfzL68KbXsxs9wnBORMcMiOq',
        '678910123',
        'Técnico',
        'Albacete',
        '2024-11-25'
    );


INSERT INTO material (partnumber, nombre, descripcion, almacen, stock, umbral_stock) VALUES
('P1001', 'Material 1', 'Descripción del material 1', 'Madrid', 50, 10),
('P1001', 'Material 1', 'Descripción del material 1', 'León', 70, 20),
('P1001', 'Material 1', 'Descripción del material 1', 'Albacete', 90, 25),
('P1001', 'Material 1', 'Descripción del material 1', 'Sevilla', 25, 30),
('P1002', 'Material 2', 'Descripción del material 2', 'Zaragoza', 10, 10),
('P1002', 'Material 2', 'Descripción del material 2', 'León', 80, 20),
('P1002', 'Material 2', 'Descripción del material 2', 'Albacete', 50, 12),
('P1002', 'Material 2', 'Descripción del material 2', 'Sevilla', 100, 35),
('P1003', 'Material 3', 'Descripción del material 3', 'Madrid', 65, 18),
('P1003', 'Material 3', 'Descripción del material 3', 'Zaragoza', 45, 12),
('P1003', 'Material 3', 'Descripción del material 3', 'Albacete', 95, 28),
('P1003', 'Material 3', 'Descripción del material 3', 'Sevilla', 110, 40),
('P1004', 'Material 4', 'Descripción del material 4', 'Madrid', 55, 16),
('P1004', 'Material 4', 'Descripción del material 4', 'Zaragoza', 35, 8),
('P1004', 'Material 4', 'Descripción del material 4', 'León', 85, 25),
('P1004', 'Material 4', 'Descripción del material 4', 'Sevilla', 130, 50),
('P1005', 'Material 5', 'Descripción del material 5', 'Madrid', 50, 14),
('P1005', 'Material 5', 'Descripción del material 5', 'Zaragoza', 60, 20),
('P1005', 'Material 5', 'Descripción del material 5', 'León', 95, 35),
('P1005', 'Material 5', 'Descripción del material 5', 'Albacete', 105, 38),
('P1006', 'Material 6', 'Descripción del material 6', 'Madrid', 80, 22),
('P1006', 'Material 6', 'Descripción del material 6', 'Albacete', 0, 38),
('P1006', 'Material 6', 'Descripción del material 6', 'Sevilla', 110, 45),
('P1007', 'Material 7', 'Descripción del material 7', 'Madrid', 65, 20),
('P1007', 'Material 7', 'Descripción del material 7', 'Zaragoza', 45, 13),
('P1007', 'Material 7', 'Descripción del material 7', 'Albacete', 85, 30),
('P1008', 'Material 8', 'Descripción del material 8', 'Zaragoza', 50, 18),
('P1008', 'Material 8', 'Descripción del material 8', 'León', 60, 20),
('P1008', 'Material 8', 'Descripción del material 8', 'Albacete', 120, 40),
('P1009', 'Material 9', 'Descripción del material 9', 'Madrid', 55, 16),
('P1009', 'Material 9', 'Descripción del material 9', 'Zaragoza', 70, 25),
('P1009', 'Material 9', 'Descripción del material 9', 'Sevilla', 130, 50),
('P1010', 'Material 10', 'Descripción del material 10', 'Madrid', 50, 15),
('P1010', 'Material 10', 'Descripción del material 10', 'Zaragoza', 60, 18),
('P1010', 'Material 10', 'Descripción del material 10', 'León', 90, 30),
('P1011', 'Material 11', 'Descripción del material 11', 'Madrid', 65, 20),
('P1011', 'Material 11', 'Descripción del material 11', 'Zaragoza', 45, 12),
('P1011', 'Material 11', 'Descripción del material 11', 'Albacete', 95, 35),
('P1011', 'Material 11', 'Descripción del material 11', 'Sevilla', 135, 50),
('P1012', 'Material 12', 'Descripción del material 12', 'Madrid', 60, 18),
('P1012', 'Material 12', 'Descripción del material 12', 'León', 85, 30),
('P1012', 'Material 12', 'Descripción del material 12', 'Sevilla', 120, 45),
('P1013', 'Material 13', 'Descripción del material 13', 'Madrid', 75, 25),
('P1013', 'Material 13', 'Descripción del material 13', 'León', 90, 35),
('P1013', 'Material 13', 'Descripción del material 13', 'Sevilla', 130, 50),
('P1014', 'Material 14', 'Descripción del material 14', 'Madrid', 0, 20),
('P1014', 'Material 14', 'Descripción del material 14', 'León', 70, 25),
('P1014', 'Material 14', 'Descripción del material 14', 'Albacete', 95, 30),
('P1014', 'Material 14', 'Descripción del material 14', 'Sevilla', 110, 40);



-- Crear usuarios y asignar roles
CREATE USER IF NOT EXISTS 'administrador' @'localhost' IDENTIFIED BY '123';
GRANT ALL PRIVILEGES ON *.* TO 'administrador' @'localhost';

CREATE USER IF NOT EXISTS 'gestor' @'localhost' IDENTIFIED BY '123';
GRANT SELECT,INSERT,UPDATE ON alm_system.ruta TO 'gestor' @'localhost';

GRANT SELECT,INSERT,UPDATE ON alm_system.material TO 'gestor' @'localhost';

CREATE USER IF NOT EXISTS 'tecnico' @'localhost' IDENTIFIED BY '123';

GRANT SELECT,INSERT ON alm_system.solicitud TO 'tecnico' @'localhost';

FLUSH PRIVILEGES;