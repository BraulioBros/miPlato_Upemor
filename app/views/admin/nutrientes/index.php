<?php
/**
 * LISTADO DE NUTRIENTES (ADMIN)
 * 
 * Muestra tabla con todos los nutrientes registrados en el sistema.
 * Los nutrientes son usados en la gestión de comidas y análisis nutricional.
 * 
 * Variables disponibles:
 * - $list - Array con nutrientes del sistema
 *   Campos: id_nutriente, nombre, calorias_por_gramo, unidad_medida, tipo
 * 
 * Acciones disponibles:
 * - Crear nuevo nutriente (botón "Nuevo")
 * - Editar datos de nutriente
 * - Eliminar nutriente (con confirmación)
 */
?>

<!-- ========== ENCABEZADO DE PÁGINA ========== -->
<div class='page-head'>
    <h2>Nutrientes</h2>
    <a class='btn' href='index.php?controller=admin&action=dashboard'>← Volver</a>
</div>

<!-- ========== BARRA DE ACCIONES ========== -->
<div class='actions'>
    <a class='btn' href='index.php?controller=admin&action=nutrienteForm'>Nuevo</a>
</div>

<!-- ========== TABLA DE NUTRIENTES ========== -->
<table class='table'>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Kcal/g</th>
            <th>Unidad</th>
            <th>Tipo</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($list as $n): ?>
            <tr>
                <td><?= htmlspecialchars($n['nombre']) ?></td>
                <td><?= $n['calorias_por_gramo'] ?></td>
                <td><?= htmlspecialchars($n['unidad_medida']) ?></td>
                <td><?= htmlspecialchars($n['tipo']) ?></td>
                <td>
                    <!-- Enlace: Editar nutriente -->
                    <a class='link' href='index.php?controller=admin&action=nutrienteForm&id=<?= $n['id_nutriente'] ?>'>
                        Editar
                    </a> | 
                    <!-- Enlace: Eliminar nutriente (con confirmación) -->
                    <a class='link danger' href='index.php?controller=admin&action=nutrienteDelete&id=<?= $n['id_nutriente'] ?>' 
                       onclick="return confirm('¿Seguro que deseas eliminar este nutriente?');">
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
