<?php
/**
 * MODELO ESTUDIANTE DETALLE
 * 
 * Maneja los datos antropométricos y de salud de los estudiantes.
 * Esta tabla amplía la información del usuario (tabla usuarios) con
 * datos específicos necesarios para cálculos nutricionales.
 * 
 * Tabla: estudiantes_detalle
 * - usuario_id: ID del usuario (FK a usuarios)
 * - peso: Peso en kilogramos
 * - altura: Altura en centímetros
 * - fecha_nacimiento: Fecha de nacimiento (YYYY-MM-DD)
 * - sexo: Sexo del estudiante (M/F)
 * - actividad: Factor de actividad física (1.2 a 2.0)
 */
class EstudianteDetalle extends Model{
  
  /**
   * Obtiene los detalles de un estudiante por su ID de usuario
   * 
   * @param int $u - ID del usuario (estudiante)
   * @return array|false - Datos del estudiante o false si no existen
   */
  public function findByUserId($u){
    $st=$this->db->prepare("SELECT * FROM estudiantes_detalle WHERE usuario_id=?");
    $st->execute([$u]);
    return $st->fetch();
  }
  
  /**
   * Crea o actualiza los detalles de un estudiante (INSERT or UPDATE)
   * 
   * Si el estudiante ya existe, actualiza sus datos.
   * Si no existe, crea un nuevo registro.
   * 
   * @param int $u - ID del usuario
   * @param float $p - Peso en kilogramos (puede ser NULL)
   * @param float $a - Altura en centímetros (puede ser NULL)
   * @param string $fn - Fecha de nacimiento YYYY-MM-DD (puede ser NULL)
   * @param string $s - Sexo: 'M' o 'F' (puede ser NULL)
   * @param float $act - Factor de actividad: 1.2 (sedentario) a 2.0 (muy activo)
   * @return bool - True si se insertó o actualizó exitosamente
   */
  public function upsert($u,$p,$a,$fn,$s,$act){
    if($this->findByUserId($u)){
      // Actualizar si ya existe
      $st=$this->db->prepare("UPDATE estudiantes_detalle SET peso=?,altura=?,fecha_nacimiento=?,sexo=?,actividad=? WHERE usuario_id=?");
      return $st->execute([$p,$a,$fn,$s,$act,$u]);
    } else {
      // Insertar si no existe
      $st=$this->db->prepare("INSERT INTO estudiantes_detalle (usuario_id,peso,altura,fecha_nacimiento,sexo,actividad) VALUES (?,?,?,?,?,?)");
      return $st->execute([$u,$p,$a,$fn,$s,$act]);
    }
  }
  
  /**
   * Obtiene todos los estudiantes con sus datos de usuario combinados
   * 
   * Combina información de:
   * - Tabla usuarios (nombre, apellidos, correo)
   * - Tabla estudiantes_detalle (peso, altura, fecha_nacimiento, etc)
   * 
   * @return array - Array con datos combinados de todos los estudiantes
   */
  public function allWithUsers(){
    return $this->db->query("SELECT u.id,u.nombre,u.apellidos,u.correo,ed.peso,ed.altura,ed.fecha_nacimiento,ed.sexo,ed.actividad FROM usuarios u LEFT JOIN estudiantes_detalle ed ON ed.usuario_id=u.id WHERE u.rol='estudiante' ORDER BY u.id DESC")->fetchAll();
  }
}
