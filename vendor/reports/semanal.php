<?php
// Reporte semanal de consumo con FPDF + PHPlot

// MODELOS
require_once __DIR__ . '/../../app/models/EstudianteDetalle.php';
require_once __DIR__ . '/../../app/models/Consumo.php';
require_once __DIR__ . '/../../public/libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../public/libraries/phplot/phplot.php';

// ==================================
//   DATOS DEL ESTUDIANTE Y CONSUMO
// ==================================

$uid            = $_SESSION['user']['id'];
$nombreCompleto = $_SESSION['user']['nombre'] . ' ' . ($_SESSION['user']['apellidos'] ?? '');

$det = (new EstudianteDetalle())->findByUserId($uid);

// Datos personales
$edad = 0;
if (!empty($det['fecha_nacimiento'])) {
    $fn   = new DateTime($det['fecha_nacimiento']);
    $edad = (new DateTime())->diff($fn)->y;
}
$peso   = isset($det['peso'])   ? $det['peso'] . ' kg'   : 'N/D';
$altura = isset($det['altura']) ? $det['altura'] . ' cm' : 'N/D';

// Rango de 7 días (hoy y 6 días atrás)
$desde = date('Y-m-d', strtotime('-6 days'));
$hasta = date('Y-m-d');

$consumos = (new Consumo())->detalleRango($uid, $desde, $hasta);

// =======================
//   TOTALES POR NUTRIENTE (SEMANA)
// =======================
$nutrientesPorSemana = [];
$totalKcalSemana = 0;

foreach ($consumos as $r) {
    $nutriente = !empty($r['nutriente']) ? $r['nutriente'] : 'Sin nutriente';
    
    if (!isset($nutrientesPorSemana[$nutriente])) {
        $nutrientesPorSemana[$nutriente] = 0;
    }
    $nutrientesPorSemana[$nutriente] += (float) $r['kcal'];
    $totalKcalSemana += (float) $r['kcal'];
}

// Si no hay nutrientes registrados, crear gráfica vacía
if (empty($nutrientesPorSemana)) {
    $nutrientesPorSemana['Sin datos'] = 1;
}

// Datos para PHPlot (gráfica de pastel)
$dataNutrientes = [];
foreach ($nutrientesPorSemana as $nutriente => $kcal) {
    $dataNutrientes[] = [$nutriente, (float) $kcal];
}
$totalesPorDia = [];

$cursor = new DateTime($desde);
$end    = new DateTime($hasta);

while ($cursor <= $end) {
    $key = $cursor->format('Y-m-d');
    $totalesPorDia[$key] = 0;
    $cursor->modify('+1 day');
}

foreach ($consumos as $r) {
    if (isset($totalesPorDia[$r['fecha']])) {
        $totalesPorDia[$r['fecha']] += (float) $r['kcal'];
    }
}

// Datos para PHPlot
$data         = [];
$labelsTexto  = [];
$valuesTexto  = [];

foreach ($totalesPorDia as $fecha => $total) {
    $diaNum  = (int) date('N', strtotime($fecha)); // 1=Lun..7=Dom
    $mapDias = [1 => 'Lun', 2 => 'Mar', 3 => 'Mie', 4 => 'Jue', 5 => 'Vie', 6 => 'Sab', 7 => 'Dom'];
    $label   = $mapDias[$diaNum] . ' ' . date('d', strtotime($fecha));

    $data[]        = [$label, (float) $total];
    $labelsTexto[] = $label;
    $valuesTexto[] = round($total, 2);
}

// Invertir el array para que hoy esté a la derecha
$data = array_reverse($data);

// =======================
//   GRÁFICA CON PHPLOT
// =======================

// Carpeta donde guardamos la gráfica
$graphsDir = __DIR__ . '/../../public/media/graphs';
if (!is_dir($graphsDir)) {
    mkdir($graphsDir, 0777, true);
}

$graphFile = $graphsDir . '/consumo_semanal.png';

// Crear gráfica de barras
$plot = new PHPlot(800, 400);
$plot->SetImageBorderType('plain');
$plot->SetPlotType('bars');
$plot->SetDataType('text-data');
$plot->SetDataValues($data);

$plot->SetTitle('Consumo semanal de kcal');
$plot->SetXTitle('Dia');
$plot->SetYTitle('kcal');

$plot->SetDataColors([
    '#4285F4', // azul
    '#FB8C00', // naranja
    '#34A853', // verde
    '#AB47BC', // morado
    '#EA4335', // rojo
    '#00ACC1', // turquesa
    '#FBC02D', // amarillo
]);

$plot->SetShading(4);
$plot->SetOutputFile($graphFile);
$plot->SetIsInline(true);
$plot->DrawGraph();

// Crear gráfica de pastel (nutrientes) con GD Library
$graphFileNutrientes = $graphsDir . '/consumo_semanal_nutrientes_' . date('Y-m-d-H-i-s') . '.png';

// Colores para la gráfica (RGB)
$colorList = [
    [66, 133, 244],     // azul
    [251, 140, 0],      // naranja
    [52, 168, 83],      // verde
    [171, 71, 188],     // morado
    [234, 67, 53],      // rojo
    [0, 172, 193],      // turquesa
    [251, 192, 45],     // amarillo
    [233, 30, 99],      // rosa
    [0, 150, 136],      // teal
    [121, 85, 72],      // marrón
];

// Crear imagen
$width = 1000;
$height = 600;
$imageNutrientes = imagecreatetruecolor($width, $height);
$bgColor = imagecolorallocate($imageNutrientes, 255, 255, 255);
imagefill($imageNutrientes, 0, 0, $bgColor);

// Dibujar gráfica de pastel
$centerX = $width / 2 - 100;
$centerY = $height / 2;
$radius = 180;

