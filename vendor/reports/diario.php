<?php
// Reporte diario de consumo con FPDF + PHPlot

// MODELOS
require_once __DIR__ . '/../../app/models/EstudianteDetalle.php';
require_once __DIR__ . '/../../app/models/Consumo.php';

// LIBRERÍAS
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

// Fecha actual
$hoy = date('Y-m-d');

// Obtener consumos del día
$consumos = (new Consumo())->detallePorFecha($uid, $hoy);

// =======================
//   TOTALES POR NUTRIENTE
// =======================
$nutrientesPorDia = [];
$totalKcal = 0;

foreach ($consumos as $r) {
    $nutriente = !empty($r['nutriente']) ? $r['nutriente'] : 'Sin nutriente';
    
    if (!isset($nutrientesPorDia[$nutriente])) {
        $nutrientesPorDia[$nutriente] = 0;
    }
    $nutrientesPorDia[$nutriente] += (float) $r['kcal'];
    $totalKcal += (float) $r['kcal'];
}

// Si no hay nutrientes registrados, crear gráfica vacía
if (empty($nutrientesPorDia)) {
    $nutrientesPorDia['Sin datos'] = 1;
}

// =======================
//   GRÁFICA CON GD LIBRARY
// =======================

$graphsDir = __DIR__ . '/../../public/media/graphs';
if (!is_dir($graphsDir)) {
    mkdir($graphsDir, 0777, true);
}

$graphFile = $graphsDir . '/consumo_diario_' . date('Y-m-d-H-i-s') . '.png';

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
$image = imagecreatetruecolor($width, $height);
$bgColor = imagecolorallocate($image, 255, 255, 255);
imagefill($image, 0, 0, $bgColor);

// Dibujar gráfica de pastel
$centerX = $width / 2 - 100;
$centerY = $height / 2;
$radius = 180;

// Calcular ángulos para cada segmento
$totalKcalTemp = array_sum($nutrientesPorDia);
$startAngle = 0;
$colorIdx = 0;
$legendY = 50;

// Dibujar título
$black = imagecolorallocate($image, 0, 0, 0);
imagestring($image, 5, 250, 20, 'Consumo diario de kcal por nutriente - ' . $hoy, $black);

// Dibujar segmentos del pastel
foreach ($nutrientesPorDia as $nutriente => $kcal) {
    $percentage = ($kcal / $totalKcalTemp) * 100;
    $angle = ($kcal / $totalKcalTemp) * 360;
    
    $rgb = $colorList[$colorIdx % count($colorList)];
    $segmentColor = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
    
    // Dibujar segmento
    imagefilledarc($image, $centerX, $centerY, $radius * 2, $radius * 2, $startAngle, $startAngle + $angle, $segmentColor, IMG_ARC_PIE);
    
    // Dibujar leyenda en el lado derecho
    $legendColor = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
    imagefilledrectangle($image, 750, $legendY, 765, $legendY + 10, $legendColor);
    
    $nutrienteLimpio = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nutriente);
    $nutrienteLimpio = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $nutrienteLimpio);
    imagestring($image, 3, 770, $legendY - 2, $nutrienteLimpio . ' (' . round($percentage, 1) . '%)', $black);
    
    $startAngle += $angle;
    $colorIdx++;
    $legendY += 18;
}

// Guardar imagen
imagepng($image, $graphFile);
imagedestroy($image);

// =======================
//   PDF CON FPDF
// =======================

$pdf = new FPDF();
$pdf->AddPage();

// Título
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Reporte diario de consumo'), 0, 1, 'C');

// Datos del estudiante
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, utf8_decode("Estudiante: {$nombreCompleto}"), 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode("Edad: {$edad}   Peso: {$peso}   Altura: {$altura}"), 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode("Fecha: {$hoy}"), 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode("Total de kcal consumidas: {$totalKcal}"), 0, 1, 'L');

$pdf->Ln(4);

// =======================
//   TABLA DETALLADA
// =======================

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, utf8_decode('Detalle de consumo diario'), 0, 1, 'C');
$pdf->Ln(2);

// Encabezados - Centrados en la página
$pdf->SetFont('Arial', 'B', 9);
$w = [60, 20, 25, 45, 15]; // anchos de columnas ajustados - nutriente primero, kcal al final
$totalWidth = array_sum($w);
$pageWidth = 210; // Ancho de página en mm
$leftMargin = ($pageWidth - $totalWidth) / 2; // Calcular margen izquierdo para centrar

$pdf->SetX($leftMargin);
$pdf->Cell($w[0], 8, utf8_decode('Comida'),      1, 0, 'C');
$pdf->Cell($w[1], 8, utf8_decode('Gramos'),      1, 0, 'C');
$pdf->Cell($w[2], 8, utf8_decode('kcal/100g'),   1, 0, 'C');
$pdf->Cell($w[3], 8, utf8_decode('Nutriente'),   1, 0, 'C');
$pdf->Cell($w[4], 8, utf8_decode('kcal'),        1, 1, 'C');

$pdf->SetFont('Arial', '', 8);

// Filas
foreach ($consumos as $r) {
    $nutriente = !empty($r['nutriente']) ? utf8_decode($r['nutriente']) : 'Sin nutriente';
    $pdf->SetX($leftMargin);
    $pdf->Cell($w[0], 6, utf8_decode(substr($r['comida'], 0, 20)),     1, 0, 'C');
    $pdf->Cell($w[1], 6, number_format($r['gramos'], 1),    1, 0, 'C');
    $pdf->Cell($w[2], 6, number_format($r['kcal_100g'], 0), 1, 0, 'C');
    $pdf->Cell($w[3], 6, substr($nutriente, 0, 25),         1, 0, 'C');
    $pdf->Cell($w[4], 6, number_format($r['kcal'], 0),      1, 1, 'C');
}

// Total de calorías
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetX($leftMargin);
$pdf->Cell($w[0], 8, '', 1, 0, 'C');
$pdf->Cell($w[1], 8, '', 1, 0, 'C');
$pdf->Cell($w[2], 8, '', 1, 0, 'C');
$pdf->Cell($w[3], 8, utf8_decode('TOTAL:'), 1, 0, 'C');
$pdf->Cell($w[4], 8, number_format($totalKcal, 0), 1, 1, 'C');

$pdf->Ln(8);

// =======================
//   GRÁFICA AL FINAL
// =======================

$yGraph = $pdf->GetY();

// Si la gráfica no cabe en la página actual, agregar una nueva página
if ($yGraph > 180) {
    $pdf->AddPage();
    $yGraph = $pdf->GetY();
}

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, utf8_decode('Gráfica de consumo por nutriente'), 0, 1, 'C');
$pdf->Ln(2);

$yGraph = $pdf->GetY();

// Insertar imagen del gráfico si existe
if (file_exists($graphFile)) {
    $pdf->Image($graphFile, 5, $yGraph, 200, 120);
}

// Salida del PDF
$pdf->Output('D', 'reporte_diario_' . $_SESSION['user']['nombre'] . '_' . $_SESSION['user']['apellidos'] . '_' . $hoy . '.pdf');
exit;
