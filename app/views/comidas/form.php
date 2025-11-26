<?php<div class="form-group">
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
