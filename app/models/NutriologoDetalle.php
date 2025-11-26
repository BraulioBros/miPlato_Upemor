<?php
class NutriologoDetalle extends Model{
public function findByUserId($u){$st=$this->db->prepare("SELECT * FROM nutriologos_detalle WHERE usuario_id=?");$st->execute([$u]);return $st->fetch();}
public function upsert($u,$c,$t,$cmp=1){ if($this->findByUserId($u)){ $st=$this->db->prepare("UPDATE nutriologos_detalle SET cedula=?,telefono=?,completed=? WHERE usuario_id=?");return $st->execute([$c,$t,$cmp,$u]); } else { $st=$this->db->prepare("INSERT INTO nutriologos_detalle (usuario_id,cedula,telefono,completed) VALUES (?,?,?,?)");return $st->execute([$u,$c,$t,$cmp]); } }
public function allWithUsers(){ return $this->db->query("SELECT u.id,u.nombre,u.apellidos,u.correo,nd.cedula,nd.telefono FROM usuarios u LEFT JOIN nutriologos_detalle nd ON u.id=nd.usuario_id WHERE u.rol='nutriologo' ORDER BY u.id DESC")->fetchAll(); }
}
