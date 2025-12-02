<?php
/**
 * CLASE ROUTER - Sistema de Enrutamiento
 * 
 * Responsable de:
 * - Obtener el controlador y acción de la URL
 * - Cargar dinámicamente el archivo del controlador
 * - Instanciar el controlador
 * - Ejecutar la acción solicitada
 * 
 * Ejemplo de uso:
 * http://localhost/Estancia/?controller=estudiante&action=dashboard
 * Esto ejecutará: EstudianteController->dashboard()
 */
class Router{
  /**
   * Despacha la petición al controlador y acción apropiados
   * 
   * Proceso:
   * 1. Obtiene 'controller' y 'action' de la URL
   * 2. Construye el nombre de la clase del controlador (ej: "estudiante" -> "EstudianteController")
   * 3. Verifica que el archivo del controlador exista
   * 4. Requiere e instancia el controlador
   * 5. Verifica que el método (acción) exista
   * 6. Ejecuta la acción
   */
  public static function dispatch(){
    // Obtiene el nombre del controlador de la URL, por defecto 'auth'
    $c=$_GET['controller']??'auth';
    
    // Obtiene el nombre de la acción de la URL, por defecto 'login'
    $a=$_GET['action']??'login';
    
    // Construye el nombre de la clase: ej "estudiante" -> "EstudianteController"
    $cn=ucfirst($c).'Controller';
    
    // Construye la ruta al archivo del controlador
    $f=__DIR__.'/../controllers/'.$cn.'.php';
    
    // Verifica que el archivo exista, sino devuelve error 404
    if(!file_exists($f)){
      http_response_code(404);
      echo'Controller not found';
      exit;
    }
    
    // Incluye el archivo del controlador
    require_once $f;
    
    // Verifica que la clase exista en el archivo
    if(!class_exists($cn)){
      echo'Class not found';
      exit;
    }
    
    // Instancia el controlador
    $o=new $cn();
    
    // Verifica que el método (acción) exista en el controlador
    if(!method_exists($o,$a)){
      echo'Action not found';
      exit;
    }
    
    // Ejecuta la acción
    $o->$a();
  }
}
