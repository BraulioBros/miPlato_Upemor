<?php
/**
 * FORMULARIO DE COMIDA (CREATE/EDIT)
 * 
 * Formulario para crear o editar una comida.
 * 
 * Variables disponibles:
 * - $c - Array con datos de comida (solo si está editando)
 * - $nutrientes - Array de todos los nutrientes disponibles
 * 
 * Si $c está vacío -> Crear nueva comida
 * Si $c tiene datos -> Editar comida existente
 * 
 * Validaciones:
 * - Nombre: Requerido, mínimo 3 caracteres
 * - Descripción: Requerido
 * - Calorías: Requerido, valor numérico >= 0
 */
?>

<a class='btn' href='index.php?controller=admin&action=comidas'>← Volver</a>

<!-- Título dinámico según sea crear o editar -->
<h2><?= isset($c) ? 'Editar' : 'Nueva' ?> comida</h2>

<form method='POST' action='index.php?controller=admin&action=comidaSave' onsubmit="return validarFormularioComida()">

    <!-- Si estamos editando, enviamos el ID de la comida -->
    <?php if (isset($c)): ?>
        <input type='hidden' name='id_comida' value='<?= $c['id_comida'] ?>'>
    <?php endif; ?>

    <!-- Campo: Nombre de la comida -->
    <label class='form-label'>Nombre <span style="color: #dc2626;">*</span></label>
    <input class='form-control' name='nombre'
           value='<?= htmlspecialchars($c['nombre'] ?? '') ?>' 
           placeholder="Ej: Arroz blanco"
           minlength="3"
           maxlength="100"
           required>
    <small style="color: #666;">Mínimo 3 caracteres</small>

    <!-- Campo: Descripción (requerido) -->
    <label class='form-label'>Descripción <span style="color: #dc2626;">*</span></label>
    <textarea class='form-control' name='descripcion' rows='3'
              placeholder="Ej: Arroz cocido al vapor, sin sal"
              minlength="3"
              maxlength="500"
              required><?= htmlspecialchars($c['descripcion'] ?? '') ?></textarea>
    <small style="color: #666;">Mínimo 3 caracteres</small>

    <!-- Campo: Calorías por 100 gramos -->
    <label class='form-label'>Kcal por 100g <span style="color: #dc2626;">*</span></label>
    <input class='form-control' type='number' step='0.01' min='0' max='999'
           name='calorias_por_100g'
           value='<?= htmlspecialchars($c['calorias_por_100g'] ?? '0') ?>'
           placeholder="Ej: 130"
           required>
    <small style="color: #666;">Valor numérico mayor a 0</small>

    <!-- Campo: Nutriente principal (REQUERIDO) -->
    <label class='form-label'>Nutriente <span style="color: #dc2626;">*</span></label>
    <select class='form-control' name='id_nutriente' required>
        <option value=''>-- Selecciona un nutriente --</option>
        <?php foreach ($nutrientes as $n): ?>
            <option value='<?= $n['id_nutriente'] ?>'
                <?= isset($c) && $c['id_nutriente'] == $n['id_nutriente'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($n['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <!-- Botón enviar -->
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
