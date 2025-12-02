<?php
/**
 * VISTA DE REGISTRO - REDIRECCIONAMIENTO
 * 
 * Esta vista ha sido integrada en login.php como un tab.
 * El registro ahora se maneja desde la misma card que el login.
 * Se redirige automáticamente a la página de login.
 */

// Redirigir a la página de login (que contiene registro integrado)
header('Location: ' . $_SERVER['HTTP_REFERER'] ?: 'index.php?controller=auth&action=login');
exit();
?>

