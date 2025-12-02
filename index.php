<?php
/**
 * ARCHIVO PRINCIPAL - MiPlato Upemor
 * 
 * Este es el archivo de entrada de la aplicación. Maneja:
 * - Inicialización de sesiones
 * - Carga de archivos core (configuración, helpers, clases base)
 * - Enrutamiento de peticiones
 * - Manejo de autenticación para login/registro
 */

// Inicializa la sesión PHP si no está activa
if(session_status()===PHP_SESSION_NONE)session_start();

// Carga la configuración de base de datos y constantes
require __DIR__.'/app/config/config.php';

// Carga funciones helper para generación de URLs
require __DIR__.'/app/helpers/url.php';

// Carga clase base para todos los controladores
require __DIR__.'/app/core/Controller.php';

// Carga clase base para todos los modelos
require __DIR__.'/app/core/Model.php';

// Carga clase para conexión a base de datos
require __DIR__.'/app/core/Database.php';

// Carga clase para enrutamiento y despachador de controladores
require __DIR__.'/app/core/Router.php';

// Desactiva cacheo para evitar problemas con datos antiguos
nocache_headers_safe();

// Obtiene el controlador y acción de la URL, por defecto va a auth/login
$c=$_GET['controller']??'auth'; 
$a=$_GET['action']??'login';

// Si está en la página de login/registro y ya está autenticado, destruye la sesión
if($c==='auth' && in_array($a,['login','register'])){
  if(!empty($_SESSION['user'])){ 
    $_SESSION=[]; 
    if(ini_get('session.use_cookies')){ 
      $p=session_get_cookie_params(); 
      setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']); 
    } 
    session_destroy(); 
    session_start(); 
    session_regenerate_id(true); 
  }
}

// Despacha la petición al controlador y acción apropiados
Router::dispatch();
