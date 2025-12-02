<?php
/**
 * CLASE BASE CONTROLLER
 * 
 * Clase padre de todos los controladores. Proporciona:
 * - Métodos para renderizar vistas (view, partial)
 * - Control de autenticación y autorización
 * - Métodos de protección contra acceso no autorizado
 */
class Controller{
  /**
   * Renderiza una vista completa sin layout (para páginas especiales como login)
   * 
   * @param string $file - Ruta de la vista (ej: 'auth/login')
   * @param array $data - Variables a pasar a la vista
   * 
   * Renderiza la vista sin incluir layout.php
   */
  protected function fullView($file,$data=[]){
    extract($data); // Convierte el array en variables individuales
    include __DIR__.'/../views/'.$file.'.php'; // Incluye solo la vista, sin layout
  }

  /**
   * Renderiza una vista completa con layout
   * 
   * @param string $file - Ruta de la vista (ej: 'dashboard/estudiante')
   * @param array $data - Variables a pasar a la vista
   * 
   * Carga layout.php que incluirá la vista específica
   */
  protected function view($file,$data=[]){
    $view=$file;
    extract($data); // Convierte el array en variables individuales
    include __DIR__.'/../views/layout.php'; // Incluye el layout principal
  }
  
  /**
   * Renderiza un fragmento (partial) de vista sin layout
   * 
   * @param string $file - Ruta de la vista parcial
   * @param array $data - Variables a pasar a la vista
   * 
   * Útil para vistas que se renderizan dentro del layout
   */
  protected function partial($file,$data=[]){
    extract($data); // Convierte el array en variables individuales
    include __DIR__.'/../views/'.$file.'.php'; // Incluye la vista
  }
  
  /**
   * Requiere que el usuario esté autenticado
   * 
   * Si no hay sesión de usuario, redirige al login
   */
  protected function requireLogin(){
    if(session_status()===PHP_SESSION_NONE)session_start();
    if(empty($_SESSION['user'])){
      header('Location: index.php?controller=auth&action=login');
      exit;
    }
    nocache_headers_safe();
  }
  
  /**
   * Requiere que el usuario tenga un rol específico
   * 
   * @param string|array $r - Rol(es) requerido(s) (ej: 'admin' o ['admin', 'nutriologo'])
   * 
   * Si el usuario no tiene el rol requerido, redirige al login
   */
  protected function requireRole($r){
    $this->requireLogin(); // Primero verifica que esté logueado
    $u=$_SESSION['user'];
    $r=(array)$r; // Convierte a array si es string
    if(!in_array($u['rol'],$r)){ // Verifica que el rol del usuario esté en la lista
      header('Location: index.php?controller=auth&action=login');
      exit;
    }
  }
}