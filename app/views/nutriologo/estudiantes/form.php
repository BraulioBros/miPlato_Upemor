<a class='btn' href='index.php?controller=nutriologo&action=estudiantes'>← Volver</a><h2>Datos del estudiante #<?= htmlspecialchars($id) ?></h2>
<form method='POST' action='index.php?controller=nutriologo&action=estudianteSave'><input type='hidden' name='usuario_id' value='<?= htmlspecialchars($id) ?>'>
<label class='form-label'>Peso (kg)</label><input class='form-control' type='number' step='0.1' name='peso' value='<?= $det['peso'] ?? '' ?>' required>
<label class='form-label'>Altura (cm)</label><input class='form-control' type='number' step='0.1' name='altura' value='<?= $det['altura'] ?? '' ?>' required>
<label class='form-label'>Fecha de nacimiento</label><input class='form-control' type='date' name='fecha_nacimiento' value='<?= $det['fecha_nacimiento'] ?? '2005-06-12' ?>' min='1980-01-01' max='2009-12-31' required>
<label class='form-label'>Sexo</label><?php $sexo=$det['sexo'] ?? 'M'; ?><select class='form-control' name='sexo'><option value='M' <?= ($sexo=='M')?'selected':'' ?>>Masculino</option><option value='F' <?= ($sexo=='F')?'selected':'' ?>>Femenino</option></select>
<label class='form-label'>Actividad (1.2-1.9)</label><input class='form-control' type='number' step='0.1' min='1.2' max='1.9' name='actividad' value='<?= $det['actividad'] ?? '1.4' ?>' required>
<button class='btn primary' type='submit'>Guardar</button></form>
