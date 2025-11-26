<div class='card center'>
<div class='circle-logo' style='background-image:url("<?= asset('img/logo.png') ?>");'></div>
<h2>Crear cuenta</h2><p>Regístrate en MiPlato Upemor</p>
<form method='POST' action='index.php?controller=auth&action=doRegister' autocomplete='off'>
<label class='form-label'>Nombre</label><input class='form-control' name='nombre' required>
<label class='form-label'>Apellidos</label><input class='form-control' name='apellidos' required>
<label class='form-label'>Fecha de nacimiento</label><input class='form-control' type='date' name='fecha_nacimiento' value='2005-06-12' min='1980-01-01' max='2009-12-31' required>
<label class='form-label'>Correo</label><input class='form-control' type='email' name='correo' required autocomplete='off'>
<label class='form-label'>Contraseña</label><input class='form-control' type='password' name='contrasena' minlength='6' required autocomplete='new-password'>
<button class='btn primary w-100 mt-12' type='submit'>Registrarme</button></form>
<p class='muted'>¿Ya tienes cuenta? <a class='link' href='index.php?controller=auth&action=login'>Inicia sesión</a></p></div>
