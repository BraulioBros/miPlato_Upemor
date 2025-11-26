<a class='btn' href='index.php?controller=admin&action=nutriologos'>← Volver</a><h2><?= isset($u)?'Editar':'Nuevo' ?> nutriólogo</h2>
<form method='POST' action='index.php?controller=admin&action=nutriologoSave' autocomplete='off'>
<?php if(isset($u)): ?><input type='hidden' name='id' value='<?= $u['id'] ?>'><?php endif; ?>
<label class='form-label'>Nombre</label><input class='form-control' name='nombre' value='<?= $u['nombre'] ?? '' ?>' required>
<label class='form-label'>Apellidos</label><input class='form-control' name='apellidos' value='<?= $u['apellidos'] ?? '' ?>' required>
<label class='form-label'>Correo</label><input class='form-control' type='email' name='correo' value='<?= $u['correo'] ?? '' ?>' required autocomplete='off'>
<?php if(!isset($u)): ?><label class='form-label'>Contraseña</label><input class='form-control' type='password' name='password' minlength='6' required autocomplete='new-password'><?php endif; ?>
<button class='btn primary mt-12' type='submit'>Guardar</button></form>
