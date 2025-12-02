<?php
/**
 * CONTROLADOR DE AUTENTICACIÓN
 * 
 * Maneja todo lo relacionado con:
 * - Login de usuarios
 * - Registro de nuevos estudiantes
 * - Logout (cierre de sesión)
 * - Redirección según rol del usuario
 */

require_once __DIR__.'/../models/User.php';
require_once __DIR__.'/../models/NutriologoDetalle.php';

class AuthController extends Controller{
  
  /**
   * Muestra la página de login
   * 
   * Si el usuario ya está autenticado, lo redirige a su dashboard según su rol
   */
  public function login(){ 
    nocache_headers_safe();
    
    // Si ya hay una sesión activa, redirige al dashboard correspondiente
    if(!empty($_SESSION['user'])){ 
      $r=$_SESSION['user']['rol'];
      if($r==='admin') redirect('admin','dashboard');
      elseif($r==='nutriologo') redirect('nutriologo','dashboard');
      else redirect('estudiante','dashboard');
    }
    
    // Muestra la vista de login sin layout
    $this->fullView('auth/login',[]);
  }
  
  /**
   * Muestra la página de registro
   * 
   * Si el usuario ya está autenticado, lo redirige a su dashboard según su rol
   */
  public function register(){ 
    nocache_headers_safe();
    
    // Si ya hay una sesión activa, redirige al dashboard correspondiente
    if(!empty($_SESSION['user'])){ 
      $r=$_SESSION['user']['rol'];
      if($r==='admin') redirect('admin','dashboard');
      elseif($r==='nutriologo') redirect('nutriologo','dashboard');
      else redirect('estudiante','dashboard');
    }
    
    // Muestra la vista de registro sin layout
    $this->fullView('auth/register',[]);
  }
  
  /**
   * Procesa el login de un usuario
   * 
   * Proceso:
   * 1. Obtiene email y contraseña del formulario
   * 2. Busca el usuario en la BD
   * 3. Verifica la contraseña con password_verify()
   * 4. Crea la sesión del usuario
   * 5. Si es nutriólogo, verifica si completó su perfil
   * 6. Redirige al dashboard correspondiente
   */
  public function doLogin(){ 
    // Obtiene credenciales del formulario
    $email=$_POST['correo']??'';
    $pass=$_POST['contrasena']??'';
    
    // Busca el usuario por email
    $u=(new User())->findByEmail($email);
    
    // Si no existe o contraseña es incorrecta, muestra error
    if(!$u||!password_verify($pass,$u['password'])){
      redirect('auth','login',['error'=>'Credenciales inválidas']);
    }
    
    // Crea la sesión del usuario
    $_SESSION['user']=[
      'id'=>$u['id'],
      'nombre'=>$u['nombre'],
      'apellidos'=>$u['apellidos'],
      'correo'=>$u['correo'],
      'rol'=>$u['rol']
    ];
    
    // Si es nutriólogo, verifica si completó su perfil (cédula y teléfono)
    if($u['rol']==='nutriologo'){ 
      $nd=new NutriologoDetalle();
      $det=$nd->findByUserId($u['id']);
      // Si no completó el perfil, redirige a completar
      if(!$det || (int)$det['completed']===0){ 
        redirect('nutriologo','completar');
      }
    }
    
    // Redirige al dashboard según el rol del usuario
    switch($u['rol']){
      case 'admin':
        redirect('admin','dashboard');
      case 'nutriologo':
        redirect('nutriologo','dashboard');
      default:
        redirect('estudiante','dashboard');
    }
  }
  
  /**
   * Procesa el registro de un nuevo estudiante
   * 
   * Proceso:
   * 1. Valida que todos los campos sean completados
   * 2. Valida que la fecha de nacimiento sea válida (1980-2009)
   * 3. Valida que la contraseña tenga al menos 6 caracteres
   * 4. Valida que el email no esté duplicado
   * 5. Crea el usuario en la BD
   * 6. Crea el registro de datos del estudiante
   * 7. Redirige al login
   */
  public function doRegister(){
    // Obtiene y valida la fecha de nacimiento
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    if ($fecha_nacimiento){
      $min_fecha = '1980-01-01';
      $max_fecha = '2009-12-31';
      $timestamp = strtotime($fecha_nacimiento);
      if ($timestamp < strtotime($min_fecha) || $timestamp > strtotime($max_fecha)){
        redirect('auth','login',['error'=>'La fecha de nacimiento debe estar entre 01/01/1980 y 31/12/2009']);
      }
    }
    
    // Obtiene los datos del formulario
    $n=trim($_POST['nombre']??'');
    $a=trim($_POST['apellidos']??'');
    $c=trim($_POST['correo']??'');
    $p=$_POST['contrasena']??'';
    $fn=$_POST['fecha_nacimiento']??null;
    
    // Valida que todos los campos estén completo
    if(!$n||!$a||!$c||strlen($p)<6||!$fn){
      redirect('auth','login',['error'=>'Completa todos los campos']);
    }
    
    // Valida que el email no esté duplicado
    $uM=new User();
    if($uM->findByEmail($c)){
      redirect('auth','login',['error'=>'⚠️ Este correo ya está registrado en el sistema']);
    }
    
    // Intenta crear el usuario
    try{ 
      // Crea el usuario con contraseña hasheada
      $id=$uM->create([
        'nombre'=>$n,
        'apellidos'=>$a,
        'correo'=>$c,
        'password'=>password_hash($p,PASSWORD_BCRYPT),
        'rol'=>'estudiante'
      ]);
      
      // Carga el modelo de EstudianteDetalle
      require_once __DIR__.'/../models/EstudianteDetalle.php';
      
      // Crea el registro de datos del estudiante con valores por defecto
      (new EstudianteDetalle())->upsert($id,null,null,$fn,'M',1.4);
      
      // Redirige al login con mensaje de éxito
      redirect('auth','login',['ok'=>'Cuenta creada, inicia sesión']);
    }catch(Exception $e){ 
      // Si el email ya existe, muestra error
      redirect('auth','login',['error'=>'⚠️ Este correo ya está registrado en el sistema']);
    } 
  }
  
  /**
   * Cierra la sesión del usuario (logout)
   * 
   * Proceso:
   * 1. Inicia la sesión si no está activa
   * 2. Limpia todas las variables de sesión
   * 3. Destruye la sesión
   * 4. Regenera el ID de sesión (medida de seguridad)
   * 5. Redirige al login
   */
  public function logout(){ 
    // Inicia sesión si no está activa
    if(session_status()===PHP_SESSION_NONE) session_start();
    
    // Limpia todas las variables de sesión
    $_SESSION=[];
    
    // Destruye la sesión
    session_destroy();
    
    // Regenera el ID de sesión por seguridad
    if(function_exists('session_regenerate_id')) session_regenerate_id(true);
    
    // Desactiva cacheo
    nocache_headers_safe();
    
    // Redirige al login
    redirect('auth','login');
  }
}
