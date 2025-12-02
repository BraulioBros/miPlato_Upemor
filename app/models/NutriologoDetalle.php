<?php
/**
 * MODELO NUTRIOLOGO DETALLE
 * 
 * Maneja los datos profesionales adicionales de los nutriólogos.
 * Esta tabla amplía la información del usuario (tabla usuarios) con
 * datos específicos para identificación profesional.
 * 
 * Tabla: nutriologos_detalle
 * - usuario_id: ID del usuario (FK a usuarios)
 * - cedula: Número de cédula profesional
 * - telefono: Teléfono de contacto
 * - completed: Flag (0/1) indicando si completó el perfil (requiere cédula + teléfono)
 */
class NutriologoDetalle extends Model{
  
  /**
   * Obtiene los detalles de un nutriólogo por su ID de usuario
   * 
   * @param int $u - ID del usuario (nutriólogo)
   * @return array|false - Datos del nutriólogo o false si no existen
   */
  public function findByUserId($u){
    $st=$this->db->prepare("SELECT * FROM nutriologos_detalle WHERE usuario_id=?");
    $st->execute([$u]);
    return $st->fetch();
  }
  
  /**
   * Crea o actualiza los detalles de un nutriólogo (INSERT or UPDATE)
   * 
   * Si el nutriólogo ya existe, actualiza sus datos.
   * Si no existe, crea un nuevo registro.
   * 
   * Se usa al:
   * - Primer login (completar cédula y teléfono)
   * - Editar datos profesionales
   * 
   * @param int $u - ID del usuario
   * @param string $c - Número de cédula profesional (puede ser NULL)
   * @param string $t - Teléfono de contacto (puede ser NULL)
   * @param int $cmp - Flag completed: 0 = incompleto, 1 = completado (default: 1)
   * @return bool - True si se insertó o actualizó exitosamente
   */
  public function upsert($u,$c,$t,$cmp=1){
    if($this->findByUserId($u)){
      // Actualizar si ya existe
      $st=$this->db->prepare("UPDATE nutriologos_detalle SET cedula=?,telefono=?,completed=? WHERE usuario_id=?");
      return $st->execute([$c,$t,$cmp,$u]);
    } else {
      // Insertar si no existe
      $st=$this->db->prepare("INSERT INTO nutriologos_detalle (usuario_id,cedula,telefono,completed) VALUES (?,?,?,?)");
      return $st->execute([$u,$c,$t,$cmp]);
    }
  }
  
  /**
   * Obtiene todos los nutriólogos con sus datos de usuario combinados
   * 
   * Combina información de:
   * - Tabla usuarios (nombre, apellidos, correo)
   * - Tabla nutriologos_detalle (cédula, teléfono)
   * 
   * @return array - Array con datos combinados de todos los nutriólogos
   */
  public function allWithUsers(){
    return $this->db->query("SELECT u.id,u.nombre,u.apellidos,u.correo,nd.cedula,nd.telefono FROM usuarios u LEFT JOIN nutriologos_detalle nd ON u.id=nd.usuario_id WHERE u.rol='nutriologo' ORDER BY u.id DESC")->fetchAll();
  }
}
