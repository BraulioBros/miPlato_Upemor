<h1>Dashboard Admin</h1>
<div class='grid-cards'>
  <div class='bigcard'>
    <a href='index.php?controller=admin&action=usuarios' style='text-decoration: none; color: inherit;'>Usuarios <strong><?= $stats['usuarios'] ?></strong></a>
  </div>

  <div class='bigcard'>
    <a href='index.php?controller=admin&action=nutriologos' style='text-decoration: none; color: inherit;'>Nutriólogos <strong><?= $stats['nutriologos'] ?></strong></a>
  </div>

  <div class='bigcard'>
    <a href='index.php?controller=admin&action=nutrientes' style='text-decoration: none; color: inherit;'>Nutrientes <strong><?= $stats['nutrientes'] ?></strong></a>
  </div>

  <div class='bigcard'>
    <a href='index.php?controller=admin&action=comidas' style='text-decoration: none; color: inherit;'>Comidas <strong><?= $stats['comidas'] ?></strong></a>
  </div>

  <div class='bigcard'>
    <a href='index.php?controller=admin&action=estudiantes' style='text-decoration: none; color: inherit;'>Estudiantes <strong><?= $stats['estudiantes'] ?></strong></a>
  </div>
</div>

<h2 style='margin-top: 32px; margin-bottom: 16px;'>Acciones de la base de datos</h2>
<div class='grid-cards'>
  <!-- Card: respaldo de base de datos -->
  <div class='bigcard'>
    <div>Respaldo de base de datos</div>
    <p>Generar y descargar copia de seguridad actual.</p>
    <a class="btn" href="index.php?controller=backup&action=descargar">
      Descargar respaldo
    </a>
  </div>

  <!-- Card: restaurar desde respaldo -->
  <div class='bigcard'>
    <div>Restaurar base de datos</div>
    <p>Aplicar el respaldo más reciente guardado en el servidor.</p>
    <a class="btn btn-danger" href="index.php?controller=backup&action=restaurar"
       onclick="return confirm('¿Seguro que deseas restaurar la base de datos? Esta acción no se puede deshacer.');">
      Restaurar último respaldo
    </a>
  </div>
</div>
