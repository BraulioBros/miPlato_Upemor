<?php
/**
 * CONTROLADOR DE ESTUDIANTE
 * 
 * Maneja todas las funcionalidades del estudiante:
 * - Dashboard principal
 * - Registro de consumo de alimentos
 * - Reportes diarios y semanales
 * - Descarga de PDF de reportes
 * - Cálculo de objetivos calóricos personalizados
 */

require_once __DIR__.'/../models/Comida.php';
require_once __DIR__.'/../models/Consumo.php';
require_once __DIR__.'/../models/EstudianteDetalle.php';

class EstudianteController extends Controller{
  
  /**
   * Calcula el objetivo calórico diario del estudiante
   * 
   * Utiliza la fórmula de Harris-Benedict ajustada por factor de actividad:
   * BMR = Tasa Metabólica Basal (calorías en reposo)
   * Objetivo = BMR * Factor de Actividad
   * 
   * @param int $uid - ID del usuario (estudiante)
   * @return int - Calorías objetivo diarias redondeadas
   */
  private function objetivoCalorias($uid){
    // Obtiene los datos del estudiante
    $det=(new EstudianteDetalle())->findByUserId($uid);
    if(!$det){return 2000;} // Valor por defecto si no hay datos
    
    // Calcula la edad a partir de la fecha de nacimiento
    $edad=20;
    if(!empty($det['fecha_nacimiento'])){
      $edad=max(10,(int)((time()-strtotime($det['fecha_nacimiento']))/(365.25*24*3600)));
    }
    
    // Obtiene peso, altura, sexo y factor de actividad (con valores por defecto)
    $peso=floatval($det['peso']?:70);
    $altura=floatval($det['altura']?:170);
    $sexo=$det['sexo']?:'M';
    $actividad=floatval($det['actividad']?:1.4);
    
    // Calcula BMR usando la fórmula de Harris-Benedict
    if($sexo=='M'){
      $bmr = 10*$peso + 6.25*$altura - 5*$edad + 5; // Hombres
    } else {
      $bmr = 10*$peso + 6.25*$altura - 5*$edad - 161; // Mujeres
    }
    
    // Retorna el objetivo (BMR * factor de actividad)
    return round($bmr*$actividad);
  }
  
  /**
   * Muestra el dashboard principal del estudiante
   * 
   * Muestra:
   * - Estadísticas de hoy (kcal consumidas vs objetivo)
   * - Resumen de consumo diario y semanal
   * - Modal con feedback del último consumo registrado
   */
  public function dashboard(){ 
    $this->requireRole('estudiante');
    
    // Obtiene el ID del usuario autenticado
    $uid=$_SESSION['user']['id'];
    $consumo=new Consumo();
    
    // Obtiene todos los consumos del estudiante
    $list=$consumo->allByUser($uid);
    
    // Obtiene resumen diario (últimos 14 días)
    $diario=$consumo->resumenDiario($uid);
    
    // Obtiene resumen semanal (últimas 8 semanas)
    $semanal=$consumo->resumenSemanal($uid);
    
    // Calcula el objetivo calórico del estudiante
    $objetivo=$this->objetivoCalorias($uid);
    
    // Calcula kcal consumidas hoy
    $hoy=date('Y-m-d');
    $k=0;
    foreach($diario as $d){
      if($d['fecha']==$hoy){
        $k=$d['kcal'];
        break;
      }
    }
    
    // Calcula diferencia respecto al objetivo
    $diff=$objetivo-$k;
    
    // Genera feedback motivacional básico
    $feedback=($diff>150)?"Te faltan aproximadamente ".round($diff)." kcal para tu objetivo de hoy.":(($diff<-150)?"Has superado tu objetivo por ".round(-$diff)." kcal hoy.":"¡Vas muy bien, estás dentro de tu objetivo de hoy!");
    
    // Renderiza la vista del dashboard
    $this->view('dashboard/estudiante',compact('list','diario','semanal','objetivo','k','feedback'));
  }
  
  /**
   * Muestra el formulario para registrar un nuevo consumo
   */
  public function consumoAdd(){ 
    $this->requireRole('estudiante');
    
    // Obtiene todas las comidas disponibles
    $comidas=(new Comida())->all();
    
    // Renderiza el formulario
    $this->view('estudiante/consumo_form',compact('comidas'));
  }
  
