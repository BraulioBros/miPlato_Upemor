<?php
/**
 * DASHBOARD DEL NUTRIÓLOGO
 * 
 * Vista principal del panel de nutriólogo.
 * Muestra estadísticas generales y resumen de recursos disponibles.
 * 
 * Variables disponibles:
 * - $stats - Array con estadísticas:
 *   ├─ estudiantes: Cantidad total de estudiantes en el sistema
 *   ├─ nutrientes: Cantidad total de nutrientes registrados
 *   └─ comidas: Cantidad total de comidas disponibles
 * 
 * Funcionalidades:
 * - Muestra título del panel
 * - Tarjetas con contadores de recursos principales
 * - Instrucciones para acceder a módulos
 * 
 * Módulos accesibles desde el menú lateral:
 * - Dashboard: Este panel
 * - Estudiantes: Ver y gestionar datos de estudiantes
 * - Nutrientes: Ver y crear nutrientes
 * - Comidas: Ver y crear comidas
 */
?>

<!-- ========== ENCABEZADO PRINCIPAL ========== -->
<h1>Panel Nutriólogo</h1>

<!-- ========== TARJETAS CON ESTADÍSTICAS ========== -->
<!-- Muestra contadores de los recursos principales del sistema -->
<div class='grid-cards'>
    <!-- Cantidad de estudiantes -->
    <div class='bigcard'>
        Estudiantes <strong><?= $stats['estudiantes'] ?></strong>
    </div>
    
    <!-- Cantidad de nutrientes registrados -->
    <div class='bigcard'>
        Nutrientes <strong><?= $stats['nutrientes'] ?></strong>
    </div>
    
    <!-- Cantidad de comidas disponibles -->
    <div class='bigcard'>
        Comidas <strong><?= $stats['comidas'] ?></strong>
    </div>
</div>

<!-- ========== INSTRUCCIONES ========== -->
<!-- Guía rápida para el usuario sobre cómo navegar -->
<p>Usa el menú lateral para acceder a tus módulos.</p>