<?php

/**
 * Clase RequestModel
 * Modelo encargado de gestionar la tabla "solicitud" y "solicitud_material" de la base de datos mediante código SQL.
 */
class RequestModel
{
    private $db;

    /**
     * Constructor de la clase
     */
    public function __construct()
    {
        $this->db = conectar(); //Reutilizamos la conexión a la BD
    }

    /**
     * Obtiene todas las solicitudes con información del empleado que la generó.
     * @return array Devuelve un array asociativo si encuentra los datos o un array vacio en caso de error
     */
    public function getAllRequests()
    {
        try {
            $sql = "SELECT solicitud.*, 
                    empleado.nombre AS empleado_nombre
                    FROM solicitud 
                    JOIN empleado 
                    ON empleado.id = solicitud.id_empleado
                    ORDER BY solicitud.id_solicitud";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getAllRequests: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crea una nueva solicitud, usando el id del usuario que inició sesión
     * @return int|boolean Devuelve un entero que representa el identificador de la solicitud. En caso de error, devuelve false.
     */
    public function createRequest()
    {
        try {
            $id_empleado = $_SESSION['user']['id'];

            //Fecha y hora se insertan automáticamente en la base de datos

            $sql = "INSERT INTO solicitud (id_empleado) VALUES (?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_empleado]);
            return $this->db->lastInsertId(); //Retorna el último ID insertado
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina una solicitud de la base de datos según su identificador.
     * @param int $id Identificador de la solicitud
     * @return boolean Devuelve true en caso de éxito y false en caso contrario
     */
    public function deleteRequest($id)
    {
        try {
            $sql = "DELETE FROM solicitud WHERE id_solicitud=?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Añade un material a una solicitud dentro de la tabla "solicitud_material", asignándole también una ruta
     * @param int $id_solicitud Identificador de la solicitud.
     * @param int $id_material Identificador del material.
     * @param int $cantidad_solicitada Entero que representa la cantidad de ese material que el usuario insertó en el formulario.
     * @param int $id_ruta Identificador de la ruta que se generó con esa solicitud y ese material.
     * @return boolean Devuelve true en caso de éxito y false en caso de error.
     */
    public function addMaterialToRequest($id_solicitud, $id_material, $cantidad_solicitada, $id_ruta)
    {
        try {
            $sql = "INSERT INTO solicitud_material VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_solicitud, $id_material, $cantidad_solicitada, $id_ruta]);
            return true;
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza el material de una solicitud. En caso de que el usuario haya ajustado la cantidad de un material
     * a 0, ese material se elimina de la solicitud. 
     * @param int $id_solicitud Identificador de la solicitud a actualizar.
     * @param array $id_materiales Array que identifica a todos los materiales incluidos en esa solicitud.
     * @param array $cantidades Array de enteros que representa la cantidad a actualizar de cada material.
     * @return boolean Devuelve true en caso de éxito y false en caso contrario.
     */
    public function updateMaterialToRequest($id_solicitud, $id_materiales, $cantidades)
    {
        try {
            foreach ($id_materiales as $index => $id_material) {
                $cantidad = $cantidades[$index];

                //Se elimina si se actualiza a 0
                if ($cantidad === 0) {
                    $this->deleteMaterial($id_solicitud, $id_material);
                }
                $sql = "UPDATE solicitud_material 
                        SET cantidad_solicitada = ? 
                        WHERE id_solicitud = ? 
                        AND id_material = ?";

                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(1, $cantidad, PDO::PARAM_INT);
                $stmt->bindParam(2, $id_solicitud, PDO::PARAM_INT);
                $stmt->bindParam(3, $id_material, PDO::PARAM_INT);
                $stmt->execute();
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un material de una solicitud.
     * @param int $id_solicitud Identificador de la solicitud.
     * @param int $id_material Identificador del material a eliminar de la solicitud.
     * @return boolean Devuelve true en caso de éxito y false en caso contrario.
     */
    public function deleteMaterial($id_solicitud, $id_material)
    {
        try {
            $sql = "DELETE FROM solicitud_material WHERE id_solicitud=? AND id_material=?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $id_solicitud, PDO::PARAM_INT);
            $stmt->bindParam(2, $id_material, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Establece el estado de la solicitud basado en sus rutas de transporte (fechas y horas).
     * Para ello se obtienen la(s) ruta(s) asociada(s) a esa solicitud.
     * @param int $id_solicitud Identificador de la solicitud a actualizar.
     * @return boolean Devuelve true en caso de éxito y false en caso de error.
     */
    public function setStatus($id_solicitud)
    {
        try {
            //Obtiene las rutas asociadas a la solicitud
            $sql_ruta = "SELECT r.fecha_salida, r.hora_salida, r.fecha_llegada, r.hora_llegada
                         FROM ruta r
                         INNER JOIN solicitud_material sm ON r.id_ruta = sm.id_ruta
                         WHERE sm.id_solicitud = ?";
            $stmt_ruta = $this->db->prepare($sql_ruta);
            $stmt_ruta->execute([$id_solicitud]);
            $rutas = $stmt_ruta->fetchAll(PDO::FETCH_ASSOC);

            /** Estados y condiciones:
             * "Pendiente": Ninguna ruta de la misma solicitud ha salido.
             * "En tránsito": Al menos una ruta de la misma solicitud ha salido, pero no todas han llegado.
             * "Entregado": Todas las rutas de la misma solicitud han llegado.
             * "Finalizada": El usuario marca manualmente con un botón.
             */
            $estado = "Pendiente"; //Asumimos que está pendiente por defecto
            $rutas_salida = 0;  //Contador de rutas que han salido
            $rutas_llegada = 0; //Contador de rutas que han llegado
            $total_rutas = count($rutas); //Número total de rutas en la solicitud

            foreach ($rutas as $ruta) {
                //Comparamos las fechas y horas

                if ($ruta['fecha_salida']) { //Si tiene asignada fecha de salida
                    $ha_salido = cdate($ruta['fecha_salida'], $ruta['hora_salida']);
                    $ha_llegado = cdate($ruta['fecha_llegada'], $ruta['hora_llegada']);
                    if ($ha_salido) {
                        $rutas_salida++; //Contamos cuántas rutas han salido
                    }
                    if ($ha_llegado) {
                        $rutas_llegada++; //Contamos cuántas rutas han llegado
                    }
                }
            }

            //Si no está ya finalizada, determina el estado final de la solicitud
            $solicitud = $this->getRequestDetails($id_solicitud);

            if ($solicitud[0]['estado'] != "Finalizada") {
                if ($rutas_llegada === $total_rutas && $total_rutas > 0) {
                    $estado = "Entregado"; //Todas las rutas han llegado
                } elseif ($rutas_salida > 0) {
                    $estado = "En tránsito"; //Al menos una ha salido, pero no todas han llegado
                }
                //Actualiza el estado de la solicitud
                $sql_update = "UPDATE solicitud SET estado = ? WHERE id_solicitud = ?";
                $stmt_update = $this->db->prepare($sql_update);
                $stmt_update->execute([$estado, $id_solicitud]);
                return true;
            }
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los detalles de una solicitud, incluyendo materiales, el empleado que la generó y resto de datos asociados.
     * @param int $id_solicitud Identificador de la solicitud de la que se obtienen los detalles.
     * @return array Lista de detalles de la solicitud con los materiales asociados, o array vacío en caso de error.
     */
    public function getRequestDetails($id_solicitud)
    {
        try {
            $sql = "SELECT s.fecha_inicio,
                s.hora_inicio,
                s.estado,
                s.fecha_fin,
                s.hora_fin,
                sm.id_material,
                sm.cantidad_solicitada AS cantidad,
                sm.id_ruta,
                e.nombre AS empleado_nombre,
                e.rol,
                e.email AS email,
                e.sede AS sede,
                m.partnumber AS material_pn,
                m.nombre AS material_nombre,
                m.almacen AS material_almacen
            FROM solicitud s
            JOIN empleado e ON s.id_empleado = e.id
            JOIN solicitud_material sm ON s.id_solicitud=sm.id_solicitud
            JOIN material m ON sm.id_material = m.id_material
            WHERE s.id_solicitud = ?";


            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_solicitud]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);  //Devuelve los resultados de la consulta como un array asociativo
        } catch (Exception $e) {
            error_log("Error al obtener detalles de la solicitud: " . $e->getMessage());
            return [];  //Devuelve un array vacío si ocurre un error
        }
    }

    /**
     * Finaliza una solicitud, es decir, el campo "estado" lo actualiza al valor "finalizada".
     * Se insertan la fecha y horas actuales.
     * @param int $id Identificador de la solicitud.
     * @return boolean Devuelve true en caso de éxito y false en caso contrario.
     */
    public function finalizeRequest($id)
    {
        $fecha_fin = date('Y-m-d');
        $hora_fin = date('H:i:s');

        try {
            $sql = "UPDATE solicitud SET estado = ?, fecha_fin = ?, hora_fin = ? WHERE id_solicitud = ?";
            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(1, "Finalizada", PDO::PARAM_STR);
            $stmt->bindValue(2, $fecha_fin, PDO::PARAM_STR);
            $stmt->bindValue(3, $hora_fin, PDO::PARAM_STR);
            $stmt->bindValue(4, $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en finalizeRequest: " . $e->getMessage()); // Guarda el error en el log del servidor
            return false; //Devuelve false en caso de error
        }
    }

    /**MÉTODOS PARA GENERAR LOS INFORMES EN PDF */

    /**
     * Obtiene la solicitud que se resolvió en un mayor número de días.
     * @return boolean Devuelve true en caso de éxito y false en caso contrario.
     */
    public function requestMaxDias()
    {
        try {
            $sql = "WITH max_dias AS (
                            SELECT MAX(DATEDIFF(fecha_fin, fecha_inicio)) AS max_diferencia
                            FROM solicitud
                            WHERE fecha_fin IS NOT NULL)
                    SELECT 
                            id_solicitud,
                            id_empleado,
                            fecha_inicio,
                            fecha_fin,
                            DATEDIFF(fecha_fin, fecha_inicio) AS dias_diferencia
                    FROM solicitud, max_dias
                    WHERE fecha_fin IS NOT NULL
                    AND DATEDIFF(fecha_fin, fecha_inicio) = max_dias.max_diferencia";

            return $stmt = $this->db->query($sql);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage()); // Guarda el error en el log del servidor
            return false; 
        }
    }

   /**
     * Obtiene la solicitud que se resolvió en un menor número de días.
     * @return boolean Devuelve true en caso de éxito y false en caso contrario.
     */
    public function requestMinDias()
    {
        try {
            $sql = "WITH min_dias AS (
                            SELECT MIN(DATEDIFF(fecha_fin, fecha_inicio)) AS min_diferencia
                            FROM solicitud
                            WHERE fecha_fin IS NOT NULL)
                    SELECT 
                            id_solicitud,
                            id_empleado,
                            fecha_inicio,
                            fecha_fin,
                            DATEDIFF(fecha_fin, fecha_inicio) AS dias_diferencia
                    FROM solicitud, min_dias
                    WHERE fecha_fin IS NOT NULL
                    AND DATEDIFF(fecha_fin, fecha_inicio) = min_dias.min_diferencia";

            return $stmt = $this->db->query($sql);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage()); // Guarda el error en el log del servidor
            return false; //Retorna false en caso de error
        }
    }
}
