<?php
/**
 * FORMULARIO DE DATOS ANTROPOMÉTRICOS (NUTRIÓLOGO)
 * 
 * Formulario para que el nutriólogo edite/registre datos del estudiante.
 * Estos datos son usados para cálculos de TMB y objetivo calórico.
 * 
 * Variables disponibles:
 * - $id - ID del usuario estudiante
 * - $det - Datos antropométricos actuales
 *   Campos: peso, altura, fecha_nacimiento, sexo, actividad
 * 
 * Campos del formulario:
 * - peso - Peso en kg (decimal, obligatorio)
 * - altura - Altura en cm (decimal, obligatorio)
 * - fecha_nacimiento - Fecha nacimiento (date, obligatorio)
 * - sexo - Sexo: M (Masculino) o F (Femenino) (select, obligatorio)
 * - actividad - Factor de actividad física (1.2 a 1.9, obligatorio)
 *   1.2 = Sedentario
 *   1.375 = Ligera actividad
 *   1.55 = Actividad moderada
 *   1.725 = Actividad alta
 *   1.9 = Muy alta actividad
 * 
 * Cálculo de TMB:
 * Se usa fórmula de Harris-Benedict con estos datos
 * 
 * Acción: Guarda datos en EstudianteDetalle (estudianteSave)
 */
?>

<a class='btn' href='index.php?controller=nutriologo&action=estudiantes'>← Volver</a>
<h2>Datos del estudiante #<?= htmlspecialchars($id) ?></h2>

<!-- ========== FORMULARIO DE DATOS ANTROPOMÉTRICOS ========== -->
<form method='POST' action='index.php?controller=nutriologo&action=estudianteSave'><input type='hidden' name='usuario_id' value='<?= htmlspecialchars($id) ?>'>
<label class='form-label'>Peso (kg)</label><input class='form-control' type='number' step='0.1' name='peso' value='<?= $det['peso'] ?? '' ?>' required>
<label class='form-label'>Altura (cm)</label><input class='form-control' type='number' step='0.1' name='altura' value='<?= $det['altura'] ?? '' ?>' required>
<label class='form-label'>Fecha de nacimiento</label><input class='form-control' type='date' name='fecha_nacimiento' value='<?= $det['fecha_nacimiento'] ?? '2005-06-12' ?>' min='1980-01-01' max='2009-12-31' required>
<label class='form-label'>Sexo</label><?php $sexo=$det['sexo'] ?? 'M'; ?><select class='form-control' name='sexo'><option value='M' <?= ($sexo=='M')?'selected':'' ?>>Masculino</option><option value='F' <?= ($sexo=='F')?'selected':'' ?>>Femenino</option></select>
<label class='form-label'>Actividad (1.2-1.9)</label><input class='form-control' type='number' step='0.1' min='1.2' max='1.9' name='actividad' value='<?= $det['actividad'] ?? '1.4' ?>' required>
<button class='btn primary' type='submit'>Guardar</button></form>
