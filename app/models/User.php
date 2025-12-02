<?php
/**
 * MODELO USER (USUARIOS)
 * 
 * Maneja todas las operaciones relacionadas con usuarios.
 * Usuarios pueden tener roles: 'admin', 'nutriologo', 'estudiante'
 * 
 * Tabla: usuarios
 * - id: ID único del usuario
 * - nombre: Nombre del usuario
 * - apellidos: Apellidos del usuario
 * - correo: Email único para login
 * - password: Hash de contraseña (PASSWORD_BCRYPT)
 * - rol: Tipo de usuario (admin, nutriologo, estudiante)
 */
class User extends Model{
  
  /**
   * Busca un usuario por su email
   * 
   * Se usa principalmente en el login para validar credenciales
   * Búsqueda case-insensitive y trimmeada
   * 
   * @param string $e - Email del usuario
   * @return array|false - Datos del usuario o false si no existe
   */
  public function findByEmail($e){
    $e = trim($e);
    $st=$this->db->prepare("SELECT * FROM usuarios WHERE LOWER(correo)=LOWER(?) LIMIT 1");
    $st->execute([$e]);
    return $st->fetch();
  }
  
  /**
   * Busca un usuario por su ID
   * 
   * @param int $id - ID del usuario
   * @return array|false - Datos del usuario o false si no existe
   */
  public function find($id){
    $st=$this->db->prepare("SELECT * FROM usuarios WHERE id=?");
    $st->execute([$id]);
    return $st->fetch();
  }
  
  /**
   * Obtiene todos los usuarios del sistema
   * 
   * Retorna solo ciertos campos por seguridad (sin contraseña)
   * 
   * @return array - Array de todos los usuarios ordenados DESC por ID
   */
  public function all(){
    return $this->db->query("SELECT id,nombre,apellidos,correo,rol FROM usuarios ORDER BY id DESC")->fetchAll();
  }
  
  /**
   * Crea un nuevo usuario
   * 
   * Campos requeridos:
   * - nombre, apellidos, correo, password (hasheada), rol
   * 
   * @param array $d - Datos: ['nombre', 'apellidos', 'correo', 'password', 'rol']
   * @return int - ID del nuevo usuario insertado
   */
  public function create($d){
    $st=$this->db->prepare("INSERT INTO usuarios (nombre,apellidos,correo,password,rol) VALUES (?,?,?,?,?)");
    $st->execute([$d['nombre'],$d['apellidos'],$d['correo'],$d['password'],$d['rol']]);
    return $this->db->lastInsertId();
  }
  
  /**
   * Actualiza los datos de un usuario existente
   * 
   * Nota: No actualiza la contraseña (hay métodos separados para eso)
   * 
   * @param int $id - ID del usuario a actualizar
   * @param array $d - Datos: ['nombre', 'apellidos', 'correo', 'rol']
   * @return bool - True si se actualizó exitosamente
   */
  public function update($id,$d){
    $st=$this->db->prepare("UPDATE usuarios SET nombre=?,apellidos=?,correo=?,rol=? WHERE id=?");
    return $st->execute([$d['nombre'],$d['apellidos'],$d['correo'],$d['rol'],$id]);
  }
  
  /**
   * Elimina un usuario completamente del sistema
   * 
   * Nota: Esto también elimina sus registros relacionados dependiendo de constraints de FK
   * 
   * @param int $id - ID del usuario a eliminar
   * @return bool - True si se eliminó exitosamente
   */
  public function delete($id){
    $st=$this->db->prepare("DELETE FROM usuarios WHERE id=?");
    return $st->execute([$id]);
  }
  
  /**
   * Cuenta la cantidad de usuarios con un rol específico
   * 
   * @param string $r - Rol a contar ('admin', 'nutriologo', 'estudiante')
   * @return int - Cantidad de usuarios con ese rol
   */
  public function countByRole($r){
    $st=$this->db->prepare("SELECT COUNT(*) as count FROM usuarios WHERE rol=?");
    $st->execute([$r]);
    $result=$st->fetch();
    return $result['count'];
  }
  
  /**
   * Busca un usuario por email excluyendo un ID específico
   * 
   * Se usa para validar que el email no esté duplicado al actualizar
   * Búsqueda case-insensitive y trimmeada
   * 
   * @param string $e - Email del usuario
   * @param int $id - ID a excluir de la búsqueda
   * @return array|false - Datos del usuario o false si no existe
   */
  public function findByEmailExcludingId($e, $id){
    $e = trim($e);
    $st=$this->db->prepare("SELECT * FROM usuarios WHERE LOWER(correo)=LOWER(?) AND id!=? LIMIT 1");
    $st->execute([$e, $id]);
    return $st->fetch();
  }
}
