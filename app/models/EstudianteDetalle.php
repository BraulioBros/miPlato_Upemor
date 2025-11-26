<?php
class EstudianteDetalle extends Model{
public function findByUserId($u){$st=$this->db->prepare("SELECT * FROM estudiantes_detalle WHERE usuario_id=?");$st->execute([$u]);return $st->fetch();}
public function upsert($u,$p,$a,$fn,$s,$act){ if($this->findByUserId($u)){ $st=$this->db->prepare("UPDATE estudiantes_detalle SET peso=?,altura=?,fecha_nacimiento=?,sexo=?,actividad=? WHERE usuario_id=?");return $st->execute([$p,$a,$fn,$s,$act,$u]); } else { $st=$this->db->prepare("INSERT INTO estudiantes_detalle (usuario_id,peso,altura,fecha_nacimiento,sexo,actividad) VALUES (?,?,?,?,?,?)");return $st->execute([$u,$p,$a,$fn,$s,$act]); } }
public function allWithUsers(){ return $this->db->query("SELECT u.id,u.nombre,u.apellidos,u.correo,ed.peso,ed.altura,ed.fecha_nacimiento,ed.sexo,ed.actividad FROM usuarios u LEFT JOIN estudiantes_detalle ed ON ed.usuario_id=u.id WHERE u.rol='estudiante' ORDER BY u.id DESC")->fetchAll(); }
}