  /**
   * Procesa el guardado de un nuevo consumo
   * 
   * Proceso:
   * 1. Valida que el usuario esté autenticado
   * 2. Crea el registro de consumo en la BD
   * 3. Calcula feedback motivacional según kcal consumidas vs objetivo
   * 4. Genera mensajes diferentes según si se sobrepasó o no la meta
   * 5. Redirige al dashboard
   */
  public function consumoSave(){ 
    $this->requireRole('estudiante');
    $uid=$_SESSION['user']['id'];
    
    // Crea el registro de consumo en la BD
    (new Consumo())->create([
      'usuario_id'=>$uid,
      'comida_id'=>$_POST['comida_id'],
      'cantidad_gramos'=>$_POST['cantidad_gramos'],
      'fecha'=>$_POST['fecha']
    ]);
    
    // ========== CALCULAR FEEDBACK DETALLADO ==========
    
    $consumo = new Consumo();
    $objetivo = $this->objetivoCalorias($uid);
    $hoy = date('Y-m-d');
    $diario = $consumo->resumenDiario($uid);
    
    // Calcula kcal consumidas hoy
    $k = 0;
    foreach($diario as $d){
      if($d['fecha']==$hoy){
        $k=$d['kcal'];
        break;
      }
    }
    
    // Calcula diferencia y porcentaje
    $diff = $objetivo - $k;
    $porcentaje = round(($k / $objetivo) * 100);
    
    // Genera mensajes motivacionales según el estado
    $mensajes = [];
    $tipo_alerta = 'normal'; // normal, excelente, advertencia, peligro
    
    // Mucho espacio disponible (>300 kcal)
    if($diff>300){
      $tipo_alerta = 'normal';
      $mensajes = [
        "¡Buen comienzo! Te quedan ".round($diff)." kcal.",
        "Sigue adelante, aún tienes mucho espacio para hoy.",
        "¡A comer saludable se trata!"
      ];
    } 
    // Espacio moderado (150-300 kcal)
    elseif($diff>150){
      $tipo_alerta = 'normal';
      $mensajes = [
        "¡Muy bien! Te faltan aproximadamente ".round($diff)." kcal.",
        "Vas en buen camino, continúa así.",
        "¡Casi a la mitad! Sigue comiendo balanceado."
      ];
    } 
    // Muy cerca del objetivo (0-150 kcal)
    elseif($diff>0){
      $tipo_alerta = 'excelente';
      $mensajes = [
        "¡Excelente! Estás muy cerca de tu objetivo con ".round($diff)." kcal restantes.",
        "¡Casi lo logras! Cuida las porciones.",
        "¡Vas a lograrlo, estás muy cerca!"
      ];
    } 
    // SOBREPASADO significativamente (>300 kcal)
    elseif($diff<-300){
      $tipo_alerta = 'peligro';
      $mensajes = [
        "⚠️ ¡Has superado significativamente tu objetivo por ".round(-$diff)." kcal! Evita consumir más comidas a menos que sea muy necesario.",
        "⚠️ ¡Cuidado! Has excedido tu meta en ".round(-$diff)." kcal. Te recomendamos abstenerte de comer más hoy.",
        "⚠️ Sobrepaso considerable: ".round(-$diff)." kcal extra. Considera actividad física y sé cauteloso con las siguientes comidas."
      ];
    } 
    // SOBREPASADO moderadamente (150-300 kcal)
    elseif($diff<-150){
      $tipo_alerta = 'advertencia';
      $mensajes = [
        "⚠️ Has superado tu objetivo por ".round(-$diff)." kcal. Modera el consumo en las siguientes comidas.",
        "⚠️ Sobrepasaste tu meta en ".round(-$diff)." kcal. Solo consume si es absolutamente necesario.",
        "⚠️ Cuidado: ".round(-$diff)." kcal por encima de tu objetivo. Evita comer más a menos que sea imprescindible."
      ];
    } 
    // Objetivo exactamente alcanzado
    else {
      $tipo_alerta = 'excelente';
      $mensajes = [
        "¡Perfecto! Estás exactamente en tu objetivo. ¡Bien hecho!",
        "¡Equilibrio perfecto! Así se mantiene una buena nutrición.",
        "¡Excelente control de porciones!"
      ];
    }
    
    // Selecciona un mensaje aleatorio
    $mensaje = $mensajes[array_rand($mensajes)];
    
    // Guarda el feedback en la sesión para mostrar en el modal
    $_SESSION['feedback_consumo'] = [
      'mensaje' => $mensaje,
      'kcal_consumidas' => round($k),
      'objetivo' => $objetivo,
      'diferencia' => round($diff),
      'porcentaje' => $porcentaje,
      'tipo_alerta' => $tipo_alerta,
      'sobrepasado' => $diff < 0
    ];
    
    // Redirige al dashboard para mostrar el modal
    redirect('estudiante','dashboard');
  }
  
