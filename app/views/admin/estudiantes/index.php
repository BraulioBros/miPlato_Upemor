<?php
/**
 * LISTADO DE ESTUDIANTES (ADMIN)
 * 
 * Muestra tabla con todos los estudiantes y sus datos antropométricos.
 * 
 * Variables disponibles:
 * - $list - Array con estudiantes y sus detalles
 *   Campos: id, nombre, apellidos, correo, peso, altura, 
 *           fecha_nacimiento, sexo, actividad
 * 
 * Acciones disponibles:
 * - Editar datos antropométricos del estudiante
 */
?>

<div class='page-head'>
    <h2>Estudiantes</h2>
    <a class='btn' href='index.php?controller=admin&action=dashboard'>← Volver</a>
</div>

<!-- ========== TABLA DE ESTUDIANTES ========== -->
<table class='table'>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Peso</th>
            <th>Altura</th>
            <th>Fecha Nacimiento</th>
            <th>Sexo</th>
            <th>Actividad</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($list as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['nombre'].' '.$e['apellidos']) ?></td>
                <td><?= htmlspecialchars($e['correo']) ?></td>
                <td><?= $e['peso'] ?></td>
                <td><?= $e['altura'] ?></td>
                <td><?= $e['fecha_nacimiento'] ?></td>
                <td><?= $e['sexo'] ?></td>
                <td><?= $e['actividad'] ?></td>
                <td>
                    <a class='link' href='index.php?controller=admin&action=estudianteForm&id=<?= $e['id'] ?>'>
                        Editar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
