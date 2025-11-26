<div class='page-head'><h2>Estudiantes</h2><a class='btn' href='index.php?controller=admin&action=dashboard'>← Volver</a></div>
<table class='table'><thead><tr><th>Nombre</th><th>Correo</th><th>Peso</th><th>Altura</th><th>Fecha Nacimiento</th><th>Sexo</th><th>Actividad</th><th>Acción</th></tr></thead><tbody>
<?php foreach($list as $e): ?>
<tr><td><?= htmlspecialchars($e['nombre'].' '.$e['apellidos']) ?></td><td><?= htmlspecialchars($e['correo']) ?></td><td><?= $e['peso'] ?></td><td><?= $e['altura'] ?></td><td><?= $e['fecha_nacimiento'] ?></td><td><?= $e['sexo'] ?></td><td><?= $e['actividad'] ?></td>
<td><a class='link' href='index.php?controller=admin&action=estudianteForm&id=<?= $e['id'] ?>'>Editar</a></td></tr><?php endforeach; ?></tbody></table>
