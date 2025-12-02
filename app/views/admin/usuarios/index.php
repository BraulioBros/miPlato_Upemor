<?php
/**
 * LISTADO DE USUARIOS (ADMIN)
 * 
 * Muestra tabla con todos los usuarios registrados en el sistema.
 * Los usuarios pueden tener roles de admin o estudiante, cada uno con permisos específicos.
 * 
 * Variables disponibles:
 * - $list - Array con usuarios del sistema
 *   Campos: id, nombre, apellidos, correo, rol
 * 
 * Acciones disponibles:
 * - Crear nuevo usuario (botón "Nuevo")
 * - Editar datos de usuario
 * - Eliminar usuario (con confirmación)
 */
?>

<!-- ========== ENCABEZADO DE PÁGINA ========== -->
<div class='page-head'>
    <h2>Usuarios</h2>
    <a class='btn' href='index.php?controller=admin&action=dashboard'>← Volver</a>
</div>

<!-- ========== BARRA DE ACCIONES ========== -->
<div class='actions'>
    <a class='btn' href='index.php?controller=admin&action=usuarioForm'>Nuevo</a>
</div>

<!-- ========== TABLA DE USUARIOS ========== -->
<table class='table'>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($list as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['nombre'].' '.$u['apellidos']) ?></td>
                <td><?= htmlspecialchars($u['correo']) ?></td>
                <td><?= $u['rol'] ?></td>
                <td>
                    <!-- Enlace: Editar usuario -->
                    <a class='link' href='index.php?controller=admin&action=usuarioForm&id=<?= $u['id'] ?>'>
                        Editar
                    </a> | 
                    <!-- Enlace: Eliminar usuario (con confirmación) -->
                    <a class='link danger' href='index.php?controller=admin&action=usuarioDelete&id=<?= $u['id'] ?>' 
                       onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
