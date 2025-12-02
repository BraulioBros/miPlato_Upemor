<?php
/**
 * MODELO CONSUMO
 * 
 * Maneja todos los registros de consumo de alimentos.
 * Un "consumo" es cuando un estudiante registra que comió una cantidad
 * de un alimento específico en una fecha determinada.
 * 
 * Tabla: consumos
 * - id: ID único del consumo
 * - usuario_id: ID del estudiante que comió
 * - comida_id: ID de la comida consumida
 * - cantidad_gramos: Cantidad en gramos
 * - fecha: Fecha del consumo
 */
class Consumo extends Model{

  /**
   * Obtiene todos los consumos de un usuario
   * 
   * @param int $u - ID del usuario
   * @return array - Array de consumos ordenados por fecha DESC
   */
  public function allByUser($u){
    $st=$this->db->prepare("SELECT c.* FROM consumos c WHERE c.usuario_id=? ORDER BY fecha DESC, id DESC");
    $st->execute([$u]);
    return $st->fetchAll();
  }

  /**
   * Crea un nuevo registro de consumo
   * 
   * @param array $d - Datos: ['usuario_id', 'comida_id', 'cantidad_gramos', 'fecha']
   * @return bool - True si se insertó exitosamente
   */
  public function create($d){
    $st=$this->db->prepare("INSERT INTO consumos (usuario_id, comida_id, cantidad_gramos, fecha) VALUES (?,?,?,?)");
    return $st->execute([$d['usuario_id'],$d['comida_id'],$d['cantidad_gramos'],$d['fecha']]);
  }

  /**
   * Elimina un consumo (con validación de propietario)
   * 
   * @param int $id - ID del consumo a eliminar
   * @param int $u - ID del usuario (para validar que sea el propietario)
   * @return bool - True si se eliminó exitosamente
   */
  public function delete($id,$u){
    $st=$this->db->prepare("DELETE FROM consumos WHERE id=? AND usuario_id=?");
    return $st->execute([$id,$u]);
  }

  /**
   * Obtiene el resumen diario de consumo (últimos 14 días)
   * 
   * Agrupa por fecha y suma las kcal de todos los consumos del día.
   * 
   * @param int $u - ID del usuario
   * @return array - Array con fecha y kcal total por día
   */
  public function resumenDiario($u){
    $sql="SELECT fecha, SUM((cantidad_gramos * cm.calorias_por_100g)/100) AS kcal
          FROM consumos c 
          JOIN comidas cm ON cm.id_comida=c.comida_id
          WHERE c.usuario_id=? 
          GROUP BY fecha 
          ORDER BY fecha DESC";
    $st=$this->db->prepare($sql);
    $st->execute([$u]);
    return $st->fetchAll();
  }

  /**
   * Obtiene el resumen semanal de consumo (últimas 8 semanas)
   * 
   * Agrupa por semana (YEARWEEK) y suma las kcal de toda la semana.
   * 
   * @param int $u - ID del usuario
   * @return array - Array con desde, hasta y kcal total por semana
   */
  public function resumenSemanal($u){
    $sql="SELECT MIN(fecha) AS desde, MAX(fecha) AS hasta, SUM((cantidad_gramos * cm.calorias_por_100g)/100) AS kcal
          FROM consumos c 
          JOIN comidas cm ON cm.id_comida=c.comida_id
          WHERE c.usuario_id=? 
          GROUP BY YEARWEEK(fecha,1) 
          ORDER BY desde DESC";
    $st=$this->db->prepare($sql);
    $st->execute([$u]);
    return $st->fetchAll();
  }

  /**
   * Obtiene el detalle completo de consumos de un día específico
   * 
   * Incluye: nombre de comida, gramos, kcal/100g, kcal totales, nutriente
   * 
   * @param int $u - ID del usuario
   * @param string $fecha - Fecha en formato YYYY-MM-DD
   * @return array - Array con detalles de cada consumo del día
   */
  public function detallePorFecha($u,$fecha){
    $sql="SELECT c.fecha, 
                 cm.nombre AS comida, 
                 c.cantidad_gramos AS gramos, 
                 cm.calorias_por_100g AS kcal_100g,
                 (c.cantidad_gramos * cm.calorias_por_100g / 100) AS kcal, 
                 COALESCE(n.nombre, 'Sin nutriente') AS nutriente
          FROM consumos c 
          JOIN comidas cm ON cm.id_comida=c.comida_id
          LEFT JOIN nutrientes n ON cm.id_nutriente=n.id_nutriente
          WHERE c.usuario_id=? AND c.fecha=?
          ORDER BY c.fecha DESC, c.id DESC";
    $st=$this->db->prepare($sql);
    $st->execute([$u,$fecha]);
    return $st->fetchAll();
  }

  /**
   * Obtiene el detalle completo de consumos en un rango de fechas
   * 
   * Incluye: nombre de comida, gramos, kcal/100g, kcal totales, nutriente
   * 
   * @param int $u - ID del usuario
   * @param string $desde - Fecha inicial en formato YYYY-MM-DD
   * @param string $hasta - Fecha final en formato YYYY-MM-DD
   * @return array - Array con detalles de todos los consumos del rango
   */
  public function detalleRango($u,$desde,$hasta){
    $sql="SELECT c.fecha, 
                 cm.nombre AS comida, 
                 c.cantidad_gramos AS gramos, 
                 cm.calorias_por_100g AS kcal_100g,
                 (c.cantidad_gramos * cm.calorias_por_100g / 100) AS kcal, 
                 COALESCE(n.nombre, 'Sin nutriente') AS nutriente
          FROM consumos c 
          JOIN comidas cm ON cm.id_comida=c.comida_id
          LEFT JOIN nutrientes n ON cm.id_nutriente=n.id_nutriente
          WHERE c.usuario_id=? AND c.fecha BETWEEN ? AND ?
          ORDER BY c.fecha DESC, c.id DESC";
    $st=$this->db->prepare($sql);
    $st->execute([$u,$desde,$hasta]);
    return $st->fetchAll();
  }
}
