<?php
if(session_status()===PHP_SESSION_NONE)session_start();
require __DIR__.'/app/config/config.php';
require __DIR__.'/app/helpers/url.php';
require __DIR__.'/app/core/Controller.php';
require __DIR__.'/app/core/Model.php';
require __DIR__.'/app/core/Database.php';
require __DIR__.'/app/core/Router.php';
nocache_headers_safe();
$c=$_GET['controller']??'auth'; $a=$_GET['action']??'login';
if($c==='auth' && in_array($a,['login','register'])){
  if(!empty($_SESSION['user'])){ $_SESSION=[]; if(ini_get('session.use_cookies')){ $p=session_get_cookie_params(); setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']); } session_destroy(); session_start(); session_regenerate_id(true); }
}
Router::dispatch();
