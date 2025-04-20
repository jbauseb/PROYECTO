<?php

/**
 * Clase RouteModel
 * Modelo encargado de obtener datos y gestionar la tabla "rutas" de la base de datos.
 */
class RouteModel
{
    private $db;

    /**
     * Constructor de clase.
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
     * Obtiene todas las rutas de la base de datos.
     * @return array Devulve un array con la lista de rutas o un array vacío en caso de error.
     */
    public function getAllRoutes()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM ruta");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getAllRoutes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Seleccionar una ruta por ID
     * @param int $id Identificador de la ruta.
     * @return array Datos de la ruta o vacío si hay error.
     */
    public function selectRoute($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM ruta WHERE id_ruta = ?");
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en selectRoute: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Eliminar una ruta por su identificador.
     * @param int $id Identificador de la ruta a eliminar.
     * @return bool true si se eliminó con éxito, false en caso contrario
     */
    public function deleteRoute($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM ruta WHERE id_ruta = ?");
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Error en deleteRoute: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina una ruta si la fecha de llegada fue hace más de 365 días.
     * @param string $fecha Fecha de la ruta
     * @param int $id_ruta ID de la ruta
     * @return bool true si se eliminó, false si no
     */
    public function deleteOldRoute($fecha, $id_ruta)
    {
        try {
            if ($fecha) {
                $fechaRuta = new DateTime($fecha);
                $fechaLimite = (new DateTime())->sub(new DateInterval('P365D')); //Resta 365 días

                if ($fechaRuta < $fechaLimite) { //Si la fecha es anterior a 365 días, la elimina
                    return $this->deleteRoute($id_ruta) ? true : false; //Devuelve un valor booleano
                }
            }
        } catch (PDOException $e) {
            error_log("Error en deleteOldRoute: " . $e->getMessage());
            return false;
        }

        return false; //Valor predeterminado
    }

    /**
     * Insertar una nueva ruta.
     * @param string $origen Origen de la ruta.
     * @param string $destino Sede del peticionario y destino de la ruta.
     * @return int|null Identificador de la nueva ruta o null si falla.
     */
    public function insertRoute($origen, $destino)
    {
        try {
            $sql = "INSERT INTO ruta (origen, destino) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$origen, $destino]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error insertRoute: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar una ruta.
     * @param int $id_ruta Id de la ruta.
     * @param string $origen Origen de la ruta.
     * @param string $destino Destino de la ruta.
     * @param string $fecha_salida Fecha en la que parte la ruta del origen.
     * @param string $hora_salida Hora de partida de la ruta.
     * @param string $fecha_llegada Fecha prevista de llegada de la ruta al destino.
     * @param string $hora_llegada Fecha prevista de llegada de la ruta al destino.
     * @return boolean True si se actualiza con éxito, o false si no se puede actualizar.
     */
    public function updateRoute($id_ruta, $origen, $destino, $fecha_salida, $hora_salida, $fecha_llegada, $hora_llegada)
    {
        try {
            $sql = "UPDATE ruta SET
                origen = ?,
                destino = ?,
                fecha_salida = ?,
                hora_salida = ?,
                fecha_llegada = ?,
                hora_llegada = ?
            WHERE id_ruta = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $origen, PDO::PARAM_STR);
            $stmt->bindParam(2, $destino, PDO::PARAM_STR);
            $stmt->bindParam(3, $fecha_salida, PDO::PARAM_STR);
            $stmt->bindParam(4, $hora_salida, PDO::PARAM_STR);
            $stmt->bindParam(5, $fecha_llegada, PDO::PARAM_STR);
            $stmt->bindParam(6, $hora_llegada, PDO::PARAM_STR);
            $stmt->bindParam(7, $id_ruta, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Error updateRoute: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Finaliza una ruta, es decir, actualiza la bandera "finalizada" de 0 a 1.
     * @param int $id_ruta Identificador de la ruta.
     * @return bool Devuelve true 
     */
    public function finalizeRoute($id_ruta)
    {
        try {
            $sql = "UPDATE ruta SET finalizada = 1 
                    WHERE id_ruta = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $id_ruta, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error finalizeRoute: " . $e->getMessage());
            return false;
        }
    }

    /**MÉTODOS PARA GENERAR LOS INFORMES EN PDF */

    /**
     * Obtiene todas las rutas que ya están finalizadas (es decir, hay llegado al destino).
     * @return array|bool Devuelve un array con todas las rutas finalizadas y false en caso de error.
     */
    function getFinalizedRoutes()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM ruta WHERE finalizada=1");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getFinalizedRoutes: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el origen más repetido entre todas las rutas, y el número de veces.
     * Puede haber más de un origen con el mismo número de veces.
     * @return array|bool Devuelve un array en caso de existir y false en caso de error.
     */
    function origenMasRepetido() //Puede haber más de uno
    {
        try {
            $sql = "WITH Origenes AS (
                    SELECT origen, COUNT(*) AS total
                    FROM ruta WHERE finalizada=1
                    GROUP BY origen)
                        SELECT origen, total
                        FROM Origenes
                        WHERE total = (SELECT MAX(total) FROM Origenes);
                        ";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error origenMasRepetido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el destino más repetido entre todas las rutas, y el número de veces.
     * Puede haber más de un destino con el mismo número de veces.
     * @return array|bool Devuelve un array en caso de existir y false en caso de error.
     */
    function destinoMasRepetido()
    {
        try {
            $sql = "WITH Destinos AS (
                    SELECT destino, COUNT(*) AS total
                    FROM ruta WHERE finalizada=1
                    GROUP BY destino)
                        SELECT destino, total
                        FROM Destinos
                        WHERE total = (SELECT MAX(total) FROM Destinos);
                        ";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error destinoMasRepetido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene la duración en horas de todas las rutas y las ordena por duración.
     * @param string $orden En caso de DESC los ordena de manera descencendente, y ASC para ascendente.
     * @return array|bool Con todas las rutas y su duración, y de manera ascendente o descendente en función del parámetro. False en caso de error.
     */
    function rutaTiempo($orden) //DES o ASC para obtener la de mayor tiempo y la de menor tiempo
    {
        try {
            $sql = "SELECT id_ruta, origen, destino, fecha_salida, hora_salida, fecha_llegada, hora_llegada,
                    TIMESTAMPDIFF(HOUR, CONCAT(fecha_salida, ' ', hora_salida), CONCAT(fecha_llegada, ' ', hora_llegada)) AS duracion_horas
                    FROM ruta WHERE finalizada=1
                    ORDER BY duracion_horas " . $orden;

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error rutaTiempo: " . $e->getMessage());
            return false;
        }
    }
}
