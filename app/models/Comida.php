<?php
class Comida extends Model
{
    // Listar todas las comidas con nombre del nutriente
    public function all()
    {
        $sql = "SELECT c.id_comida, c.nombre, c.descripcion, c.calorias_por_100g, c.id_nutriente, n.nombre AS nutriente
                FROM comidas c
                LEFT JOIN nutrientes n ON c.id_nutriente = n.id_nutriente
                ORDER BY c.id_comida DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // Buscar una comida por su id_comida
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

    // Crear comida
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

    // Actualizar comida
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

    // Borrar comida
    public function delete($id_comida)
    {
        $st = $this->db->prepare("DELETE FROM comidas WHERE id_comida = ?");
        return $st->execute([$id_comida]);
    }
}
