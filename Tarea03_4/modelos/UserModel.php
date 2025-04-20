<?php

/**
 * Clase UserModel. Gestiona la tabla "empleado" de la base de datos
 */
class UserModel
{
    private $db;

    /**
     * Constructor de la clase
     */
    public function __construct($db = null)
    {
        if ($db) {
            $this->db = $db; //Conexión con testdb_alm_system
        } else {
            $this->db = conectar();
        }
    }

    /**
     * Obtiene todos los usuarios de la tabla "empleado"
     * @return array|false Con todos los datos de los empleados, y false en caso de error.
     */
    public function getAllUsers()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM empleado");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getAllUsers: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea un nuevo empleado. La función comprueba si el DNI o el usuario ya existen. 
     * En caso de error, muestra un mensaje.
     * @param string $dni DNI único del empleado.
     * @param string $nombre Nombre del empleado.
     * @param string $email Email del empleado.
     * @param string $usuario Representa a un usuario de manera única.
     * @param string $password Contraseña asignada al empleado.
     * @param int $telefono Teléfono del empleado.
     * @param string $rol Administrador, Gestor o Técnico.
     * @param string $sede Albacete, León, Madrid, Sevilla o Zaragoza.
     * @param string $fecha_alta Fecha en la que se le da de alta. No tiene por qué ser la actual. Puede ser pasada o futura.
     * @return int El id del empleado insertado.
     */

