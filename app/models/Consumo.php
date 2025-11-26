<?php
class Consumo extends Model{

public function allByUser($u){
  $st=$this->db->prepare("SELECT c.* FROM consumos c WHERE c.usuario_id=? ORDER BY fecha DESC, id DESC");
  $st->execute([$u]);return $st->fetchAll();
}

public function create($d){
  $st=$this->db->prepare("INSERT INTO consumos (usuario_id, comida_id, cantidad_gramos, fecha) VALUES (?,?,?,?)");
  return $st->execute([$d['usuario_id'],$d['comida_id'],$d['cantidad_gramos'],$d['fecha']]);
}

public function delete($id,$u){
  $st=$this->db->prepare("DELETE FROM consumos WHERE id=? AND usuario_id=?");
  return $st->execute([$id,$u]);
}

public function resumenDiario($u){
  $sql="SELECT fecha, SUM((cantidad_gramos * cm.calorias_por_100g)/100) AS kcal
        FROM consumos c JOIN comidas cm ON cm.id_comida=c.comida_id
        WHERE c.usuario_id=? GROUP BY fecha ORDER BY fecha DESC";
  $st=$this->db->prepare($sql);$st->execute([$u]);return $st->fetchAll();
}

public function resumenSemanal($u){
  $sql="SELECT MIN(fecha) AS desde, MAX(fecha) AS hasta, SUM((cantidad_gramos * cm.calorias_por_100g)/100) AS kcal
        FROM consumos c JOIN comidas cm ON cm.id_comida=c.comida_id
        WHERE c.usuario_id=? GROUP BY YEARWEEK(fecha,1) ORDER BY desde DESC";
  $st=$this->db->prepare($sql);$st->execute([$u]);return $st->fetchAll();
}

public function detallePorFecha($u,$fecha){
  $sql="SELECT c.fecha, cm.nombre AS comida, c.cantidad_gramos AS gramos, cm.calorias_por_100g AS kcal_100g,
               (c.cantidad_gramos * cm.calorias_por_100g / 100) AS kcal, COALESCE(n.nombre, 'Sin nutriente') AS nutriente
        FROM consumos c 
        JOIN comidas cm ON cm.id_comida=c.comida_id
        LEFT JOIN nutrientes n ON cm.id_nutriente=n.id_nutriente
        WHERE c.usuario_id=? AND c.fecha=?
        ORDER BY c.fecha DESC, c.id DESC";
  $st=$this->db->prepare($sql);$st->execute([$u,$fecha]);return $st->fetchAll();
}

public function detalleRango($u,$desde,$hasta){
  $sql="SELECT c.fecha, cm.nombre AS comida, c.cantidad_gramos AS gramos, cm.calorias_por_100g AS kcal_100g,
               (c.cantidad_gramos * cm.calorias_por_100g / 100) AS kcal, COALESCE(n.nombre, 'Sin nutriente') AS nutriente
        FROM consumos c 
        JOIN comidas cm ON cm.id_comida=c.comida_id
        LEFT JOIN nutrientes n ON cm.id_nutriente=n.id_nutriente
        WHERE c.usuario_id=? AND c.fecha BETWEEN ? AND ?
        ORDER BY c.fecha DESC, c.id DESC";
  $st=$this->db->prepare($sql);$st->execute([$u,$desde,$hasta]);return $st->fetchAll();
}
}