  /**
   * Elimina un registro de consumo
   * 
   * @param int GET['id'] - ID del consumo a eliminar
   */
  public function consumoDelete(){ 
    $this->requireRole('estudiante');
    
    // Elimina el consumo (el modelo valida que pertenezca al usuario)
    (new Consumo())->delete($_GET['id'],$_SESSION['user']['id']);
    
    // Redirige al dashboard
    redirect('estudiante','dashboard');
  }
  
  /**
   * Genera y descarga el reporte diario en PDF
   * 
   * El PDF incluye:
   * - Datos personales del estudiante
   * - Tabla de consumo del día
   * - Gráfica de nutrientes (pie chart con GD Library)
   */
  public function pdfDiario(){ 
    $this->requireRole('estudiante');
    
    // Obtiene el ID del usuario
    $uid=$_SESSION['user']['id'];
    $consumo=new Consumo();
    
    // Obtiene el resumen diario
    $diario=$consumo->resumenDiario($uid);
    
    // Incluye el generador de PDF diario
    include __DIR__.'/../../vendor/reports/diario.php';
  }
  
  /**
   * Genera y descarga el reporte semanal en PDF
   * 
   * El PDF incluye:
   * - Datos personales del estudiante
   * - Tabla de consumo semanal
   * - Gráfica de nutrientes (pie chart con GD Library)
   */
  public function pdfSemanal()
  {
    $this->requireRole('estudiante');
    
    // Incluye el generador de PDF semanal
    include __DIR__ . '/../../vendor/reports/semanal.php';
    exit;
  }

  /**
   * Muestra el reporte diario interactivo con gráficas Chart.js
   * 
   * Muestra:
   * - Selector de fecha para elegir qué día ver
   * - Tabla detallada de consumo
   * - Gráfica de líneas con consumo de los últimos 14 días
   * - Gráfica de dónut con desglose de nutrientes
   */
  public function reporteDiario()
  {
    $this->requireRole('estudiante');
    $uid = $_SESSION['user']['id'];
    $consumo = new Consumo();
    
    // ========== DATOS DEL ESTUDIANTE ==========
    
    $det = (new EstudianteDetalle())->findByUserId($uid);
    $edad = 0;
    if (!empty($det['fecha_nacimiento'])) {
      $fn = new DateTime($det['fecha_nacimiento']);
      $edad = (new DateTime())->diff($fn)->y;
    }
    
    $objetivo = $this->objetivoCalorias($uid);
    
    // ========== FECHA SELECCIONADA ==========
    
    // Obtiene la fecha del parámetro GET o usa hoy
    $fechaSeleccionada = $_GET['fecha'] ?? date('Y-m-d');
    
    // Valida que la fecha no sea en el futuro
    if ($fechaSeleccionada > date('Y-m-d')) {
      $fechaSeleccionada = date('Y-m-d');
    }
    
    // ========== CONSUMO DEL DÍA ==========
    
    // Obtiene los detalles de consumo del día seleccionado
    $consumosDiarios = $consumo->detallePorFecha($uid, $fechaSeleccionada);
    
    // Suma total de kcal del día
    $totalKcal = array_sum(array_map(function($c) { return (float) $c['kcal']; }, $consumosDiarios));
    
    // ========== AGRUPAR POR NUTRIENTES ==========
    
    // Agrupa los consumos por tipo de nutriente
    $nutrientesPorDia = [];
    foreach ($consumosDiarios as $r) {
      $nutriente = !empty($r['nutriente']) ? $r['nutriente'] : 'Sin nutriente';
      if (!isset($nutrientesPorDia[$nutriente])) {
        $nutrientesPorDia[$nutriente] = 0;
      }
      $nutrientesPorDia[$nutriente] += (float) $r['kcal'];
    }
    
    // ========== DATOS HISTÓRICOS ==========
    
    // Obtiene el resumen diario (últimos 14 días) para la gráfica de líneas
    $diario = $consumo->resumenDiario($uid);
    
    // Renderiza la vista con todos los datos
    $this->view('estudiante/reporteDiario', compact('consumosDiarios', 'totalKcal', 'objetivo', 'diario', 'fechaSeleccionada', 'nutrientesPorDia'));
  }