    public function insertUser($dni, $nombre, $email, $usuario, $password, $telefono, $rol, $sede, $fecha_alta)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO empleado (
        dni, nombre, email, usuario, password, telefono, rol, sede, fecha_alta)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bindParam(1, $dni, PDO::PARAM_STR);
            $stmt->bindParam(2, $nombre, PDO::PARAM_STR);
            $stmt->bindParam(3, $email, PDO::PARAM_STR);
            $stmt->bindParam(4, $usuario, PDO::PARAM_STR);
            $stmt->bindParam(5, $password, PDO::PARAM_STR);
            $stmt->bindParam(6, $telefono, PDO::PARAM_INT);
            $stmt->bindParam(7, $rol, PDO::PARAM_STR);
            $stmt->bindParam(8, $sede, PDO::PARAM_STR);
            $stmt->bindParam(9, $fecha_alta, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            if ($e->getCode() === '23000') { //Código que indica que en el campo "DNI" o el campo "usuario" ya exiten esos datos.
                echo "Error: Ese DNI y/o ese usuario ya existen en la base de datos.";
            };
        }
    }

    /**
     * Buscar usuarios según los filtros aplicados, que son opcionales.
     * @param array $filtros Campos de búsqueda (id, nombre, DNI, rol, sede).
     * @return array|bool Resultados de la consulta o false en caso de error.
     */

    public function searchUser($filtros)
    {
        $condiciones = []; //Almacena las condiciones "where"
        $parametros = []; //Almacena los valores

        try {
            //Se recorre los filtros de búsqueda
            foreach ($filtros as $clave => $valor) {
                $valor = trim($valor); //Eliminamos espacios en blanco

                if ($valor !== "") { //Sólo se agregan filtros si el valor no está vacío.

                    if (in_array($clave, ['nombre'])) {
                        //Búsqueda flexible con LIKE para nombre
                        $condiciones[] = "$clave LIKE :$clave";
                        $parametros[":$clave"] = "%$valor%";
                    } else {
                        //Búsqueda exacta para id, dni, sede y rol
                        $condiciones[] = "$clave = :$clave";
                        $parametros[":$clave"] = $valor;
                    }
                }
            }
            //Construimos la consulta dinámica, combinando com implode.
            //Si no hay filtros, se selecciona a todos los empleados.
            $where = $condiciones ? "WHERE " . implode(" AND ", $condiciones) : "";
            $sql = "SELECT * FROM empleado $where";
            $stmt = $this->db->prepare($sql);

            //Preparación de los parámetros de la consulta
            foreach ($parametros as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error searchUser: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Selecciona a un empleado por el id o por el usuario.
     * @param string $campo Campo por el que buscar al empleado (id o usuario).
     * @param int|string $valor Valor asignado a ese campo (entero en caso de id y string en caso de usuario).
     * @return array|bool Devuelve todos los datos del usuario en un array asociativo o false en caso de error.
     */
    public function selectUserBy($campo, $valor)
    {
        $columnas = ['id', 'usuario'];

        if (!in_array($campo, $columnas)) {
            throw new InvalidArgumentException("Columna no válida.");
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM empleado WHERE $campo = ?");

            //Determinamos el tipo de dato
            $parametro = is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(1, $valor, $parametro);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error selectUser: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza a un empleado.
     * @param int $id Identificador del usuario a actualizar.
     * @param string $dni DNI único del usuario.
     * @param string $email Email del usuario.
     * @param int $telefono Teléfono del usuario.
     * @param string $rol Administrador, Gestor o Técnico.
     * @param string $sede Albacete, León, Madrid, Sevilla o Zaragoza.
     * @param string $fecha_alta Fecha en la que se le da de alta.
     * @param string $passwordhash Contraseña asignada al usuario.
     * @return bool True en caso de éxito y false en caso de error.
     */
    public function updateUser($id, $dni, $nombre, $email, $usuario, $telefono, $rol, $sede, $fecha_alta, $passwordHash)
    {
        try {
            //Si se cambia la contraseña, se añade. Si no, no se incluye en la actualización
            $sql = "UPDATE empleado SET
                                        dni = ?,
                                        nombre = ?,
                                        email = ?,
                                        usuario = ?,
                                        telefono = ?,
                                        rol = ?,
                                        sede = ?,
                                        fecha_alta = ?";

            if ($passwordHash) {
                $sql .= ", password = ?";
            }
            $sql .= " WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $dni, PDO::PARAM_STR);
            $stmt->bindParam(2, $nombre, PDO::PARAM_STR);
            $stmt->bindParam(3, $email, PDO::PARAM_STR);
            $stmt->bindParam(4, $usuario, PDO::PARAM_STR);
            $stmt->bindParam(5, $telefono, PDO::PARAM_STR);
            $stmt->bindParam(6, $rol, PDO::PARAM_STR);
            $stmt->bindParam(7, $sede, PDO::PARAM_STR);
            $stmt->bindParam(8, $fecha_alta, PDO::PARAM_STR);

            if ($passwordHash) {
                $stmt->bindParam(9, $passwordHash, PDO::PARAM_STR);
                $stmt->bindParam(10, $id, PDO::PARAM_STR);
            } else {
                $stmt->bindParam(9, $id, PDO::PARAM_STR);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updateUser: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina a un usuario por su identificador.
     * @param int $id Identificador del usuario.
     * @return bool True en caso de éxito y false en caso de error.
     */
    public function deleteUser($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM empleado WHERE id = ?");
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleteUser: " . $e->getMessage());
            return false;
        }
    }

    /**MÉTODOS PARA INFORMES EN PDF */

    /**
     * Obtiene los datos del usuario con la mayor antiguedad en la tabla "empleado"
     * @return array|bool Devuelve array con los usuarios más antiguos (uno o varios) o false en caso de error.
     */
    public function oldestUser()
    {
        try {
            $sql = "SELECT * FROM empleado WHERE fecha_alta = (SELECT MIN(fecha_alta) FROM empleado)";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); //Pueden existir más de uno con la misma antiguedad
        } catch (PDOException $e) {
            error_log("Error oldestUser: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los datos del usuario con la menor antiguedad en la tabla "empleado"
     * @return array|bool Devuelve array con los usuarios más modernos (uno o varios) o false en caso de error.
     */
    public function newestUser()
    {
        try {
            $sql = "SELECT * FROM empleado WHERE fecha_alta = (SELECT MAX(fecha_alta) FROM empleado)";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error newestUser: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el usuario (o usuarios) que ha realizado el mayor número de solicitudes.
     * @return array|bool Devuelve un array con los datos del usuario o false en caso de error.
     */
    public function userMostRequests()
    {
        try {
            $sql = "SELECT e.id, e.nombre, e.email, e.sede, e.fecha_alta, COUNT(s.id_solicitud) AS num_solicitudes
                    FROM solicitud s
                    JOIN empleado e ON s.id_empleado = e.id
                    GROUP BY e.id, e.nombre, e.email, e.sede, e.fecha_alta
                    HAVING num_solicitudes = (
                            SELECT MAX(total)
                            FROM (
                                SELECT COUNT(*) AS total
                                FROM solicitud
                                GROUP BY id_empleado
                            ) AS subquery)";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error UserMostRequests: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los datos del usuario que ha realizado menos solicitudes.
     * @return array|bool Devuelve array con los datos del usuario o false en caso de error.
     */
    public function userFewerRequests()
    {
        try {
            $sql = "SELECT e.id, e.nombre, e.email, e.sede, e.fecha_alta, COUNT(s.id_solicitud) AS num_solicitudes
                  FROM solicitud s
                  JOIN empleado e ON s.id_empleado = e.id
                  GROUP BY e.id, e.nombre, e.email, e.sede, e.fecha_alta
                  HAVING num_solicitudes = (
                      SELECT MIN(total)
                      FROM (
                          SELECT COUNT(*) AS total
                          FROM solicitud
                          GROUP BY id_empleado
                      ) AS subquery)";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error userFewerRequest: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los datos del empleado con rol de Técnico que no ha realizado ninguna solicitud.
     * @return array|bool Array con los datos del empleado o false en caso de error.
     */
    //Usuario técnico que no ha realizado ninguna solicitud
    public function userWithoutRequests()
    {
        try {
            $sql = "SELECT * FROM empleado 
                WHERE id NOT IN (
                SELECT id_empleado FROM solicitud
                ) AND rol = 'Técnico'";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error userWithoutRequests: " . $e->getMessage());
            return false;
        }
    }
}
