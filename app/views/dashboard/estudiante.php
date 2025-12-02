<?php
/**
 * DASHBOARD ESTUDIANTE
 * 
 * Panel de control principal del estudiante.
 * Muestra:
 * - Objetivo calórico diario (calculado con Harris-Benedict)
 * - Consumo actual del día
 * - Feedback motivacional personalizado
 * - Acciones rápidas (registrar consumo, reportes)
 * - Tabla de consumo reciente
 * - Gráficas de tendencia (14 días)
 * 
 * Variables disponibles (desde EstudianteController::dashboard()):
 * - $objetivo - Calorías objetivo diario
 * - $k - Calorías consumidas hoy
 * - $feedback - Mensaje motivacional personalizado
 * - $list - Listado de consumos del usuario
 * - $diario - Resumen diario (últimos 14 días)
 * - $semanal - Resumen semanal (últimas 8 semanas)
 */
?>

<h1>Panel Estudiante</h1>

<!-- ========== TARJETAS DE ESTADÍSTICAS ========== -->
<div class='student-stats'>
    <!-- Objetivo calórico diario -->
    <div class='stat-card'>
        <div class='stat-icon'>🎯</div>
        <div class='stat-info'>
            <p class='stat-label'>Objetivo Diario</p>
            <p class='stat-value'><?= $objetivo ?> kcal</p>
        </div>
    </div>
    
    <!-- Consumo del día -->
    <div class='stat-card'>
        <div class='stat-icon'>🍽️</div>
        <div class='stat-info'>
            <p class='stat-label'>Consumido Hoy</p>
            <p class='stat-value'><?= round($k) ?> kcal</p>
        </div>
    </div>
</div>

<!-- Feedback motivacional generado por el controlador -->
<p class='feedback-text'><em><?= htmlspecialchars($feedback) ?></em></p>

<!-- ========== ACCIONES RÁPIDAS ========== -->
<div class='action-cards'>
  <a href='index.php?controller=estudiante&action=consumoAdd' class='action-card'>
    <div class='action-icon'>➕</div>
    <h3>Registrar Consumo</h3>
    <p>Agrega una nueva comida a tu registro diario</p>
  </a>
  <a href='index.php?controller=estudiante&action=reporteDiario' class='action-card'>
    <div class='action-icon'>📊</div>
    <h3>Reporte Diario</h3>
    <p>Visualiza tu consumo y progreso del día</p>
  </a>
  <a href='index.php?controller=estudiante&action=reporteSemanal' class='action-card'>
    <div class='action-icon'>📈</div>
    <h3>Reporte Semanal</h3>
    <p>Analiza tu progreso de la última semana</p>
  </a>
</div>

<style>
.student-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin: 20px 0;
}

.stat-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 25px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 20px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stat-icon {
  font-size: 48px;
}

.stat-label {
  margin: 0;
  font-size: 14px;
  opacity: 0.9;
  text-transform: uppercase;
}

.stat-value {
  margin: 0;
  font-size: 28px;
  font-weight: bold;
}

.feedback-text {
  text-align: center;
  font-size: 16px;
  color: #333;
  margin: 20px 0;
}

.action-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin: 30px 0;
}

.action-card {
  background: white;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  padding: 30px;
  text-decoration: none;
  color: inherit;
  transition: all 0.3s ease;
  text-align: center;
  cursor: pointer;
}

.action-card:hover {
  border-color: #667eea;
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
  transform: translateY(-5px);
}

.action-icon {
  font-size: 48px;
  margin-bottom: 15px;
}

.action-card h3 {
  margin: 15px 0 10px 0;
  font-size: 20px;
  color: #333;
}

.action-card p {
  margin: 0;
  color: #666;
  font-size: 14px;
  line-height: 1.5;
}
</style>

