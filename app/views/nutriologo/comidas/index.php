<?php
/**
 * LISTADO DE COMIDAS (NUTRIÓLOGO)
 * 
 * Muestra tabla con comidas que el nutriólogo ha creado/gestiona.
 * Permite crear, editar y eliminar comidas.
 * 
 * Variables disponibles:
 * - $list - Array con comidas
 *   Campos: id_comida, nombre, descripcion, calorias_por_100g, nutriente
 * 
 * Acciones disponibles:
 * - Crear nueva comida (botón "Nueva")
 * - Editar comida existente
 * - Eliminar comida (con confirmación)
 */
?>

<!-- ========== ENCABEZADO DE PÁGINA ========== -->
<div class='page-head'><h2>Comidas</h2><a class='btn' href='index.php?controller=nutriologo&action=dashboard'>← Volver</a></div>

<!-- ========== BARRA DE ACCIONES ========== -->
<div class='actions'><a class='btn' href='index.php?controller=nutriologo&action=comidaForm'>Nueva</a></div>

<!-- ========== TABLA DE COMIDAS ========== -->
<table class='table'><thead><tr><th>Nombre</th><th>Descripción</th><th>Kcal/100g</th><th>Nutriente</th><th>Acción</th></tr></thead><tbody>
<?php foreach($list as $c): ?><tr><td><?= htmlspecialchars($c['nombre']) ?></td><td><?= htmlspecialchars($c['descripcion']) ?></td><td><?= $c['calorias_por_100g'] ?></td><td><?= htmlspecialchars($c['nutriente'] ?? '-') ?></td>
<td><a class='link' href='index.php?controller=nutriologo&action=comidaForm&id=<?= $c['id_comida'] ?>'>Editar</a> | <a class='link danger' href='index.php?controller=nutriologo&action=comidaDelete&id=<?= $c['id_comida'] ?>' onclick="return confirm('¿Seguro que deseas eliminar esta comida?');">Eliminar</a></td></tr><?php endforeach; ?></tbody></table>
