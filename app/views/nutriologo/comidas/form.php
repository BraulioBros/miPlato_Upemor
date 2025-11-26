<a class='btn' href='index.php?controller=nutriologo&action=comidas'>← Volver</a><h2><?= isset($c)?'Editar':'Nueva' ?> comida</h2>
<form method='POST' action='index.php?controller=nutriologo&action=comidaSave'><?php if(isset($c)): ?><input type='hidden' name='id_comida' value='<?= $c['id_comida'] ?>'><?php endif; ?>
<label class='form-label'>Nombre</label><input class='form-control' name='nombre' value='<?= $c['nombre'] ?? '' ?>' required>
<label class='form-label'>Descripción</label><textarea class='form-control' name='descripcion' rows='3'><?= $c['descripcion'] ?? '' ?></textarea>
<label class='form-label'>Kcal por 100g</label><input class='form-control' type='number' step='0.1' name='calorias_por_100g' value='<?= $c['calorias_por_100g'] ?? '0' ?>' required>
<label class='form-label'>Nutriente</label><select class='form-control' name='id_nutriente'><option value=''>-- Sin nutriente --</option><?php foreach($nutrientes as $n): ?><option value='<?= $n['id_nutriente'] ?>' <?= isset($c) && $c['id_nutriente'] == $n['id_nutriente'] ? 'selected' : '' ?>><?= htmlspecialchars($n['nombre']) ?></option><?php endforeach; ?></select>
<button class='btn primary mt-12' type='submit'>Guardar</button></form>
