<?php
/**
 * FORMULARIO DE COMIDAS (VISTA PÚBLICA)
 * 
 * Fragmento de formulario para seleccionar nutriente principal de una comida.
 * Se usa en formularios de creación/edición de comidas.
 * 
 * Variables disponibles:
 * - $nutrientes - Array con nutrientes disponibles
 *   Campos: id, nombre
 * - $comida - Datos de comida (null si es creación)
 *   Campo: id_nutriente
 * 
 * Acción: Selecciona nutriente principal para la comida
 */
?>

<!-- ========== SELECTOR DE NUTRIENTE ========== -->
<div class="form-group">
    <label for="id_nutriente">Nutriente principal</label>
    <select name="id_nutriente" id="id_nutriente" class="form-control" required>
        <option value="">-- Selecciona un nutriente --</option>

        <?php foreach ($nutrientes as $n): ?>
            <option value="<?= $n['id'] ?>"
                <?= isset($comida) && $comida['id_nutriente'] == $n['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($n['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
