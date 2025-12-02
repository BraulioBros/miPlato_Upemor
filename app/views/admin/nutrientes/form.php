<?php
/**
 * FORMULARIO DE NUTRIENTES (ADMIN)
 * 
 * Formulario para crear o editar nutrientes del sistema.
 * 
 * Variables disponibles:
 * - $n - Datos del nutriente (null si es creación)
 *   Campos: id_nutriente, nombre, calorias_por_gramo, unidad_medida, tipo
 * 
 * Tipos de nutrientes:
 * - macronutriente (carbohidratos, proteínas, grasas)
 * - micronutriente (minerales, vitaminas)
 * - vitamina
 * - mineral
 * - fibra
 * 
 * Acción: Guarda nuevo nutriente o edita existente (nutrienteSave)
 */
?>

<a class='btn' href='index.php?controller=admin&action=nutrientes'>← Volver</a>
<h2><?= isset($n)?'Editar':'Nuevo' ?> nutriente</h2>

<!-- ========== FORMULARIO DE NUTRIENTE ========== -->
<form method='POST' action='index.php?controller=admin&action=nutrienteSave'>
    <!-- Campo oculto: ID (solo en edición) -->
    <?php if(isset($n)): ?>
        <input type='hidden' name='id_nutriente' value='<?= $n['id_nutriente'] ?>'>
    <?php endif; ?>
    
    <!-- Campo: Nombre del Nutriente -->
    <label class='form-label'>Nombre</label>
    <input class='form-control' name='nombre' value='<?= $n['nombre'] ?? '' ?>' required>
    
    <!-- Campo: Calorías por Gramo (usado en cálculos nutricionales) -->
    <label class='form-label'>Calorías por gramo</label>
    <input class='form-control' type='number' step='0.01' name='calorias_por_gramo' 
           value='<?= isset($n) ? $n['calorias_por_gramo'] : '' ?>' required>
    
    <!-- Campo: Unidad de Medida (gramo, mg, microgramo, etc.) -->
    <label class='form-label'>Unidad de medida</label>
    <input class='form-control' name='unidad_medida' value='<?= $n['unidad_medida'] ?? '' ?>' required>
    
    <!-- Campo: Tipo de Nutriente (clasificación para análisis nutricional) -->
    <label class='form-label'>Tipo</label>
    <select class='form-control' name='tipo' required>
        <option value="">-- Selecciona un tipo --</option>
        <option value='macronutriente' <?= (isset($n) && $n['tipo'] === 'macronutriente') ? 'selected' : '' ?>>
            Macronutriente
        </option>
        <option value='micronutriente' <?= (isset($n) && $n['tipo'] === 'micronutriente') ? 'selected' : '' ?>>
            Micronutriente
        </option>
        <option value='vitamina' <?= (isset($n) && $n['tipo'] === 'vitamina') ? 'selected' : '' ?>>
            Vitamina
        </option>
        <option value='mineral' <?= (isset($n) && $n['tipo'] === 'mineral') ? 'selected' : '' ?>>
            Mineral
        </option>
        <option value='fibra' <?= (isset($n) && $n['tipo'] === 'fibra') ? 'selected' : '' ?>>
            Fibra
        </option>
    </select>
    
    <!-- Botón: Guardar cambios -->
    <button class='btn primary mt-12' type='submit'>Guardar</button>
</form>
