<?php
require_once __DIR__.'/../models/Comida.php';require_once __DIR__.'/../models/Consumo.php';require_once __DIR__.'/../models/EstudianteDetalle.php';
class EstudianteController extends Controller{
private function objetivoCalorias($uid){$det=(new EstudianteDetalle())->findByUserId($uid);if(!$det){return 2000;} $edad=20;if(!empty($det['fecha_nacimiento'])){$edad=max(10,(int)((time()-strtotime($det['fecha_nacimiento']))/(365.25*24*3600)));} $peso=floatval($det['peso']?:70);$altura=floatval($det['altura']?:170);$sexo=$det['sexo']?:'M';$actividad=floatval($det['actividad']?:1.4);$bmr=($sexo=='M')?(10*$peso+6.25*$altura-5*$edad+5):(10*$peso+6.25*$altura-5*$edad-161);return round($bmr*$actividad);}
public function dashboard(){ $this->requireRole('estudiante');$uid=$_SESSION['user']['id'];$consumo=new Consumo();$list=$consumo->allByUser($uid);$diario=$consumo->resumenDiario($uid);$semanal=$consumo->resumenSemanal($uid);$objetivo=$this->objetivoCalorias($uid);$hoy=date('Y-m-d');$k=0;foreach($diario as $d){if($d['fecha']==$hoy){$k=$d['kcal'];break;}}$diff=$objetivo-$k;$feedback=($diff>150)?"Te faltan aproximadamente ".round($diff)." kcal para tu objetivo de hoy.":(($diff<-150)?"Has superado tu objetivo por ".round(-$diff)." kcal hoy.":"¡Vas muy bien, estás dentro de tu objetivo de hoy!");$this->view('dashboard/estudiante',compact('list','diario','semanal','objetivo','k','feedback'));}
public function consumoAdd(){ $this->requireRole('estudiante');$comidas=(new Comida())->all();$this->view('estudiante/consumo_form',compact('comidas'));}
public function consumoSave(){ 
    $this->requireRole('estudiante');
    $uid=$_SESSION['user']['id'];
    (new Consumo())->create(['usuario_id'=>$uid,'comida_id'=>$_POST['comida_id'],'cantidad_gramos'=>$_POST['cantidad_gramos'],'fecha'=>$_POST['fecha']]);
    
    // Calcular feedback
    $consumo = new Consumo();
    $objetivo = $this->objetivoCalorias($uid);
    $hoy = date('Y-m-d');
    $diario = $consumo->resumenDiario($uid);
    $k = 0;
    foreach($diario as $d){
        if($d['fecha']==$hoy){
            $k=$d['kcal'];
            break;
        }
    }
    
    $diff = $objetivo - $k;
    $porcentaje = round(($k / $objetivo) * 100);
    
    // Mensajes motivacionales
    $mensajes = [];
    $tipo_alerta = 'normal'; // normal, excelente, advertencia, peligro
    
    if($diff>300){
        $tipo_alerta = 'normal';
        $mensajes = [
            "¡Buen comienzo! Te quedan ".round($diff)." kcal.",
            "Sigue adelante, aún tienes mucho espacio para hoy.",
            "¡A comer saludable se trata!"
        ];
    } elseif($diff>150){
        $tipo_alerta = 'normal';
        $mensajes = [
            "¡Muy bien! Te faltan aproximadamente ".round($diff)." kcal.",
            "Vas en buen camino, continúa así.",
            "¡Casi a la mitad! Sigue comiendo balanceado."
        ];
    } elseif($diff>0){
        $tipo_alerta = 'excelente';
        $mensajes = [
            "¡Excelente! Estás muy cerca de tu objetivo con ".round($diff)." kcal restantes.",
            "¡Casi lo logras! Cuida las porciones.",
            "¡Vas a lograrlo, estás muy cerca!"
        ];
    } elseif($diff<-300){
        $tipo_alerta = 'peligro';
        $mensajes = [
            "⚠️ ¡Has superado significativamente tu objetivo por ".round(-$diff)." kcal! Evita consumir más comidas a menos que sea muy necesario.",
            "⚠️ ¡Cuidado! Has excedido tu meta en ".round(-$diff)." kcal. Te recomendamos abstenerte de comer más hoy.",
            "⚠️ Sobrepaso considerable: ".round(-$diff)." kcal extra. Considera actividad física y sé cauteloso con las siguientes comidas."
        ];
    } elseif($diff<-150){
        $tipo_alerta = 'advertencia';
        $mensajes = [
            "⚠️ Has superado tu objetivo por ".round(-$diff)." kcal. Modera el consumo en las siguientes comidas.",
            "⚠️ Sobrepasaste tu meta en ".round(-$diff)." kcal. Solo consume si es absolutamente necesario.",
            "⚠️ Cuidado: ".round(-$diff)." kcal por encima de tu objetivo. Evita comer más a menos que sea imprescindible."
        ];
    } else {
        $tipo_alerta = 'excelente';
        $mensajes = [
            "¡Perfecto! Estás exactamente en tu objetivo. ¡Bien hecho!",
            "¡Equilibrio perfecto! Así se mantiene una buena nutrición.",
            "¡Excelente control de porciones!"
        ];
    }
    
    $mensaje = $mensajes[array_rand($mensajes)];
    
    $_SESSION['feedback_consumo'] = [
        'mensaje' => $mensaje,
        'kcal_consumidas' => round($k),
        'objetivo' => $objetivo,
        'diferencia' => round($diff),
        'porcentaje' => $porcentaje,
        'tipo_alerta' => $tipo_alerta,
        'sobrepasado' => $diff < 0
    ];
    
    redirect('estudiante','dashboard');
}
public function consumoDelete(){ $this->requireRole('estudiante');(new Consumo())->delete($_GET['id'],$_SESSION['user']['id']);redirect('estudiante','dashboard');}
public function pdfDiario(){ $this->requireRole('estudiante');$uid=$_SESSION['user']['id'];$consumo=new Consumo();$diario=$consumo->resumenDiario($uid);include __DIR__.'/../../vendor/reports/diario.php';}
public function pdfSemanal()
{
    $this->requireRole('estudiante');
    include __DIR__ . '/../../vendor/reports/semanal.php';
    exit;
}

public function reporteDiario()
{
    $this->requireRole('estudiante');
    $uid = $_SESSION['user']['id'];
    $consumo = new Consumo();
    
    // Datos del estudiante
    $det = (new EstudianteDetalle())->findByUserId($uid);
    $edad = 0;
    if (!empty($det['fecha_nacimiento'])) {
        $fn = new DateTime($det['fecha_nacimiento']);
        $edad = (new DateTime())->diff($fn)->y;
    }
    
    $objetivo = $this->objetivoCalorias($uid);
    
    // Fecha seleccionada o hoy
    $fechaSeleccionada = $_GET['fecha'] ?? date('Y-m-d');
    
    // Validar que la fecha no sea en el futuro
    if ($fechaSeleccionada > date('Y-m-d')) {
        $fechaSeleccionada = date('Y-m-d');
    }
    
    // Consumo del día seleccionado
    $consumosDiarios = $consumo->detallePorFecha($uid, $fechaSeleccionada);
    $totalKcal = array_sum(array_map(function($c) { return (float) $c['kcal']; }, $consumosDiarios));
    
    // Agrupar por nutrientes
    $nutrientesPorDia = [];
    foreach ($consumosDiarios as $r) {
        $nutriente = !empty($r['nutriente']) ? $r['nutriente'] : 'Sin nutriente';
        if (!isset($nutrientesPorDia[$nutriente])) {
            $nutrientesPorDia[$nutriente] = 0;
        }
        $nutrientesPorDia[$nutriente] += (float) $r['kcal'];
    }
    
    // Datos históricos para gráfica (últimos 14 días)
    $diario = $consumo->resumenDiario($uid);
    
    $this->view('estudiante/reporteDiario', compact('consumosDiarios', 'totalKcal', 'objetivo', 'diario', 'fechaSeleccionada', 'nutrientesPorDia'));
}

public function reporteSemanal()
{
    $this->requireRole('estudiante');
    $uid = $_SESSION['user']['id'];
    $consumo = new Consumo();
    
    // Objetivo
    $objetivo = $this->objetivoCalorias($uid);
    
    // Fecha base seleccionada o hoy
    $fechaBase = $_GET['fecha'] ?? date('Y-m-d');
    
    // Validar que la fecha no sea en el futuro
    if ($fechaBase > date('Y-m-d')) {
        $fechaBase = date('Y-m-d');
    }
    
    // Calcular el lunes de esa semana
    $fechaObj = new DateTime($fechaBase);
    $diaSemana = (int) $fechaObj->format('N'); // 1 = Lunes, 7 = Domingo
    
    // Si no es lunes, retroceder hasta el lunes
    if ($diaSemana != 1) {
        $fechaObj->modify('-' . ($diaSemana - 1) . ' days');
    }
    
    $desde = $fechaObj->format('Y-m-d');
    
    // Domingo es 6 días después del lunes
    $fechaObj->modify('+6 days');
    $hasta = $fechaObj->format('Y-m-d');
    
    // Consumo de la semana
    $consumosSemanal = $consumo->detalleRango($uid, $desde, $hasta);
    $totalKcalSemanal = array_sum(array_map(function($c) { return (float) $c['kcal']; }, $consumosSemanal));
    
    // Agrupar por nutrientes semanal
    $nutrientesPorSemana = [];
    foreach ($consumosSemanal as $r) {
        $nutriente = !empty($r['nutriente']) ? $r['nutriente'] : 'Sin nutriente';
        if (!isset($nutrientesPorSemana[$nutriente])) {
            $nutrientesPorSemana[$nutriente] = 0;
        }
        $nutrientesPorSemana[$nutriente] += (float) $r['kcal'];
    }
    
    // Consumo por día de la semana
    $consumoPorDia = [];
    $cursor = new DateTime($desde);
    $end = new DateTime($hasta);
    $diasMap = ['Mon' => 'Lunes', 'Tue' => 'Martes', 'Wed' => 'Miércoles', 'Thu' => 'Jueves', 'Fri' => 'Viernes', 'Sat' => 'Sábado', 'Sun' => 'Domingo'];
    
    while ($cursor <= $end) {
        $fecha = $cursor->format('Y-m-d');
        $diaSemana = $cursor->format('D');
        $diaHumano = $diasMap[$diaSemana] ?? $diaSemana;
        
        $kcalDia = 0;
        foreach ($consumosSemanal as $consumo_item) {
            if ($consumo_item['fecha'] == $fecha) {
                $kcalDia += (float) $consumo_item['kcal'];
            }
        }
        
        $consumoPorDia[] = ['dia' => $diaHumano, 'kcal' => $kcalDia];
        $cursor->modify('+1 day');
    }
    
    // Datos históricos para gráfica (últimas 8 semanas)
    $semanal = $consumo->resumenSemanal($uid);
    
    $this->view('estudiante/reporteSemanal', compact('consumosSemanal', 'totalKcalSemanal', 'objetivo', 'consumoPorDia', 'semanal', 'desde', 'hasta', 'fechaBase', 'nutrientesPorSemana'));
}
}
