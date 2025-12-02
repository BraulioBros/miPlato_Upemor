<?php
/**
 * LISTADO DE NUTRIÓLOGOS (ADMIN)
 * 
 * Muestra tabla con todos los nutriólogos registrados en el sistema.
 * Los nutriólogos tienen permisos para gestionar estudiantes e información nutricional.
 * 
 * Variables disponibles:
 * - $list - Array con nutriólogos del sistema
 *   Campos: id, nombre, apellidos, correo, cedula, telefono
 * 
 * Acciones disponibles:
 * - Crear nuevo nutriólogo (botón "Nuevo")
 * - Editar datos de nutriólogo
 * - Eliminar nutriólogo (con confirmación)
 */
?>

<!-- ========== ENCABEZADO DE PÁGINA ========== -->
<div class='page-head'>
    <h2>Nutriólogos</h2>
    <a class='btn' href='index.php?controller=admin&action=dashboard'>← Volver</a>
</div>

<!-- ========== BARRA DE ACCIONES ========== -->
<div class='actions'>
    <a class='btn' href='index.php?controller=admin&action=nutriologoForm'>Nuevo</a>
</div>

<!-- ========== TABLA DE NUTRIÓLOGOS ========== -->
<table class='table'>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Cédula</th>
            <th>Teléfono</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($list as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['nombre'].' '.$u['apellidos']) ?></td>
                <td><?= htmlspecialchars($u['correo']) ?></td>
                <td><?= htmlspecialchars($u['cedula'] ?? '-') ?></td>
                <td><?= htmlspecialchars($u['telefono'] ?? '-') ?></td>
                <td>
                    <!-- Enlace: Editar nutriólogo -->
                    <a class='link' href='index.php?controller=admin&action=nutriologoForm&id=<?= $u['id'] ?>'>
                        Editar
                    </a> | 
                    <!-- Enlace: Eliminar nutriólogo (con confirmación) -->
                    <a class='link danger' href='index.php?controller=admin&action=nutriologoDelete&id=<?= $u['id'] ?>' 
                       onclick="return confirm('¿Seguro que deseas eliminar este nutriólogo?');">
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
