<?php
/**
 * FORMULARIO DE COMIDAS (NUTRIÓLOGO)
 * 
 * Formulario para crear o editar comidas del sistema.
 * Los nutriólogos pueden gestionar su propia base de comidas.
 * 
 * Variables disponibles:
 * - $c - Datos de comida (null si es creación)
 *   Campos: id_comida, nombre, descripcion, calorias_por_100g, id_nutriente
 * - $nutrientes - Array con nutrientes disponibles
 *   Campos: id_nutriente, nombre
 * 
 * Campos del formulario:
 * - nombre - Nombre de la comida (REQUERIDO, mín 3 caracteres)
 * - descripcion - Descripción de la comida (REQUERIDO, mín 3 caracteres)
 * - calorias_por_100g - Calorías por 100g (decimal, REQUERIDO, >= 0)
 * - id_nutriente - Nutriente principal asociado (opcional)
 * 
 * Acción: Guarda comida nueva o edita existente (comidaSave)
 */
?>

<a class='btn' href='index.php?controller=nutriologo&action=comidas'>← Volver</a>
<h2><?= isset($c)?'Editar':'Nueva' ?> comida</h2>

<!-- ========== FORMULARIO DE COMIDA ========== -->
<form method='POST' action='index.php?controller=nutriologo&action=comidaSave' onsubmit="return validarFormularioComida()">
    <?php if(isset($c)): ?>
        <input type='hidden' name='id_comida' value='<?= $c['id_comida'] ?>'>
    <?php endif; ?>
    
    <label class='form-label'>Nombre <span style="color: #dc2626;">*</span></label>
    <input class='form-control' name='nombre' value='<?= htmlspecialchars($c['nombre'] ?? '') ?>' 
           placeholder="Ej: Arroz blanco"
           minlength="3"
           maxlength="100"
           required>
    <small style="color: #666;">Mínimo 3 caracteres</small>
    
    <label class='form-label'>Descripción <span style="color: #dc2626;">*</span></label>
    <textarea class='form-control' name='descripcion' rows='3'
              placeholder="Ej: Arroz cocido al vapor, sin sal"
              minlength="3"
              maxlength="500"
              required><?= htmlspecialchars($c['descripcion'] ?? '') ?></textarea>
    <small style="color: #666;">Mínimo 3 caracteres</small>
    
    <label class='form-label'>Kcal por 100g <span style="color: #dc2626;">*</span></label>
    <input class='form-control' type='number' step='0.01' min='0' max='999'
           name='calorias_por_100g' value='<?= htmlspecialchars($c['calorias_por_100g'] ?? '0') ?>'
           placeholder="Ej: 130"
           required>
    <small style="color: #666;">Valor numérico mayor a 0</small>
    
    <label class='form-label'>Nutriente <span style="color: #dc2626;">*</span></label>
    <select class='form-control' name='id_nutriente' required>
        <option value=''>-- Selecciona un nutriente --</option>
        <?php foreach($nutrientes as $n): ?>
            <option value='<?= $n['id_nutriente'] ?>' <?= isset($c) && $c['id_nutriente'] == $n['id_nutriente'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($n['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <button class='btn primary mt-12' type='submit'>Guardar Comida</button>
</form>

<script>
/**
 * Valida el formulario de comida antes de enviar
 */
function validarFormularioComida() {
    const nombre = document.querySelector('input[name="nombre"]').value.trim();
    const descripcion = document.querySelector('textarea[name="descripcion"]').value.trim();
    const calorias = document.querySelector('input[name="calorias_por_100g"]').value.trim();
    const nutriente = document.querySelector('select[name="id_nutriente"]').value;
    
    // Validar nombre
    if (!nombre || nombre.length < 3) {
        showErrorAlert('⚠️ El nombre debe tener al menos 3 caracteres', 6000);
        return false;
    }
    
    // Validar descripción
    if (!descripcion || descripcion.length < 3) {
        showErrorAlert('⚠️ La descripción debe tener al menos 3 caracteres', 6000);
        return false;
    }
    
    // Validar calorías
    if (!calorias || parseFloat(calorias) < 0) {
        showErrorAlert('⚠️ Las calorías deben ser un valor mayor a 0', 6000);
        return false;
    }
    
    // Validar nutriente
    if (!nutriente) {
        showErrorAlert('⚠️ Debes seleccionar un nutriente', 6000);
        return false;
    }
    
    return true;
}
</script>
