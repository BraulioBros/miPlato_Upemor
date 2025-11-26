<div class='page-head'>
  <h2>Comidas</h2>
  <a class='btn' href='index.php?controller=admin&action=dashboard'>← Volver</a>
</div>

<div class='actions'>
  <a class='btn' href='index.php?controller=admin&action=comidaForm'>Nueva</a>
</div>

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
        <td><?= htmlspecialchars($c['nutriente'] ?? 'Sin nutriente') ?></td>
        <td><?= htmlspecialchars($c['calorias_por_100g']) ?></td>
        <td>
          <a class='link'
             href='index.php?controller=admin&action=comidaForm&id=<?= $c['id_comida'] ?>'>Editar</a>
           |
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
