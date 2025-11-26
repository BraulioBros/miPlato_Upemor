<?php
require_once __DIR__.'/../models/User.php';require_once __DIR__.'/../models/NutriologoDetalle.php';
class AuthController extends Controller{
public function login(){ nocache_headers_safe(); if(!empty($_SESSION['user'])){ $r=$_SESSION['user']['rol']; if($r==='admin') redirect('admin','dashboard'); elseif($r==='nutriologo') redirect('nutriologo','dashboard'); else redirect('estudiante','dashboard'); } $this->view('auth/login',[]);}
public function register(){ nocache_headers_safe(); if(!empty($_SESSION['user'])){ $r=$_SESSION['user']['rol']; if($r==='admin') redirect('admin','dashboard'); elseif($r==='nutriologo') redirect('nutriologo','dashboard'); else redirect('estudiante','dashboard'); } $this->view('auth/register',[]);}
public function doLogin(){ $email=$_POST['correo']??'';$pass=$_POST['contrasena']??'';$u=(new User())->findByEmail($email); if(!$u||!password_verify($pass,$u['password'])){redirect('auth','login',['error'=>'Credenciales inválidas']);}
$_SESSION['user']=['id'=>$u['id'],'nombre'=>$u['nombre'],'apellidos'=>$u['apellidos'],'correo'=>$u['correo'],'rol'=>$u['rol']];
if($u['rol']==='nutriologo'){ $nd=new NutriologoDetalle();$det=$nd->findByUserId($u['id']); if(!$det || (int)$det['completed']===0){ redirect('nutriologo','completar');}}
switch($u['rol']){case 'admin':redirect('admin','dashboard');case 'nutriologo':redirect('nutriologo','dashboard');default:redirect('estudiante','dashboard');}}
public function doRegister(){
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
        if ($fecha_nacimiento){
            $min_fecha = '1980-01-01';
            $max_fecha = '2009-12-31';
            $timestamp = strtotime($fecha_nacimiento);
            if ($timestamp < strtotime($min_fecha) || $timestamp > strtotime($max_fecha)){
                redirect('auth','register',['error'=>'La fecha de nacimiento debe estar entre 01/01/1980 y 31/12/2009']);
            }
        }
 $n=trim($_POST['nombre']??'');$a=trim($_POST['apellidos']??'');$c=trim($_POST['correo']??'');$p=$_POST['contrasena']??'';$fn=$_POST['fecha_nacimiento']??null; if(!$n||!$a||!$c||strlen($p)<6||!$fn){redirect('auth','register',['error'=>'Completa todos los campos']);}
$uM=new User(); try{ $id=$uM->create(['nombre'=>$n,'apellidos'=>$a,'correo'=>$c,'password'=>password_hash($p,PASSWORD_BCRYPT),'rol'=>'estudiante']); require_once __DIR__.'/../models/EstudianteDetalle.php'; (new EstudianteDetalle())->upsert($id,null,null,$fn,'M',1.4); redirect('auth','login',['ok'=>'Cuenta creada, inicia sesión']); }catch(Exception $e){ redirect('auth','register',['error'=>'El correo ya existe']); } }
public function logout(){ if(session_status()===PHP_SESSION_NONE) session_start(); $_SESSION=[]; session_destroy(); if(function_exists('session_regenerate_id')) session_regenerate_id(true); nocache_headers_safe(); redirect('auth','login'); }
}
