<?php
/**
 * CLASE BASE MODEL
 * 
 * Clase padre de todos los modelos.
 * Proporciona acceso a la conexión de base de datos ($this->db)
 * que todos los modelos heredados utilizan para consultas.
 * 
 * Todos los modelos (User, Consumo, Nutriente, etc.) heredan de esta clase
 * y pueden acceder a $this->db para ejecutar consultas SQL.
 */
class Model{
  /**
   * @var PDO $db - Conexión a la base de datos
   * 
   * Disponible para todos los modelos que hereden de esta clase
   */
  protected $db;
  
  /**
   * Constructor - Obtiene la conexión a la base de datos
   */
  function __construct(){ 
    // Obtiene la conexión singleton de la clase Database
    $this->db=Database::getConnection();
  }
}