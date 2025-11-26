<?php
class Nutriente extends Model
{
    public function all()
    {
        $sql = "SELECT id_nutriente, nombre, calorias_por_gramo, unidad_medida, tipo
                FROM nutrientes
                ORDER BY id_nutriente DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function find($id_nutriente)
    {
        $sql = "SELECT id_nutriente, nombre, calorias_por_gramo, unidad_medida, tipo
                FROM nutrientes
                WHERE id_nutriente = ?";
        $st = $this->db->prepare($sql);
        $st->execute([$id_nutriente]);
        return $st->fetch();
    }

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

    public function delete($id_nutriente)
    {
        $st = $this->db->prepare("DELETE FROM nutrientes WHERE id_nutriente = ?");
        return $st->execute([$id_nutriente]);
    }
}
