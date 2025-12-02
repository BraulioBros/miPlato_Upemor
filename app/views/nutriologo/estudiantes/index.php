<?php
/**
 * LISTADO DE ESTUDIANTES (NUTRIÓLOGO)
 * 
 * Muestra tabla con estudiantes asignados al nutriólogo.
 * Permite editar datos antropométricos de cada estudiante.
 * 
 * Variables disponibles:
 * - $list - Array con estudiantes y sus detalles
 *   Campos: id, nombre, apellidos, correo, peso, altura, 
 *           fecha_nacimiento, sexo, actividad
 * 
 * Cálculos en la vista:
 * - Edad: Calculada a partir de fecha_nacimiento
 * 
 * Acciones disponibles:
 * - Editar datos antropométricos del estudiante
 * 
 * Los datos mostrados son de EstudianteDetalle
 */
?>

<!-- ========== ENCABEZADO DE PÁGINA ========== -->
<div class='page-head'><h2>Estudiantes</h2><a class='btn' href='index.php?controller=nutriologo&action=dashboard'>← Volver</a></div>

<!-- ========== TABLA DE ESTUDIANTES ========== -->
<table class='table'><thead><tr><th>Nombre</th><th>Correo</th><th>Peso</th><th>Altura</th><th>Edad</th><th>Sexo</th><th>Actividad</th><th>Acción</th></tr></thead><tbody>
<?php foreach($list as $e): $edad=$e['fecha_nacimiento']?floor((time()-strtotime($e['fecha_nacimiento']))/(365.25*24*3600)):''; ?>
<tr><td><?= htmlspecialchars($e['nombre'].' '.$e['apellidos']) ?></td><td><?= htmlspecialchars($e['correo']) ?></td><td><?= $e['peso'] ?></td><td><?= $e['altura'] ?></td><td><?= $edad ?></td><td><?= $e['sexo'] ?></td><td><?= $e['actividad'] ?></td><td><a class='link' href='index.php?controller=nutriologo&action=estudianteForm&id=<?= $e['id'] ?>'>Editar</a></td></tr><?php endforeach; ?></tbody></table>