<?php if(!empty($_SESSION['feedback_consumo'])): ?>
<div id='modalFeedback' class='modal-overlay'>
  <div class='modal-content <?= $_SESSION['feedback_consumo']['tipo_alerta'] ?>'>
    <div class='modal-header'>
      <h2><?= $_SESSION['feedback_consumo']['sobrepasado'] ? '⚠️ Alerta de Calorías' : '✅ ¡Consumo registrado!' ?></h2>
      <button class='modal-close' onclick='cerrarModal()'>&times;</button>
    </div>
    <div class='modal-body'>
      <div class='feedback-card <?= $_SESSION['feedback_consumo']['tipo_alerta'] ?>'>
        <p class='feedback-message'><?= htmlspecialchars($_SESSION['feedback_consumo']['mensaje']) ?></p>
        <div class='feedback-stats'>
          <div class='stat'>
            <span class='stat-label'>Consumidas:</span>
            <span class='stat-value'><?= $_SESSION['feedback_consumo']['kcal_consumidas'] ?> kcal</span>
          </div>
          <div class='stat'>
            <span class='stat-label'>Objetivo:</span>
            <span class='stat-value'><?= $_SESSION['feedback_consumo']['objetivo'] ?> kcal</span>
          </div>
          <div class='stat'>
            <span class='stat-label'>Progreso:</span>
            <span class='stat-value <?= $_SESSION['feedback_consumo']['sobrepasado'] ? 'exceeded' : '' ?>'><?= $_SESSION['feedback_consumo']['porcentaje'] ?>%</span>
          </div>
        </div>
        <div class='progress-bar'>
          <div class='progress-fill <?= $_SESSION['feedback_consumo']['tipo_alerta'] ?>' style='width: min(<?= $_SESSION['feedback_consumo']['porcentaje'] ?>%, 100%)'></div>
        </div>
        <?php if($_SESSION['feedback_consumo']['sobrepasado']): ?>
        <div class='warning-box'>
          <p><strong>💡 Recomendaciones:</strong></p>
          <ul>
            <li>Evita consumir más alimentos a menos que sea absolutamente necesario</li>
            <li>Aumenta tu actividad física para compensar el exceso calórico</li>
            <li>Mantén equilibrio en los próximos días comiendo porciones más pequeñas</li>
            <li>Consulta con tu nutriólogo si esto se repite frecuentemente</li>
          </ul>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <div class='modal-footer'>
      <button class='btn primary' onclick='cerrarModal()'>Continuar</button>
    </div>
  </div>
</div>

<style>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 90%;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  overflow: hidden;
}

.modal-content.peligro {
  border: 3px solid #dc2626;
}

.modal-content.advertencia {
  border: 3px solid #f59e0b;
}

.modal-header {
  padding: 20px;
  background: linear-gradient(135deg, #4CAF50, #45a049);
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-content.peligro .modal-header {
  background: linear-gradient(135deg, #dc2626, #b91c1c);
}

.modal-content.advertencia .modal-header {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.modal-header h2 {
  margin: 0;
  font-size: 24px;
}

.modal-close {
  background: none;
  border: none;
  color: white;
  font-size: 28px;
  cursor: pointer;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  opacity: 0.8;
}

.modal-body {
  padding: 30px 20px;
}

.feedback-card {
  text-align: center;
}

.feedback-card.peligro .feedback-message,
.feedback-card.advertencia .feedback-message {
  color: #991b1b;
  font-weight: 600;
}

.feedback-message {
  font-size: 18px;
  color: #333;
  margin: 0 0 25px 0;
  font-weight: 500;
}

.feedback-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 15px;
  margin-bottom: 20px;
}

.stat {
  background: #f5f5f5;
  padding: 15px;
  border-radius: 8px;
}

.stat-label {
  display: block;
  font-size: 12px;
  color: #666;
  margin-bottom: 5px;
  text-transform: uppercase;
}

.stat-value {
  display: block;
  font-size: 20px;
  font-weight: bold;
  color: #4CAF50;
}

.stat-value.exceeded {
  color: #dc2626;
}

.progress-bar {
  width: 100%;
  height: 10px;
  background: #e0e0e0;
  border-radius: 5px;
  overflow: hidden;
  margin-bottom: 15px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #4CAF50, #45a049);
  transition: width 0.3s ease;
}

.progress-fill.peligro {
  background: linear-gradient(90deg, #dc2626, #b91c1c);
}

.progress-fill.advertencia {
  background: linear-gradient(90deg, #f59e0b, #d97706);
}

.warning-box {
  background: #fee2e2;
  border-left: 4px solid #dc2626;
  padding: 15px;
  border-radius: 6px;
  text-align: left;
  margin-top: 20px;
}

.warning-box p {
  margin: 0 0 10px 0;
  color: #991b1b;
  font-weight: 600;
}

.warning-box ul {
  margin: 0;
  padding-left: 20px;
  color: #7f1d1d;
}

.warning-box li {
  margin-bottom: 8px;
  font-size: 14px;
}

.modal-footer {
  padding: 15px 20px;
  background: #f5f5f5;
  text-align: right;
}

.modal-footer .btn {
  margin: 0;
}
</style>

<script>
function cerrarModal() {
  document.getElementById('modalFeedback').style.display = 'none';
  <?php unset($_SESSION['feedback_consumo']); ?>
}

// Cerrar modal al presionar Escape
document.addEventListener('keydown', function(e) {
  if(e.key === 'Escape') {
    cerrarModal();
  }
});
</script>
<?php endif; ?>