  /**
   * Muestra el reporte semanal interactivo con gráficas Chart.js
   * 
   * Muestra:
   * - Selector de fecha para elegir qué semana ver
   * - Tabla detallada de consumo semanal
   * - Gráfica de barras con consumo por día
   * - Gráfica de líneas con tendencia de 8 semanas
   * - Gráfica de dónut con desglose de nutrientes
   */
  public function reporteSemanal()
  {
    $this->requireRole('estudiante');
    $uid = $_SESSION['user']['id'];
    $consumo = new Consumo();
    
    // ========== OBJETIVO ==========
    
    $objetivo = $this->objetivoCalorias($uid);
    
    // ========== CALCULAR SEMANA ==========
    
    // Obtiene la fecha base del parámetro GET o usa hoy
    $fechaBase = $_GET['fecha'] ?? date('Y-m-d');
    
    // Valida que la fecha no sea en el futuro
    if ($fechaBase > date('Y-m-d')) {
      $fechaBase = date('Y-m-d');
    }
    
    // Calcula el lunes de la semana que contiene la fecha base
    $fechaObj = new DateTime($fechaBase);
    $diaSemana = (int) $fechaObj->format('N'); // 1 = Lunes, 7 = Domingo
    
    // Si no es lunes, retrocede hasta el lunes
    if ($diaSemana != 1) {
      $fechaObj->modify('-' . ($diaSemana - 1) . ' days');
    }
    
    $desde = $fechaObj->format('Y-m-d');
    
    // Calcula el domingo (6 días después del lunes)
    $fechaObj->modify('+6 days');
    $hasta = $fechaObj->format('Y-m-d');
    
    // ========== CONSUMO DE LA SEMANA ==========
    
    // Obtiene los detalles de consumo de toda la semana
    $consumosSemanal = $consumo->detalleRango($uid, $desde, $hasta);
    
    // Suma total de kcal de la semana
    $totalKcalSemanal = array_sum(array_map(function($c) { return (float) $c['kcal']; }, $consumosSemanal));
    
    // ========== AGRUPAR POR NUTRIENTES ==========
    
    // Agrupa los consumos semanales por tipo de nutriente
    $nutrientesPorSemana = [];
    foreach ($consumosSemanal as $r) {
      $nutriente = !empty($r['nutriente']) ? $r['nutriente'] : 'Sin nutriente';
      if (!isset($nutrientesPorSemana[$nutriente])) {
        $nutrientesPorSemana[$nutriente] = 0;
      }
      $nutrientesPorSemana[$nutriente] += (float) $r['kcal'];
    }
    
    // ========== CONSUMO POR DÍA ==========
    
    // Crea un array con el consumo de cada día de la semana
    $consumoPorDia = [];
    $cursor = new DateTime($desde);
    $end = new DateTime($hasta);
    $diasMap = [
      'Mon' => 'Lunes', 'Tue' => 'Martes', 'Wed' => 'Miércoles', 
      'Thu' => 'Jueves', 'Fri' => 'Viernes', 'Sat' => 'Sábado', 'Sun' => 'Domingo'
    ];
    
    // Itera cada día de la semana
    while ($cursor <= $end) {
      $fecha = $cursor->format('Y-m-d');
      $diaSemana = $cursor->format('D');
      $diaHumano = $diasMap[$diaSemana] ?? $diaSemana;
      
      // Suma las kcal de ese día
      $kcalDia = 0;
      foreach ($consumosSemanal as $consumo_item) {
        if ($consumo_item['fecha'] == $fecha) {
          $kcalDia += (float) $consumo_item['kcal'];
        }
      }
      
      $consumoPorDia[] = ['dia' => $diaHumano, 'kcal' => $kcalDia];
      $cursor->modify('+1 day');
    }
    
    // ========== DATOS HISTÓRICOS ==========
    
    // Obtiene el resumen semanal (últimas 8 semanas) para la gráfica de líneas
    $semanal = $consumo->resumenSemanal($uid);
    
    // Renderiza la vista con todos los datos
    $this->view('estudiante/reporteSemanal', compact('consumosSemanal', 'totalKcalSemanal', 'objetivo', 'consumoPorDia', 'semanal', 'desde', 'hasta', 'fechaBase', 'nutrientesPorSemana'));
  }
}
