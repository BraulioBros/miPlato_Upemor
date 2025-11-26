<div class='page-head'>
  <h2>Reporte Semanal</h2>
  <div class='page-actions'>
    <a class='btn' href='index.php?controller=estudiante&action=dashboard'>← Volver al Panel</a>
    <a class='btn primary' href='index.php?controller=estudiante&action=pdfSemanal'>📥 Descargar PDF</a>
  </div>
</div>

<div class='reporte-container'>
  <!-- Selector de fecha -->
  <div class='date-selector-section'>
    <form method='GET' action='index.php' class='date-form'>
      <input type='hidden' name='controller' value='estudiante'>
      <input type='hidden' name='action' value='reporteSemanal'>
      <div class='form-group'>
        <label for='fecha'>Selecciona un día de la semana:</label>
        <input type='date' id='fecha' name='fecha' value='<?= htmlspecialchars($fechaBase) ?>' max='<?= date('Y-m-d') ?>'>
      </div>
      <button type='submit' class='btn primary'>Buscar</button>
    </form>
  </div>

  <!-- Información de la semana -->
  <div class='info-section'>
    <h3>Información de la Semana</h3>
    <div class='info-cards'>
      <div class='info-card'>
        <span class='label'>Período</span>
        <span class='value'><?= date('d/m', strtotime($desde)) ?> - <?= date('d/m/Y', strtotime($hasta)) ?></span>
      </div>
      <div class='info-card'>
        <span class='label'>Total Semanal</span>
        <span class='value'><?= number_format($totalKcalSemanal, 0) ?> kcal</span>
      </div>
      <div class='info-card'>
        <span class='label'>Promedio Diario</span>
        <span class='value'><?= number_format($totalKcalSemanal / 7, 0) ?> kcal</span>
      </div>
      <div class='info-card'>
        <span class='label'>Objetivo Diario</span>
        <span class='value'><?= $objetivo ?> kcal</span>
      </div>
    </div>
  </div>

  <!-- Tabla de consumo semanal -->
  <div class='table-section'>
    <h3>Detalle de Consumo Semanal</h3>
    <table class='reporte-table'>
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Comida</th>
          <th>Gramos</th>
          <th>kcal/100g</th>
          <th>kcal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($consumosSemanal as $consumo): ?>
          <tr>
            <td><?= htmlspecialchars($consumo['fecha']) ?></td>
            <td><?= htmlspecialchars(substr($consumo['comida'], 0, 30)) ?></td>
            <td><?= number_format($consumo['gramos'], 1) ?></td>
            <td><?= number_format($consumo['kcal_100g'], 0) ?></td>
            <td><strong><?= number_format($consumo['kcal'], 0) ?></strong></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan='4' style='text-align: right; font-weight: bold;'>TOTAL SEMANAL:</td>
          <td><strong><?= number_format($totalKcalSemanal, 0) ?> kcal</strong></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- Gráfica de consumo diario por día de la semana -->
  <div class='chart-section'>
    <h3>Consumo por Día de la Semana</h3>
    <canvas id='chartDaily'></canvas>
    <script>
      const dailyData = <?php echo json_encode($consumoPorDia); ?>;
      const labels = dailyData.map(x => x.dia);
      const data = dailyData.map(x => Math.round(x.kcal));
      
      new Chart(document.getElementById('chartDaily'), {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'kcal por día',
            data: data,
            backgroundColor: 'rgba(102, 126, 234, 0.8)',
            borderColor: '#667eea',
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: true,
              position: 'top'
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Calorías'
              }
            }
          }
        }
      });
    </script>
  </div>

  <!-- Gráfica de nutrientes semanal -->
  <div class='chart-section'>
    <h3>Nutrientes Consumidos en la Semana</h3>
    <canvas id='chartNutrientes'></canvas>
    <script>
      const nutrientes = <?php echo json_encode($nutrientesPorSemana); ?>;
      const nutrientesLabels = Object.keys(nutrientes);
      const nutrientesData = Object.values(nutrientes);
      
      const colores = ['#4285F4', '#FB8C00', '#34A853', '#AB47BC', '#EA4335', '#00ACC1', '#FBC02D', '#E91E63', '#009688', '#795548'];
      
      new Chart(document.getElementById('chartNutrientes'), {
        type: 'doughnut',
        data: {
          labels: nutrientesLabels,
          datasets: [{
            data: nutrientesData,
            backgroundColor: colores.slice(0, nutrientesLabels.length),
            borderColor: '#fff',
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: true,
              position: 'right'
            }
          }
        }
      });
    </script>
  </div>

</div>

<style>
.page-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.page-head h2 {
  margin: 0;
}

.page-actions {
  display: flex;
  gap: 10px;
}

.page-actions .btn {
  margin: 0;
}

.date-selector-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 25px;
  border-radius: 12px;
  margin-bottom: 30px;
}

.date-form {
  display: flex;
  gap: 15px;
  align-items: flex-end;
  flex-wrap: wrap;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 600;
  font-size: 14px;
}

.form-group input {
  padding: 10px 15px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  min-width: 200px;
}

.date-form .btn {
  margin: 0;
}

.reporte-container {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.info-section, .table-section, .chart-section {
  margin-bottom: 40px;
}

.info-section h3, .table-section h3, .chart-section h3 {
  color: #333;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid #667eea;
}

.info-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.info-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
}

.info-card .label {
  display: block;
  font-size: 12px;
  opacity: 0.9;
  text-transform: uppercase;
  margin-bottom: 8px;
}

.info-card .value {
  display: block;
  font-size: 24px;
  font-weight: bold;
}

.reporte-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.reporte-table thead {
  background-color: #f5f5f5;
}

.reporte-table th {
  padding: 15px;
  text-align: left;
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #e0e0e0;
}

.reporte-table td {
  padding: 12px 15px;
  border-bottom: 1px solid #e0e0e0;
}

.reporte-table tbody tr:hover {
  background-color: #f9f9f9;
}

.reporte-table tfoot {
  background-color: #f5f5f5;
  font-weight: bold;
}

.reporte-table tfoot td {
  padding: 15px;
  border-top: 2px solid #e0e0e0;
}

.chart-section canvas {
  max-height: 400px;
  margin: 20px 0;
}
</style>
