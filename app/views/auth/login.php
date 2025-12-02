<?php
/**
 * VISTA DE AUTENTICACIÓN UNIFICADA - DISEÑO MODERNO
 * 
 * Card única con alternancia entre login y registro.
 * Animación de degradado en el fondo.
 * 
 * Características:
 * - Una sola card con tabs para cambiar entre login/registro
 * - Fondo con animación de degradado multicolor
 * - Sin elementos verdes del logo
 * - Campos se muestran/ocultan según la pestaña seleccionada
 * - Transiciones suaves entre formularios
 * 
 * Controlador: AuthController::login() / AuthController::register()
 * Acciones POST: AuthController::doLogin() / AuthController::doRegister()
 */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticación - MiPlato Upemor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/ce1db38258.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/Estancia/public/css/auth.css">
    <script src="/Estancia/public/js/alerts.js"></script>
</head>
<body class="auth-page">
    <!-- ========== CONTENEDOR PRINCIPAL ========== -->
    <div class="auth-container">
        
        <!-- ========== ENCABEZADO ========== -->
        <div class="auth-header">
            <h1>MiPlato Upemor</h1>
            <p>Sistema de Gestión Nutricional</p>
        </div>

        <!-- ========== MENSAJES DE ERROR/ÉXITO ========== -->
        <?php if(!empty($_GET['error'])): ?>
            <div class="alert error" style="margin: 20px 0; animation: slideDownAlert 0.4s ease;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const errorMsg = <?= json_encode($_GET['error']) ?>;
                    showErrorAlert(errorMsg, 6000);
                });
            </script>
        <?php endif; ?>

        <?php if(!empty($_GET['ok'])): ?>
            <div class="alert ok" style="margin: 20px 0; animation: slideDownAlert 0.4s ease;">
                <?= htmlspecialchars($_GET['ok']) ?>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const successMsg = <?= json_encode($_GET['ok']) ?>;
                    showSuccessAlert(successMsg, 5000);
                });
            </script>
        <?php endif; ?>

        <!-- ========== TABS DE NAVEGACIÓN ========== -->
        <div class="auth-tabs">
            <button class="tab-button active" data-tab="login">
                <i class="fas fa-sign-in-alt"></i> Iniciar sesión
            </button>
            <button class="tab-button" data-tab="register">
                <i class="fas fa-user-plus"></i> Registrarse
            </button>
        </div>

        <!-- ========== CUERPO CON FORMULARIOS ========== -->
        <div class="auth-body">

            <!-- ========== TAB: LOGIN ========== -->
            <div class="form-tab active" id="login-form">
                <h2>Bienvenido</h2>
                <p>Inicia sesión para acceder a tu cuenta</p>

                <form method="POST" action="index.php?controller=auth&action=doLogin" autocomplete="off">
                    
                    <!-- Campo email -->
                    <div class="form-group">
                        <label for="login-correo">
                            <i class="fas fa-envelope"></i> Correo Electrónico
                        </label>
                        <input 
                            type="email" 
                            id="login-correo" 
                            name="correo" 
                            placeholder="tu@ejemplo.com" 
                            required 
                            autocomplete="off"
                        >
                    </div>

                    <!-- Campo contraseña -->
                    <div class="form-group">
                        <label for="login-contrasena">
                            <i class="fas fa-lock"></i> Contraseña
                        </label>
                        <input 
                            type="password" 
                            id="login-contrasena" 
                            name="contrasena" 
                            placeholder="Tu contraseña" 
                            required 
                            autocomplete="current-password"
                        >
                    </div>

                    <!-- Botón de envío -->
                    <button type="submit" class="btn-auth">
                        <i class="fas fa-arrow-right"></i> Iniciar sesión
                    </button>
                </form>
            </div>

            <!-- ========== TAB: REGISTRO ========== -->
            <div class="form-tab" id="register-form">
                <h2>Crear cuenta</h2>
                <p>Regístrate para comenzar a gestionar tu nutrición</p>

                <form method="POST" action="index.php?controller=auth&action=doRegister" autocomplete="off">
                    
                    <!-- Fila: Nombre y Apellidos -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="reg-nombre">
                                <i class="fas fa-user"></i> Nombre
                            </label>
                            <input 
                                type="text" 
                                id="reg-nombre" 
                                name="nombre" 
                                placeholder="Tu nombre" 
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="reg-apellidos">
                                <i class="fas fa-user"></i> Apellidos
                            </label>
                            <input 
                                type="text" 
                                id="reg-apellidos" 
                                name="apellidos" 
                                placeholder="Tus apellidos" 
                                required
                            >
                        </div>
                    </div>

                    <!-- Campo de email -->
                    <div class="form-group">
                        <label for="reg-correo">
                            <i class="fas fa-envelope"></i> Correo Electrónico
                        </label>
                        <input 
                            type="email" 
                            id="reg-correo" 
                            name="correo" 
                            placeholder="tu@ejemplo.com" 
                            required 
                            autocomplete="off"
                        >
                    </div>

                    <!-- Campo de fecha de nacimiento -->
                    <div class="form-group">
                        <label for="reg-fecha">
                            <i class="fas fa-calendar"></i> Fecha de Nacimiento
                        </label>
                        <input 
                            type="date" 
                            id="reg-fecha" 
                            name="fecha_nacimiento" 
                            value="2005-06-12" 
                            min="1980-01-01" 
                            max="2009-12-31" 
                            required
                        >
                        <small>Debes tener entre 15 y 44 años</small>
                    </div>

                    <!-- Campo de contraseña -->
                    <div class="form-group">
                        <label for="reg-contrasena">
                            <i class="fas fa-lock"></i> Contraseña
                        </label>
                        <input 
                            type="password" 
                            id="reg-contrasena" 
                            name="contrasena" 
                            placeholder="Mínimo 6 caracteres" 
                            minlength="6" 
                            required 
                            autocomplete="new-password"
                        >
                        <small>Usa una contraseña fuerte con letras y números</small>
                    </div>

                    <!-- Botón de envío -->
                    <button type="submit" class="btn-auth">
                        <i class="fas fa-arrow-right"></i> Registrarme
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- ========== SCRIPT PARA CAMBIAR TABS ========== -->
    <script>
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Remover clase active de todos los botones
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Remover clase active de todos los formularios
                document.querySelectorAll('.form-tab').forEach(form => {
                    form.classList.remove('active');
                });
                
                // Agregar clase active al botón clickeado
                this.classList.add('active');
                
                // Agregar clase active al formulario correspondiente
                document.getElementById(tabName + '-form').classList.add('active');
            });
        });
    </script>
</body>
</html>
