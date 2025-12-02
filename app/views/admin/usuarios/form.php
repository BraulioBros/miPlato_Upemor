<?php
/**
 * FORMULARIO DE USUARIOS (ADMIN)
 * 
 * Formulario para crear o editar usuarios del sistema.
 * Los usuarios pueden tener dos roles: Admin o Estudiante.
 * 
 * Variables disponibles:
 * - $u - Datos del usuario (null si es creación)
 *   Campos: id, nombre, apellidos, correo, rol, password (solo creación)
 * 
 * Roles disponibles:
 * - admin: Acceso total al sistema, gestión de usuarios y datos
 * - estudiante: Acceso a datos personales y registro de consumo
 * 
 * Comportamiento:
 * - Crear: Solicita contraseña (mínimo 6 caracteres)
 * - Editar: No muestra campo de contraseña
 * 
 * Acción: Guarda nuevo usuario o edita existente (usuarioSave)
 */
?>

<a class='btn' href='index.php?controller=admin&action=usuarios'>← Volver</a>
<h2><?= isset($u)?'Editar':'Nuevo' ?> usuario</h2>

<!-- ========== FORMULARIO DE USUARIO ========== -->
<form method='POST' action='index.php?controller=admin&action=usuarioSave' autocomplete='off'>
    
    <!-- Campo oculto: ID (solo en edición) -->
    <?php if(isset($u)): ?>
        <input type='hidden' name='id' value='<?= $u['id'] ?>'>
    <?php endif; ?>
    
    <!-- Campo: Nombre del Usuario -->
    <label class='form-label'>Nombre</label>
    <input class='form-control' name='nombre' value='<?= $u['nombre'] ?? '' ?>' required>
    
    <!-- Campo: Apellidos del Usuario -->
    <label class='form-label'>Apellidos</label>
    <input class='form-control' name='apellidos' value='<?= $u['apellidos'] ?? '' ?>' required>
    
    <!-- Campo: Correo Electrónico (login) -->
    <label class='form-label'>Correo</label>
    <input class='form-control' type='email' name='correo' value='<?= $u['correo'] ?? '' ?>' required autocomplete='off'>
    
    <!-- Campo: Rol del Usuario (define permisos en el sistema) -->
    <label class='form-label'>Rol</label>
    <select class='form-control' name='rol'>
        <option value='admin' <?= (isset($u)&&$u['rol']=='admin')?'selected':'' ?>>
            Admin
        </option>
        <option value='estudiante' <?= (isset($u)&&$u['rol']=='estudiante')?'selected':'' ?>>
            Estudiante
        </option>
    </select>
    
    <!-- Campo: Contraseña (solo en creación, mínimo 6 caracteres) -->
    <?php if(!isset($u)): ?>
        <label class='form-label'>Contraseña</label>
        <input class='form-control' type='password' name='password' minlength='6' required autocomplete='new-password'>
    <?php endif; ?>
    
    <!-- Botón: Guardar cambios -->
    <button class='btn primary mt-12' type='submit'>Guardar</button>
</form>
