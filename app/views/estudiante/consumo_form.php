<?php
/**
 * FORMULARIO DE REGISTRO DE CONSUMO (ESTUDIANTE)
 * 
 * Formulario para que el estudiante registre su consumo de alimentos.
 * Calcula automáticamente calorías basado en cantidad en gramos.
 * 
 * Variables disponibles:
 * - $comidas - Array con comidas disponibles
 *   Campos: id_comida, nombre, calorias_por_100g
 * 
 * Cálculo de calorías:
 * Calorías = (cantidad_gramos * calorias_por_100g) / 100
 * 
 * Acción: Guarda consumo en base de datos (consumoSave)
 */
?>

<a class='btn' href='index.php?controller=estudiante&action=dashboard'>← Volver</a>
<h2>Registrar consumo</h2>

<!-- ========== FORMULARIO DE CONSUMO ========== -->
<form method='POST' action='index.php?controller=estudiante&action=consumoSave'>
<input type='hidden' name='fecha' value='<?= date('Y-m-d') ?>'>
<label class='form-label'>Comida</label><select class='form-control' name='comida_id'><?php foreach($comidas as $c): ?><option value='<?= $c['id_comida'] ?>'><?= htmlspecialchars($c['nombre']) ?> (<?= number_format($c['calorias_por_100g'],2) ?> kcal/100g)</option><?php endforeach; ?></select>
<label class='form-label'>Cantidad (gramos)</label><input class='form-control' type='number' name='cantidad_gramos' min='1' required>
<button class='btn primary mt-12' type='submit'>Guardar</button></form>
