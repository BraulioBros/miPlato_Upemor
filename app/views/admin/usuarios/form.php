<a class='btn' href='index.php?controller=admin&action=usuarios'>← Volver</a><h2><?= isset($u)?'Editar':'Nuevo' ?> usuario</h2>
<form method='POST' action='index.php?controller=admin&action=usuarioSave' autocomplete='off'>
<?php if(isset($u)): ?><input type='hidden' name='id' value='<?= $u['id'] ?>'><?php endif; ?>
<label class='form-label'>Nombre</label><input class='form-control' name='nombre' value='<?= $u['nombre'] ?? '' ?>' required>
<label class='form-label'>Apellidos</label><input class='form-control' name='apellidos' value='<?= $u['apellidos'] ?? '' ?>' required>
<label class='form-label'>Correo</label><input class='form-control' type='email' name='correo' value='<?= $u['correo'] ?? '' ?>' required autocomplete='off'>
<label class='form-label'>Rol</label><select class='form-control' name='rol'><option value='admin' <?= (isset($u)&&$u['rol']=='admin')?'selected':'' ?>>Admin</option><option value='estudiante' <?= (isset($u)&&$u['rol']=='estudiante')?'selected':'' ?>>Estudiante</option></select>
<?php if(!isset($u)): ?><label class='form-label'>Contraseña</label><input class='form-control' type='password' name='password' minlength='6' required autocomplete='new-password'><?php endif; ?>
<button class='btn primary mt-12' type='submit'>Guardar</button></form>
