<?php
/**
 * FORMULARIO DE COMPLETAR PERFIL (NUTRIÓLOGO)
 * 
 * Formulario para que el nutriólogo complete su información profesional
 * en el primer acceso al sistema.
 * 
 * Campos requeridos:
 * - cedula - Cédula profesional (identificador único)
 * - telefono - Teléfono de contacto del nutriólogo
 * 
 * Comportamiento:
 * - Se muestra al nutriólogo en primer login si no ha completado el perfil
 * - Bloquea acceso al dashboard hasta completar
 * - Guarda datos en tabla nutriologo_detalle
 * 
 * Acción: Guarda datos profesionales (completarSave)
 */
?>

<a class='btn' href='index.php?controller=nutriologo&action=dashboard'>← Volver</a>
<h2>Completa tu registro</h2>

<!-- ========== FORMULARIO DE PERFIL PROFESIONAL ========== -->
<form method='POST' action='index.php?controller=nutriologo&action=completarSave' autocomplete='off'><label class='form-label'>Cédula profesional</label><input class='form-control' name='cedula' required autocomplete='off'><label class='form-label'>Teléfono</label><input class='form-control' name='telefono' required autocomplete='off'><button class='btn primary' type='submit'>Guardar</button></form>
