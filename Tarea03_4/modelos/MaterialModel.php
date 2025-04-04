<?php

/**
 * Clase MaterialModel
 * Modelo encargado de gestionar la tabla "material" de la base de datos mediante código SQL.
 */
class MaterialModel
{
    private $db;

    /**
     * Constructor de la clase
     */
    public function __construct()
    {
        $this->db = conectar();
    }

    /**
     * Obtiene todos los materiales de la base de datos mediante una consulta SQL
     * y los devuelve como un array asociativo
     * @return array con los materiales encontrados, o vacío en caso de error
     */
    public function getAllMaterials()
    {
        try {
            $sql = "SELECT * FROM material";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); //Devuelve un array asociativo con los materiales
        } catch (PDOException $e) {
            error_log("Error al obtener materiales: " . $e->getMessage());
            return []; //Devuelve un array vacío en caso de error
        }
    }

    /**
     * Buscar materiales con filtros opcionales.
     * Esta función permite realizar una búsqueda en la tabla `material` utilizando filtros opcionales. Los filtros
     * pueden ser por `id_material`, `partnumber`, `nombre` y `almacen`. Los filtros se aplican de manera dinámica,
     * permitiendo una búsqueda flexible (con `LIKE`) en los campos `nombre` y `partnumber`, y una búsqueda exacta
     * en los campos `id_material` y `almacen`.
     * @param array $filtros Filtros de búsqueda, donde las claves pueden ser:
     *                       - `id_material` (int): ID del material.
     *                       - `partnumber` (string): Número de parte del material.
     *                       - `nombre` (string): Nombre del material.
     *                       - `almacen` (string): Almacén asociado al material.
     * @return array Resultado de la consulta, devuelven un array asociativo con los materiales que coinciden
     *               con los filtros proporcionados.
     *               En caso de no encontrar materiales o si no se aplican filtros, retorna un array vacío.
     */
    public function searchMaterial($filtros)
    {
        $condiciones = []; //Vamos añadiendo los diferentes filtros
        $parametros = []; //Vamos añadiendo los diferentes valores

        foreach ($filtros as $clave => $valor) {
            $valor = trim($valor); //Eliminamos espacios en blanco

            if ($valor !== "") { //Permite valores como "0"
                if (in_array($clave, ['nombre', 'partnumber'])) {
                    //Búsqueda flexible con LIKE para nombres y P/N
                    $condiciones[] = "$clave LIKE :$clave";
                    $parametros[":$clave"] = "%$valor%";
                } else {
                    //Búsqueda exacta para id_material y almacén
                    $condiciones[] = "$clave = :$clave";
                    $parametros[":$clave"] = $valor;
                }
            }
        }
        //Construimos la consulta de manera dinámica
        $where = $condiciones ? "WHERE " . implode(" AND ", $condiciones) : ""; //Vamos uniendo los elementos con AND
        $sql = "SELECT * FROM material $where";
        $stmt = $this->db->prepare($sql);
        foreach ($parametros as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Selecciona todos los datos de un material concreto en función de su identificador
     * y los devuelve como un array asociativo.
     * @param $id identificador del material a seleccionar.
     * @return array Resultado de la consulta, con todos los datos del material.
     */
    public function selectMaterial($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM material WHERE id_material=?");
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina un material concreto de la tabla "material" según su identificador.
     * @param $id identificador del material. 
     * @return bool Devuelve un booleano en función del éxito o no de la eliminación.
     */
    public function deleteMaterial($id)
    {
        $stmt = $this->db->prepare("DELETE FROM material WHERE id_material = ?");
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        //Ejecuta la sentencia
        $stmt->execute();
        //Verificar si se eliminó alguna fila
        if ($stmt->rowCount() > 0) {
            return true; //Material eliminado con éxito
        }
        return false; //No se eliminó el material
    }

    /**
     * Actualiza los datos de un material concreto.
     * @param $id Identificador del material.
     * @param $pn Part Number del material.
     * @param $nombre Nombre del material.
     * @param $descripcion Descripción del material.
     * @param $almacen Almacén donde se encuentra el material.
     * @param $stock Cantidad de material en ese almacén.
     * @param $umbral_stock Cantidad que se establece como límite para mostrar aviso al usuario.
     * @return bool Devuelve booleano en función del éxito o no de la actualización.
     */
    public function updateMaterial($id, $pn, $nombre, $descripcion, $almacen, $stock, $umbral_stock)
    {
        $stmt = $this->db->prepare("UPDATE material SET
            partnumber = ?,
            nombre = ?,
            descripcion = ?,
            almacen = ?,
            stock = ?,
            umbral_stock = ?
        WHERE id_material = ?");

        $stmt->bindValue(1, $pn, PDO::PARAM_STR);
        $stmt->bindValue(2, $nombre, PDO::PARAM_STR);
        $stmt->bindValue(3, $descripcion, PDO::PARAM_STR);
        $stmt->bindValue(4, $almacen, PDO::PARAM_STR);
        $stmt->bindValue(5, $stock, PDO::PARAM_INT);
        $stmt->bindValue(6, $umbral_stock, PDO::PARAM_INT);
        $stmt->bindValue(7, $id, PDO::PARAM_INT);

        //Ejecutar la consulta
        if ($stmt->execute()) {
            return true; //Actualización exitosa
        }
        return false; //Error al actualizar
    }

    /**
     * Obtiene la cantidad de material usando su identificador y lo devuelve como un entero.
     * @param $id_material Identificador del material.
     * @return int Devuelve entero con la cantidad de ese material o nulo si hay error o no existe ese material
     */
    public function getStock($id_material)
    {
        $sql = "SELECT stock FROM material WHERE id_material= ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $id_material, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultado && isset($resultado['stock'])) {
                return intval($resultado['stock']); //Devuelve el stock como entero
            }
        }
        //En caso de error o si no se encuentra el material, devuelve null
        return null;
    }

    /**
     * Establece un nuevo stock de un material concreto
     * y devuelve un valor booleano.
     * @param $id_material Identificador del material.
     * @param $nuevo_stock Cantidad que se establecerá como stock de ese material.
     * @return bool Devuelve true en caso de éxito y false en caso de error.
     */
    public function setStock($id_material, $nuevo_stock)
    {
        $sql = "UPDATE material SET stock = ? WHERE id_material = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $nuevo_stock, PDO::PARAM_INT);
        $stmt->bindParam(2, $id_material, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Obtiene los datos del material según su Part Number y el almacén en el que se encuentra,
     * y devuelve un valor booleano.
     * @param $pn Part Number del material a seleccionar.
     * @param $almacen Almacén en el que se encontraría el material.
     * @return bool Devuelve true en caso de éxito y false en caso de error.
     */
    public function getMaterialByPartnumberAndAlmacen($pn, $almacen)
    {
        $sql = "SELECT * FROM material WHERE partnumber = ? AND almacen = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $pn, PDO::PARAM_STR);
        $stmt->bindParam(2, $almacen, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Inserta un nuevo material en la tabla "material"
     * y devuelve un valor booleano.
     * @param $pn Part Number del material.
     * @param $nombre Nombre del material.
     * @param $descripcion Descripción del material.
     * @param $stock Cantidad de material.
     * @param $umbral_stock Cantidad que se establecerá como umbral de stock de ese material para avisar al usuario.
     * @return bool Devuelve true en caso de éxito y false en caso de error.
     */
    public function insertMaterial($pn, $nombre, $descripcion, $almacen, $stock, $umbral_stock)
    {
        $sql = "INSERT INTO material (partnumber, nombre, descripcion, almacen, stock, umbral_stock) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $pn, PDO::PARAM_STR);
        $stmt->bindParam(2, $nombre, PDO::PARAM_STR);
        $stmt->bindParam(3, $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(4, $almacen, PDO::PARAM_STR);
        $stmt->bindParam(5, $stock, PDO::PARAM_INT);
        $stmt->bindParam(6, $umbral_stock, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Obtiene los materiales transportados por una ruta logística mediante consulta SQL a las tablas "material" y "solicitud_material"
     * y devuelve un array asociativo
     * @param $id_ruta Identificador de la ruta.
     * @return array Devuelve un array asociativo con los datos de los materiales.
     */
    public function getMaterialsByRoute($id_ruta)
    {
        $sql = "SELECT
                m.id_material,
                m.partnumber AS partnumber,
                m.nombre AS material_nombre,
                m.descripcion AS material_descripcion,
                m.umbral_stock AS umbral_stock,
                sm.cantidad_solicitada AS cantidad
                FROM solicitud_material sm
                JOIN material m 
                ON m.id_material = sm.id_material
                WHERE sm.id_ruta = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $id_ruta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los datos de los materiales (Part Number, Nombre, Descripción) así como la suma total de los 
     * materiales en los que esos datos son iguales, y los agrupa por Part Number y devuelve un array asociativo.
     * @return array Devuelve un array asociativo con los datos de los materiales o array vacio en caso de error.
     */
    public function getPartnumberWithStock()
    {
        try {
            $sql = "SELECT partnumber,nombre,descripcion, SUM(stock) AS total_stock FROM material GROUP BY partnumber";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener partnumbers con stock: " . $e->getMessage());
            return [];
        }
    }

    /**FUNCIONES PARA INFORMES EN PDF */
    /**
     * Obtiene el material cuyo stock es el valor más alto en la tabla "material" (puede haber varios con el mismo stock)
     * y devuelve un array asociativo.
     * @return array Devuelve un array asociativo con los datos del material.
     */
    public function materialMasStock()
    {
        $sql = "SELECT * FROM material WHERE stock = (SELECT MAX(stock) FROM material)";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Similar al método anterior, pero en este caso el que tiene el valor mínimo de stock
     * y devuelve un array asociativo.
     * @return array Devuelve un array asociativo con los datos del material.
     */

    public function materialMenosStock()
    {
        $sql = "SELECT * FROM material WHERE stock = (SELECT MIN(stock) FROM material)";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el material más solicitado mediante la suma del campo "cantidad_solicitada" de la tabla "solicitud_material",
     * lo agrupa por el identificador del material y lo ordena de manera descendente. Devuelve un array asociativo.
     * @return array Devuelve un array asociativo con los datos del material.
     */
    public function materialMasSolicitado()
    {

        $sql = "SELECT m.*, SUM(sm.cantidad_solicitada) AS total_cantidad_solicitada
                FROM material m
                JOIN solicitud_material sm ON m.id_material = sm.id_material
                GROUP BY m.id_material
                ORDER BY cantidad_solicitada DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
