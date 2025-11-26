<?php
class User extends Model{
public function findByEmail($e){$st=$this->db->prepare("SELECT * FROM usuarios WHERE correo=? LIMIT 1");$st->execute([$e]);return $st->fetch();}
public function find($id){$st=$this->db->prepare("SELECT * FROM usuarios WHERE id=?");$st->execute([$id]);return $st->fetch();}
public function all(){return $this->db->query("SELECT id,nombre,apellidos,correo,rol FROM usuarios ORDER BY id DESC")->fetchAll();}
public function create($d){$st=$this->db->prepare("INSERT INTO usuarios (nombre,apellidos,correo,password,rol) VALUES (?,?,?,?,?)");$st->execute([$d['nombre'],$d['apellidos'],$d['correo'],$d['password'],$d['rol']]);return $this->db->lastInsertId();}
public function update($id,$d){$st=$this->db->prepare("UPDATE usuarios SET nombre=?,apellidos=?,correo=?,rol=? WHERE id=?");return $st->execute([$d['nombre'],$d['apellidos'],$d['correo'],$d['rol'],$id]);}
public function delete($id){$st=$this->db->prepare("DELETE FROM usuarios WHERE id=?");return $st->execute([$id]);}
public function countByRole($r){$st=$this->db->prepare("SELECT COUNT(*) as count FROM usuarios WHERE rol=?");$st->execute([$r]);$result=$st->fetch();return $result['count'];}
}