// Calcular total
$totalNutrientes = array_sum($nutrientesPorSemana);

// Calcular ángulos para cada segmento
$startAngle = 0;
$colorIdx = 0;
$legendY = 50;

// Dibujar título
$black = imagecolorallocate($imageNutrientes, 0, 0, 0);
imagestring($imageNutrientes, 5, 250, 20, utf8_decode('Consumo semanal de kcal por nutriente - ' . $desde . ' a ' . $hasta), $black);

// Dibujar segmentos del pastel
foreach ($nutrientesPorSemana as $nutriente => $kcal) {
    $percentage = ($kcal / $totalNutrientes) * 100;
    $angle = ($kcal / $totalNutrientes) * 360;
    
    $rgb = $colorList[$colorIdx % count($colorList)];
    $segmentColor = imagecolorallocate($imageNutrientes, $rgb[0], $rgb[1], $rgb[2]);
    
    // Dibujar segmento
    imagefilledarc($imageNutrientes, $centerX, $centerY, $radius * 2, $radius * 2, $startAngle, $startAngle + $angle, $segmentColor, IMG_ARC_PIE);
    
    // Dibujar leyenda en el lado derecho
    $legendColor = imagecolorallocate($imageNutrientes, $rgb[0], $rgb[1], $rgb[2]);
    imagefilledrectangle($imageNutrientes, 750, $legendY, 765, $legendY + 10, $legendColor);
    
    $nutrienteLimpio = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nutriente);
    $nutrienteLimpio = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $nutrienteLimpio);
    imagestring($imageNutrientes, 3, 770, $legendY - 2, $nutrienteLimpio . ' (' . round($percentage, 1) . '%)', $black);
    
    $startAngle += $angle;
    $colorIdx++;
    $legendY += 18;
}

// Guardar imagen
imagepng($imageNutrientes, $graphFileNutrientes);
imagedestroy($imageNutrientes);

// =======================
//   PDF CON FPDF
// =======================

$pdf = new FPDF();
$pdf->AddPage();

// Título
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Reporte semanal de consumo'), 0, 1, 'C');

// Datos del estudiante
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, utf8_decode("Estudiante: {$nombreCompleto}"), 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode("Edad: {$edad}   Peso: {$peso}   Altura: {$altura}"), 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode("Semana: {$desde} a {$hasta}"), 0, 1, 'L');

$pdf->Ln(4);

// =======================
//   TABLA DETALLADA
// =======================

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, utf8_decode('Detalle de consumo semanal'), 0, 1, 'C');
$pdf->Ln(2);

// Encabezados
$pdf->SetFont('Arial', 'B', 10);
$w = [30, 80, 25, 30, 20]; // anchos de columnas
$pdf->Cell($w[0], 8, utf8_decode('Fecha'),      1, 0, 'C');
$pdf->Cell($w[1], 8, utf8_decode('Comida'),     1, 0, 'C');
$pdf->Cell($w[2], 8, utf8_decode('Gramos'),     1, 0, 'C');
$pdf->Cell($w[3], 8, utf8_decode('kcal/100g'),  1, 0, 'C');
$pdf->Cell($w[4], 8, utf8_decode('kcal'),       1, 1, 'C');

$pdf->SetFont('Arial', '', 9);

// Filas
foreach ($consumos as $r) {
    $pdf->Cell($w[0], 6, utf8_decode($r['fecha']),      1, 0, 'C');
    $pdf->Cell($w[1], 6, utf8_decode($r['comida']),     1, 0, 'C');
    $pdf->Cell($w[2], 6, number_format($r['gramos'], 2),    1, 0, 'C');
    $pdf->Cell($w[3], 6, number_format($r['kcal_100g'], 2), 1, 0, 'C');
    $pdf->Cell($w[4], 6, number_format($r['kcal'], 0),      1, 1, 'C');
}

// Total de calorías
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($w[0], 8, '', 1, 0, 'C');
$pdf->Cell($w[1], 8, '', 1, 0, 'C');
$pdf->Cell($w[2], 8, '', 1, 0, 'C');
$pdf->Cell($w[3], 8, utf8_decode('TOTAL:'), 1, 0, 'C');
$pdf->Cell($w[4], 8, number_format($totalKcalSemana, 0), 1, 1, 'C');

$pdf->Ln(8);

// =======================
//   GRÁFICA AL FINAL
// =======================

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, utf8_decode('Gráfica de consumo semanal (kcal por día)'), 0, 1, 'C');
$pdf->Ln(2);

$yGraph = $pdf->GetY();

// Si la gráfica no cabe en la página actual, agregar una nueva página
if ($yGraph > 180) {
    $pdf->AddPage();
    $yGraph = $pdf->GetY();
}

// Usamos la ruta absoluta del archivo PNG
$pdf->Image($graphFile, 25, $yGraph, 160, 90);

// Verificar si hay espacio para la segunda gráfica, si no, agregar nueva página
$nextY = $yGraph + 95;
if ($nextY > 200) {
    $pdf->AddPage();
    $pdf->Ln(10);
} else {
    $pdf->Ln(95);
}

// =======================
//   GRÁFICA DE NUTRIENTES
// =======================

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, utf8_decode('Gráfica de consumo por nutriente'), 0, 1, 'C');
$pdf->Ln(2);

$yGraphNutrientes = $pdf->GetY();

// Insertar imagen del gráfico de nutrientes
if (file_exists($graphFileNutrientes)) {
    $pdf->Image($graphFileNutrientes, 5, $yGraphNutrientes, 200, 120);
}

// Salida del PDF (inline en el navegador)
$pdf->Output('D', 'reporte de '.$_SESSION['user']['nombre'] . ' ' . $_SESSION['user']['apellidos'].' del '.$desde.' hasta '.$hasta.'.pdf');
exit;
