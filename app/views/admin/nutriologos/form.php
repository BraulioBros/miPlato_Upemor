<?php
/**
 * FORMULARIO DE NUTRIÓLOGOS (ADMIN)
 * 
 * Formulario para crear o editar nutriólogos del sistema.
 * Los nutriólogos son profesionales que tienen acceso para gestionar
 * información de estudiantes y planes alimentarios.
 * 
 * Variables disponibles:
 * - $u - Datos del nutriólogo (null si es creación)
 *   Campos: id, nombre, apellidos, correo, password (solo creación)
 * 
 * Comportamiento:
 * - Crear: Solicita contraseña (mínimo 6 caracteres)
 * - Editar: No muestra campo de contraseña (se cambia desde otro formulario)
 * 
 * Acción: Guarda nuevo nutriólogo o edita existente (nutriologoSave)
 */
?>

<a class='btn' href='index.php?controller=admin&action=nutriologos'>← Volver</a>
<h2><?= isset($u)?'Editar':'Nuevo' ?> nutriólogo</h2>

<!-- ========== FORMULARIO DE NUTRIÓLOGO ========== -->
<form method='POST' action='index.php?controller=admin&action=nutriologoSave' autocomplete='off'>
    
    <!-- Campo oculto: ID (solo en edición) -->
    <?php if(isset($u)): ?>
        <input type='hidden' name='id' value='<?= $u['id'] ?>'>
    <?php endif; ?>
    
    <!-- Campo: Nombre del Nutriólogo -->
    <label class='form-label'>Nombre</label>
    <input class='form-control' name='nombre' value='<?= $u['nombre'] ?? '' ?>' required>
    
    <!-- Campo: Apellidos del Nutriólogo -->
    <label class='form-label'>Apellidos</label>
    <input class='form-control' name='apellidos' value='<?= $u['apellidos'] ?? '' ?>' required>
    
    <!-- Campo: Correo Electrónico (login) -->
    <label class='form-label'>Correo</label>
    <input class='form-control' type='email' name='correo' value='<?= $u['correo'] ?? '' ?>' required autocomplete='off'>
    
    <!-- Campo: Contraseña (solo en creación, mínimo 6 caracteres) -->
    <?php if(!isset($u)): ?>
        <label class='form-label'>Contraseña</label>
        <input class='form-control' type='password' name='password' minlength='6' required autocomplete='new-password'>
    <?php endif; ?>
    
    <!-- Botón: Guardar cambios -->
    <button class='btn primary mt-12' type='submit'>Guardar</button>
</form>
