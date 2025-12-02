<?php
/**
 * LISTADO DE COMIDAS
 * 
 * Muestra tabla con todas las comidas registradas.
 * Permite:
 * - Ver nombre, descripción, nutriente asociado, calorías
 * - Editar comida
 * - Eliminar comida (con confirmación)
 * - Crear nueva comida
 * 
 * Variables disponibles:
 * - $list - Array con todas las comidas del sistema
 *   Cada comida contiene: id_comida, nombre, descripcion, nutriente, calorias_por_100g
 */
?>

<!-- ========== ENCABEZADO ========== -->
<div class='page-head'>
    <h2>Comidas</h2>
    <a class='btn' href='index.php?controller=admin&action=dashboard'>← Volver</a>
</div>

<!-- ========== BOTÓN CREAR NUEVA ========== -->
<div class='actions'>
    <a class='btn' href='index.php?controller=admin&action=comidaForm'>Nueva</a>
</div>

<!-- ========== TABLA DE COMIDAS ========== -->
<table class='table'>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Nutriente</th>
            <th>Kcal/100g</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['nombre']) ?></td>
                <td><?= htmlspecialchars($c['descripcion']) ?></td>
                <!-- Muestra nutriente asociado o "Sin nutriente" si es NULL -->
                <td><?= htmlspecialchars($c['nutriente'] ?? 'Sin nutriente') ?></td>
                <td><?= htmlspecialchars($c['calorias_por_100g']) ?></td>
                <td>
                    <!-- Link para editar -->
                    <a class='link'
                       href='index.php?controller=admin&action=comidaForm&id=<?= $c['id_comida'] ?>'>
                       Editar
                    </a>
                    |
                    <!-- Link para eliminar (con confirmación) -->
                    <a class='link danger'
                       href='index.php?controller=admin&action=comidaDelete&id=<?= $c['id_comida'] ?>'
                       onclick="return confirm('¿Seguro que deseas eliminar esta comida?');">
                       Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
