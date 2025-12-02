<?php
/**
 * MODELO NUTRIENTE
 * 
 * Maneja los nutrientes del sistema.
 * Los nutrientes son componentes nutritivos (proteína, carbohidrato, etc)
 * asociados a las comidas.
 * 
 * Tabla: nutrientes
 * - id_nutriente: ID único del nutriente
 * - nombre: Nombre del nutriente
 * - calorias_por_gramo: Contenido calórico (ej: proteína = 4 kcal/g)
 * - unidad_medida: Unidad (gramos, mililitros, etc)
 * - tipo: Tipo o clasificación (proteína, carbohidrato, grasa, etc)
 */
class Nutriente extends Model
{
    /**
     * Obtiene todos los nutrientes del sistema
     * 
     * @return array - Array de todos los nutrientes ordenados DESC por ID
     */
    public function all()
    {
        $sql = "SELECT id_nutriente, nombre, calorias_por_gramo, unidad_medida, tipo
                FROM nutrientes
                ORDER BY id_nutriente DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Busca un nutriente por su ID
     * 
     * @param int $id_nutriente - ID del nutriente
     * @return array|false - Datos del nutriente o false si no existe
     */
    public function find($id_nutriente)
    {
        $sql = "SELECT id_nutriente, nombre, calorias_por_gramo, unidad_medida, tipo
                FROM nutrientes
                WHERE id_nutriente = ?";
        $st = $this->db->prepare($sql);
        $st->execute([$id_nutriente]);
        return $st->fetch();
    }

    /**
     * Crea un nuevo nutriente
     * 
     * @param array $d - Datos: ['nombre', 'calorias_por_gramo', 'unidad_medida', 'tipo']
     * @return bool - True si se insertó exitosamente
     */
    public function create($d)
    {
        $st = $this->db->prepare(
            "INSERT INTO nutrientes
                (nombre, calorias_por_gramo, unidad_medida, tipo)
             VALUES (?,?,?,?)"
        );
        return $st->execute([
            $d['nombre'],
            $d['calorias_por_gramo'],
            $d['unidad_medida'],
            $d['tipo'],
        ]);
    }

    /**
     * Actualiza un nutriente existente
     * 
     * @param int $id_nutriente - ID del nutriente a actualizar
     * @param array $d - Datos: ['nombre', 'calorias_por_gramo', 'unidad_medida', 'tipo']
     * @return bool - True si se actualizó exitosamente
     */
    public function update($id_nutriente, $d)
    {
        $st = $this->db->prepare(
            "UPDATE nutrientes SET
                nombre             = ?,
                calorias_por_gramo = ?,
                unidad_medida      = ?,
                tipo               = ?
             WHERE id_nutriente     = ?"
        );
        return $st->execute([
            $d['nombre'],
            $d['calorias_por_gramo'],
            $d['unidad_medida'],
            $d['tipo'],
            $id_nutriente
        ]);
    }

    /**
     * Elimina un nutriente del sistema
     * 
     * Nota: Esto podría fallar si hay comidas asociadas (FK constraint)
     * 
     * @param int $id_nutriente - ID del nutriente a eliminar
     * @return bool - True si se eliminó exitosamente
     */
    public function delete($id_nutriente)
    {
        $st = $this->db->prepare("DELETE FROM nutrientes WHERE id_nutriente = ?");
        return $st->execute([$id_nutriente]);
    }

    /**
     * Busca un nutriente por su nombre
     * 
     * Se usa para validar que el nombre no esté duplicado
     * 
     * @param string $nombre - Nombre del nutriente
     * @return array|false - Datos del nutriente o false si no existe
     */
    public function findByName($nombre)
    {
        $sql = "SELECT id_nutriente, nombre, calorias_por_gramo, unidad_medida, tipo
                FROM nutrientes
                WHERE LOWER(nombre) = LOWER(?) LIMIT 1";
        $st = $this->db->prepare($sql);
        $st->execute([$nombre]);
        return $st->fetch();
    }

    /**
     * Busca un nutriente por su nombre excluyendo un ID específico
     * 
     * Se usa para validar que el nombre no esté duplicado al actualizar
     * 
     * @param string $nombre - Nombre del nutriente
     * @param int $id_nutriente - ID a excluir de la búsqueda
     * @return array|false - Datos del nutriente o false si no existe
     */
    public function findByNameExcludingId($nombre, $id_nutriente)
    {
        $sql = "SELECT id_nutriente, nombre, calorias_por_gramo, unidad_medida, tipo
                FROM nutrientes
                WHERE LOWER(nombre) = LOWER(?) AND id_nutriente != ? LIMIT 1";
        $st = $this->db->prepare($sql);
        $st->execute([$nombre, $id_nutriente]);
        return $st->fetch();
    }
}
