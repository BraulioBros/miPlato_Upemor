<?php
/**
 * MODELO COMIDA
 * 
 * Maneja todas las comidas del sistema.
 * Una comida es un alimento específico (ej: "Pollo a la plancha")
 * que puede tener un nutriente principal asociado.
 * 
 * Tabla: comidas
 * - id_comida: ID único de la comida
 * - nombre: Nombre descriptivo (ej: "Arroz blanco")
 * - descripcion: Detalles adicionales (ej: "Arroz cocido al vapor")
 * - calorias_por_100g: Calorías contenidas en 100 gramos
 * - id_nutriente: ID del nutriente principal (puede ser NULL)
 */
class Comida extends Model
{
    /**
     * Obtiene todas las comidas del sistema
     * 
     * Incluye el nombre del nutriente asociado (si existe)
     * 
     * @return array - Array de comidas con información de nutrientes
     */
    public function all()
    {
        $sql = "SELECT c.id_comida, c.nombre, c.descripcion, c.calorias_por_100g, c.id_nutriente, n.nombre AS nutriente
                FROM comidas c
                LEFT JOIN nutrientes n ON c.id_nutriente = n.id_nutriente
                ORDER BY c.id_comida DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Busca una comida por su ID
     * 
     * Incluye el nombre del nutriente asociado
     * 
     * @param int $id_comida - ID de la comida
     * @return array|false - Datos de la comida o false si no existe
     */
    public function find($id_comida)
    {
        $sql = "SELECT c.id_comida, c.nombre, c.descripcion, c.calorias_por_100g, c.id_nutriente, n.nombre AS nutriente
                FROM comidas c
                LEFT JOIN nutrientes n ON c.id_nutriente = n.id_nutriente
                WHERE c.id_comida = ?";
        $st = $this->db->prepare($sql);
        $st->execute([$id_comida]);
        return $st->fetch();
    }

    /**
     * Crea una nueva comida
     * 
     * @param array $d - Datos: ['nombre', 'descripcion', 'calorias_por_100g', 'id_nutriente' (opcional)]
     * @return bool - True si se insertó exitosamente
     */
    public function create($d)
    {
        $st = $this->db->prepare(
            "INSERT INTO comidas (nombre, descripcion, calorias_por_100g, id_nutriente)
             VALUES (?,?,?,?)"
        );
        return $st->execute([
            $d['nombre'],
            $d['descripcion'],
            $d['calorias_por_100g'],
            !empty($d['id_nutriente']) ? $d['id_nutriente'] : null,
        ]);
    }

    /**
     * Actualiza una comida existente
     * 
     * @param int $id_comida - ID de la comida a actualizar
     * @param array $d - Datos: ['nombre', 'descripcion', 'calorias_por_100g', 'id_nutriente' (opcional)]
     * @return bool - True si se actualizó exitosamente
     */
    public function update($id_comida, $d)
    {
        $st = $this->db->prepare(
            "UPDATE comidas SET
                nombre            = ?,
                descripcion       = ?,
                calorias_por_100g = ?,
                id_nutriente      = ?
             WHERE id_comida      = ?"
        );

        return $st->execute([
            $d['nombre'],
            $d['descripcion'],
            $d['calorias_por_100g'],
            !empty($d['id_nutriente']) ? $d['id_nutriente'] : null,
            $id_comida
        ]);
    }

    /**
     * Elimina una comida del sistema
     * 
     * Nota: Los consumos asociados se eliminan automáticamente (ON DELETE CASCADE)
     * 
     * @param int $id_comida - ID de la comida a eliminar
     * @return bool - True si se eliminó exitosamente
     */
    public function delete($id_comida)
    {
        $st = $this->db->prepare("DELETE FROM comidas WHERE id_comida = ?");
        return $st->execute([$id_comida]);
    }

    /**
     * Busca una comida por su nombre
     * 
     * Se usa para validar que el nombre no esté duplicado
     * 
     * @param string $nombre - Nombre de la comida
     * @return array|false - Datos de la comida o false si no existe
     */
    public function findByName($nombre)
    {
        $sql = "SELECT c.id_comida, c.nombre, c.descripcion, c.calorias_por_100g, c.id_nutriente, n.nombre AS nutriente
                FROM comidas c
                LEFT JOIN nutrientes n ON c.id_nutriente = n.id_nutriente
                WHERE LOWER(c.nombre) = LOWER(?) LIMIT 1";
        $st = $this->db->prepare($sql);
        $st->execute([$nombre]);
        return $st->fetch();
    }

    /**
     * Busca una comida por su nombre excluyendo un ID específico
     * 
     * Se usa para validar que el nombre no esté duplicado al actualizar
     * 
     * @param string $nombre - Nombre de la comida
     * @param int $id_comida - ID a excluir de la búsqueda
     * @return array|false - Datos de la comida o false si no existe
     */
    public function findByNameExcludingId($nombre, $id_comida)
    {
        $sql = "SELECT c.id_comida, c.nombre, c.descripcion, c.calorias_por_100g, c.id_nutriente, n.nombre AS nutriente
                FROM comidas c
                LEFT JOIN nutrientes n ON c.id_nutriente = n.id_nutriente
                WHERE LOWER(c.nombre) = LOWER(?) AND c.id_comida != ? LIMIT 1";
        $st = $this->db->prepare($sql);
        $st->execute([$nombre, $id_comida]);
        return $st->fetch();
    }
}
