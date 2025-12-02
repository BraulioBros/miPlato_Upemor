<?php 
/**
 * LAYOUT PRINCIPAL
 * 
 * Template principal que envuelve todas las vistas de la aplicación.
 * Contiene:
 * - Doctype y metadatos HTML
 * - Header con navegación
 * - Sidebar con menú según el rol
 * - Main container para el contenido
 * - Alertas de error/éxito
 */

// Inicia sesión si no está activa
if(session_status()===PHP_SESSION_NONE)session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiPlato Upemor</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= asset('js/alerts.js') ?>"></script>
</head>
<body>
    <!-- ========== HEADER PRINCIPAL ========== -->
    <header class="topbar">
        <div class="brand">MiPlato Upemor</div>
        <div class="nav-actions">
            <?php if(!empty($_SESSION['user'])): ?>
                <!-- Muestra nombre y rol del usuario autenticado -->
                <span class="nav-user"><?= htmlspecialchars($_SESSION['user']['nombre']) ?> (<?= $_SESSION['user']['rol'] ?>)</span>
                <!-- Link para cerrar sesión -->
                <a class="btn" href="index.php?controller=auth&action=logout">Cerrar sesión</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="app">
        <?php if(!empty($_SESSION['user'])): ?>
            <!-- ========== SIDEBAR DINÁMICO SEGÚN ROL ========== -->
            <aside class="sidebar">
                <?php if($_SESSION['user']['rol']==='admin'): ?>
                    <!-- MENÚ ADMIN: Gestión completa -->
                    <a href="index.php?controller=admin&action=dashboard" class="slink">Dashboard</a>
                    <a href="index.php?controller=admin&action=usuarios" class="slink">Usuarios</a>
                    <a href="index.php?controller=admin&action=nutriologos" class="slink">Nutriólogos</a>
                    <a href="index.php?controller=admin&action=nutrientes" class="slink">Nutrientes</a>
                    <a href="index.php?controller=admin&action=comidas" class="slink">Comidas</a>

                <?php elseif($_SESSION['user']['rol']==='nutriologo'): ?>
                    <!-- MENÚ NUTRIOLOGO: Gestión de datos -->
                    <a href="index.php?controller=nutriologo&action=dashboard" class="slink">Dashboard</a>
                    <a href="index.php?controller=nutriologo&action=estudiantes" class="slink">Estudiantes</a>
                    <a href="index.php?controller=nutriologo&action=nutrientes" class="slink">Nutrientes</a>
                    <a href="index.php?controller=nutriologo&action=comidas" class="slink">Comidas</a>

                <?php else: ?>
                    <!-- MENÚ ESTUDIANTE: Consumo y reportes -->
                    <a href="index.php?controller=estudiante&action=dashboard" class="slink">Inicio</a>
                    <a href="index.php?controller=estudiante&action=consumoAdd" class="slink">Registrar consumo</a>
                    <a href="index.php?controller=estudiante&action=reporteDiario" class="slink">Reporte Diario</a>
                    <a href="index.php?controller=estudiante&action=reporteSemanal" class="slink">Reporte Semanal</a>
                <?php endif; ?>
            </aside>
        <?php endif; ?>

        <!-- ========== CONTENIDO PRINCIPAL ========== -->
        <main class="container">
            <!-- Alertas de error (GET['error']) -->
            <?php if(!empty($_GET['error'])): ?>
                <div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const errorMsg = <?= json_encode($_GET['error']) ?>;
                        setTimeout(() => {
                            showErrorAlert(errorMsg, 6000);
                        }, 100);
                    });
                </script>
            <?php endif; ?>

            <!-- Alertas de éxito (GET['ok']) -->
            <?php if(!empty($_GET['ok'])): ?>
                <div class="alert ok"><?= htmlspecialchars($_GET['ok']) ?></div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const successMsg = <?= json_encode($_GET['ok']) ?>;
                        setTimeout(() => {
                            showSuccessAlert(successMsg, 5000);
                        }, 100);
                    });
                </script>
            <?php endif; ?>

            <!-- Vista específica (se carga dinámicamente) -->
            <section><?php $this->partial($view, get_defined_vars()); ?></section>
        </main>
    </div>
</body>
</html>
