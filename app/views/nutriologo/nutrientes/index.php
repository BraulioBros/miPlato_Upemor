<div class='page-head'><h2>Nutrientes</h2><a class='btn' href='index.php?controller=nutriologo&action=dashboard'>← Volver</a></div>
<div class='actions'><a class='btn' href='index.php?controller=nutriologo&action=nutrienteForm'>Nuevo</a></div>
<table class='table'><thead><tr><th>Nombre</th><th>Kcal/g</th><th>Unidad</th><th>Tipo</th><th>Acción</th></tr></thead><tbody>
<?php foreach($list as $n): ?><tr><td><?= htmlspecialchars($n['nombre']) ?></td><td><?= $n['calorias_por_gramo'] ?></td><td><?= htmlspecialchars($n['unidad_medida']) ?></td><td><?= htmlspecialchars($n['tipo']) ?></td>
<td><a class='link' href='index.php?controller=nutriologo&action=nutrienteForm&id=<?= $n['id_nutriente'] ?>'>Editar</a> | <a class='link danger' href='index.php?controller=nutriologo&action=nutrienteDelete&id=<?= $n['id_nutriente'] ?>' onclick="return confirm('¿Seguro que deseas eliminar este nutriente?');">Eliminar</a></td></tr><?php endforeach; ?></tbody></table>
