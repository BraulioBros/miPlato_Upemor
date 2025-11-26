<div class='card center'>
<div class='circle-logo' style='background-image:url("<?= asset('img/logo.png') ?>");'></div>
<h2>Inicia sesión</h2><p>Accede con tu cuenta de MiPlato Upemor</p>
<head><script src="https://kit.fontawesome.com/ce1db38258.js" crossorigin="anonymous"></script></head>
<form method='POST' action='index.php?controller=auth&action=doLogin' autocomplete='off'>
<label class='form-label'><i class="fa-solid fa-envelope"></i> Correo</label><input class='form-control' type='email' name='correo' autocomplete='username' required>
<label class='form-label'><i class="fa-solid fa-lock"></i> Contraseña</label><input class='form-control mb-12' type='password' name='contrasena' autocomplete='current-password' minlength='6' required>
<button class='btn primary w-100 mt-12' type='submit'>Entrar</button></form>
<p class='muted'>¿No tienes cuenta? <a class='link' href='index.php?controller=auth&action=register'>Regístrate</a></p></div>
