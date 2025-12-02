<?php
/**
 * CLASE DATABASE - Singleton de Conexión a BD
 * 
 * Proporciona una única conexión PDO a la base de datos MySQL.
 * Utiliza el patrón Singleton para garantizar que solo existe una
 * instancia de conexión durante toda la ejecución de la aplicación.
 * 
 * Configuración desde: app/config/config.php
 * - DB_HOST: Servidor MySQL
 * - DB_NAME: Nombre de la base de datos
 * - DB_USER: Usuario de MySQL
 * - DB_PASS: Contraseña de MySQL
 */
class Database{
  // Propiedad estática que almacena la conexión (null al inicio)
  private static $c=null;
  
  /**
   * Obtiene la conexión a la base de datos
   * 
   * Si no existe conexión, la crea. Si ya existe, devuelve la existente.
   * Esto asegura que solo hay una conexión durante toda la ejecución.
   * 
   * @return PDO - Objeto de conexión a la base de datos
   */
  public static function getConnection(){
    // Si no hay conexión, la crea
    if(self::$c===null){
      // Construye la cadena de conexión DSN
      $dsn='mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
      
      // Crea la conexión PDO con configuración de error y modo fetch
      self::$c=new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        [
          PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores
          PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC // Retorna arrays asociativos
        ]
      );
    }
    // Devuelve la conexión
    return self::$c;
  }
}
