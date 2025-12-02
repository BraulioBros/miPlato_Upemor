<?php
/**
 * LISTADO DE COMIDAS (VISTA PÚBLICA)
 * 
 * Muestra tabla con comidas disponibles para consulta pública.
 * Permite editar y eliminar comidas.
 * 
 * Variables disponibles:
 * - $comidas - Array con comidas
 *   Campos: id_comida, id, nombre, nutriente, calorias_por_100g
 * 
 * Acciones disponibles:
 * - Editar comida
 * - Eliminar comida (con confirmación)
 */
?>

<!-- ========== TABLA DE COMIDAS ========== -->
<table>
  <thead>
    <tr>
      <th>Código</th>
      <th>Nombre</th>
      <th>Nutriente</th>
      <th>Calorías / 100g</th>
      <th>Acción</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($comidas as $c): ?>
      <tr>
        <td><?= htmlspecialchars($c['id_comida']) ?></td>
        <td><?= htmlspecialchars($c['nombre']) ?></td>
        <td><?= htmlspecialchars($c['nutriente'] ?? 'Sin nutriente') ?></td>
        <td><?= number_format($c['calorias_por_100g'], 2) ?></td>

        <td>
          <a href="index.php?controller=comida&action=edit&id=<?= $c['id'] ?>">Editar</a>
          <a href="index.php?controller=comida&action=delete&id=<?= $c['id'] ?>"
             onclick="return confirm('¿Seguro que deseas eliminar esta comida?');">Eliminar</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
