<?php
class Controller{
protected function view($file,$data=[]){$view=$file;extract($data);include __DIR__.'/../views/layout.php';}
protected function partial($file,$data=[]){extract($data);include __DIR__.'/../views/'.$file.'.php';}
protected function requireLogin(){if(session_status()===PHP_SESSION_NONE)session_start();if(empty($_SESSION['user'])){header('Location: index.php?controller=auth&action=login');exit;}nocache_headers_safe();}
protected function requireRole($r){$this->requireLogin();$u=$_SESSION['user'];$r=(array)$r;if(!in_array($u['rol'],$r)){header('Location: index.php?controller=auth&action=login');exit;}}
}
