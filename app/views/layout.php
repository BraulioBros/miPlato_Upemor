<?php if(session_status()===PHP_SESSION_NONE)session_start();?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MiPlato Upemor</title><link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script></head><body>
<header class="topbar"><div class="brand">MiPlato Upemor</div><div class="nav-actions"><?php if(!empty($_SESSION['user'])): ?>
<span class="nav-user"><?= htmlspecialchars($_SESSION['user']['nombre']) ?> (<?= $_SESSION['user']['rol'] ?>)</span>
<a class="btn" href="index.php?controller=auth&action=logout">Cerrar sesión</a><?php endif; ?></div></header>
<div class="app"><?php if(!empty($_SESSION['user'])): ?><aside class="sidebar">
<?php if($_SESSION['user']['rol']==='admin'): ?>
<a href="index.php?controller=admin&action=dashboard" class="slink">Dashboard</a><a href="index.php?controller=admin&action=usuarios" class="slink">Usuarios</a><a href="index.php?controller=admin&action=nutriologos" class="slink">Nutriólogos</a><a href="index.php?controller=admin&action=nutrientes" class="slink">Nutrientes</a><a href="index.php?controller=admin&action=comidas" class="slink">Comidas</a>
<?php elseif($_SESSION['user']['rol']==='nutriologo'): ?>
<a href="index.php?controller=nutriologo&action=dashboard" class="slink">Dashboard</a><a href="index.php?controller=nutriologo&action=estudiantes" class="slink">Estudiantes</a><a href="index.php?controller=nutriologo&action=nutrientes" class="slink">Nutrientes</a><a href="index.php?controller=nutriologo&action=comidas" class="slink">Comidas</a>
<?php else: ?>
<a href="index.php?controller=estudiante&action=dashboard" class="slink">Inicio</a><a href="index.php?controller=estudiante&action=consumoAdd" class="slink">Registrar consumo</a><a href="index.php?controller=estudiante&action=reporteDiario" class="slink">Reporte Diario</a><a href="index.php?controller=estudiante&action=reporteSemanal" class="slink">Reporte Semanal</a>
<?php endif; ?></aside><?php endif; ?>
<main class="container"><?php if(!empty($_GET['error'])): ?><div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?><?php if(!empty($_GET['ok'])): ?><div class="alert ok"><?= htmlspecialchars($_GET['ok']) ?></div><?php endif; ?><section><?php $this->partial($view, get_defined_vars()); ?></section></main></div></body></html>
