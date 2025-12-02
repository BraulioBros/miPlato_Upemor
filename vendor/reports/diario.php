<?php
/**
 * REPORTE DIARIO EN PDF (VENDOR/REPORTS)
 * 
 * Genera un PDF profesional con el reporte de consumo del día actual.
 * Incluye:
 * - Datos personales del estudiante
 * - Tabla detallada de consumo
 * - Gráfica de pastel (nutrientes) generada con GD Library
 * 
 * Librerías utilizadas:
 * - FPDF: Generación de PDFs (escrito desde cero)
 * - GD Library: Generación de gráficas (pastel de nutrientes)
 * 
 * Proceso:
 * 1. Obtiene datos del estudiante desde EstudianteDetalle
 * 2. Obtiene consumos del día con Consumo::detallePorFecha()
 * 3. Agrupa por nutriente y suma calorías
 * 4. Genera gráfica PNG con GD Library
 * 5. Crea PDF con FPDF e inserta gráfica
 * 6. Descarga automática del PDF al navegador
 * 
 * Variables de sesión requeridas:
 * - $_SESSION['user']['id'] - ID del usuario estudiante
 * - $_SESSION['user']['nombre'] - Nombre del estudiante
 * - $_SESSION['user']['apellidos'] - Apellidos del estudiante
 */

// ========== CARGA DE MODELOS Y LIBRERÍAS ==========
// Modelos para acceder a datos de BD
require_once __DIR__ . '/../../app/models/EstudianteDetalle.php';
require_once __DIR__ . '/../../app/models/Consumo.php';

// Librerías externas para generación de reportes
require_once __DIR__ . '/../../public/libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../public/libraries/phplot/phplot.php';

// ========== DATOS DEL ESTUDIANTE Y CONSUMO ==========
// Obtiene información personal del estudiante desde sesión y BD

$uid            = $_SESSION['user']['id'];
$nombreCompleto = $_SESSION['user']['nombre'] . ' ' . ($_SESSION['user']['apellidos'] ?? '');

// Cargar detalles antropométricos del estudiante
$det = (new EstudianteDetalle())->findByUserId($uid);

// Datos personales
$edad = 0;
if (!empty($det['fecha_nacimiento'])) {
    $fn   = new DateTime($det['fecha_nacimiento']);
    $edad = (new DateTime())->diff($fn)->y;
}
$peso   = isset($det['peso'])   ? $det['peso'] . ' kg'   : 'N/D';
$altura = isset($det['altura']) ? $det['altura'] . ' cm' : 'N/D';

// Fecha actual del reporte
$hoy = date('Y-m-d');

// Obtener todos los consumos del día seleccionado
$consumos = (new Consumo())->detallePorFecha($uid, $hoy);

// ========== CÁLCULO DE TOTALES POR NUTRIENTE ==========
// Agrupa calorías por tipo de nutriente para la gráfica de pastel

$nutrientesPorDia = [];
$totalKcal = 0;

// Sumar calorías por nutriente
foreach ($consumos as $r) {
    $nutriente = !empty($r['nutriente']) ? $r['nutriente'] : 'Sin nutriente';
    
    if (!isset($nutrientesPorDia[$nutriente])) {
        $nutrientesPorDia[$nutriente] = 0;
    }
    $nutrientesPorDia[$nutriente] += (float) $r['kcal'];
    $totalKcal += (float) $r['kcal'];
}

// Si no hay nutrientes registrados, mostrar mensaje en gráfica
if (empty($nutrientesPorDia)) {
    $nutrientesPorDia['Sin datos'] = 1;
}

// ========== GENERACIÓN DE GRÁFICA CON GD LIBRARY ==========
// Crea una gráfica de pastel PNG con la distribución de nutrientes

$graphsDir = __DIR__ . '/../../public/media/graphs';
if (!is_dir($graphsDir)) {
    mkdir($graphsDir, 0777, true);
}

// Archivo PNG temporal para la gráfica
$graphFile = $graphsDir . '/consumo_diario_' . date('Y-m-d-H-i-s') . '.png';

// Paleta de colores RGB para la gráfica
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

// Crear imagen blanca (1000x600 píxeles)
$width = 1000;
$height = 600;
$image = imagecreatetruecolor($width, $height);
$bgColor = imagecolorallocate($image, 255, 255, 255);
imagefill($image, 0, 0, $bgColor);

// Parámetros para la gráfica de pastel
$centerX = $width / 2 - 100;
$centerY = $height / 2;
$radius = 180;

// Calcular ángulos para cada segmento
$totalKcalTemp = array_sum($nutrientesPorDia);
$startAngle = 0;
$colorIdx = 0;
$legendY = 50;

// Dibujar título en la gráfica
$black = imagecolorallocate($image, 0, 0, 0);
imagestring($image, 5, 250, 20, 'Consumo diario de kcal por nutriente - ' . $hoy, $black);

// Dibujar cada segmento del pastel
foreach ($nutrientesPorDia as $nutriente => $kcal) {
    $percentage = ($kcal / $totalKcalTemp) * 100;
    $angle = ($kcal / $totalKcalTemp) * 360;
    
    $rgb = $colorList[$colorIdx % count($colorList)];
    $segmentColor = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
    
    // Dibujar segmento de la gráfica
    imagefilledarc($image, $centerX, $centerY, $radius * 2, $radius * 2, $startAngle, $startAngle + $angle, $segmentColor, IMG_ARC_PIE);
    
    // Dibujar leyenda a la derecha (cuadro de color + nombre)
    $legendColor = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
    imagefilledrectangle($image, 750, $legendY, 765, $legendY + 10, $legendColor);
    
    // Limpiar caracteres especiales del nutriente (compatibilidad con GD)
    $nutrienteLimpio = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nutriente);
    $nutrienteLimpio = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $nutrienteLimpio);
    imagestring($image, 3, 770, $legendY - 2, $nutrienteLimpio . ' (' . round($percentage, 1) . '%)', $black);
    
    $startAngle += $angle;
    $colorIdx++;
    $legendY += 18;
}

// Guardar imagen PNG y liberar memoria
imagepng($image, $graphFile);
imagedestroy($image);

// ========== GENERACIÓN DEL PDF CON FPDF ==========
// Crea documento PDF profesional con datos y gráficas

$pdf = new FPDF();
$pdf->AddPage();

// ========== ENCABEZADO Y DATOS DEL ESTUDIANTE ==========
// Título principal
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Reporte diario de consumo'), 0, 1, 'C');

// Datos personales del estudiante
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 7, utf8_decode("Estudiante: {$nombreCompleto}"), 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode("Edad: {$edad}   Peso: {$peso}   Altura: {$altura}"), 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode("Fecha: {$hoy}"), 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode("Total de kcal consumidas: {$totalKcal}"), 0, 1, 'L');

$pdf->Ln(4);

// ========== TABLA DE CONSUMO DETALLADO ==========
// Encabezado de la tabla
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, utf8_decode('Detalle de consumo diario'), 0, 1, 'C');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 9);
$w = [60, 20, 25, 45, 15]; // anchos de columnas
$totalWidth = array_sum($w);
$pageWidth = 210; // Ancho de página en mm
$leftMargin = ($pageWidth - $totalWidth) / 2; // Centrar tabla

$pdf->SetX($leftMargin);
$pdf->Cell($w[0], 8, utf8_decode('Comida'),      1, 0, 'C');
$pdf->Cell($w[1], 8, utf8_decode('Gramos'),      1, 0, 'C');
$pdf->Cell($w[2], 8, utf8_decode('kcal/100g'),   1, 0, 'C');
$pdf->Cell($w[3], 8, utf8_decode('Nutriente'),   1, 0, 'C');
$pdf->Cell($w[4], 8, utf8_decode('kcal'),        1, 1, 'C');

$pdf->SetFont('Arial', '', 8);

// Filas de la tabla (cada consumo)
foreach ($consumos as $r) {
    $nutriente = !empty($r['nutriente']) ? utf8_decode($r['nutriente']) : 'Sin nutriente';
    $pdf->SetX($leftMargin);
    $pdf->Cell($w[0], 6, utf8_decode(substr($r['comida'], 0, 20)),     1, 0, 'C');
    $pdf->Cell($w[1], 6, number_format($r['gramos'], 1),    1, 0, 'C');
    $pdf->Cell($w[2], 6, number_format($r['kcal_100g'], 0), 1, 0, 'C');
    $pdf->Cell($w[3], 6, substr($nutriente, 0, 25),         1, 0, 'C');
    $pdf->Cell($w[4], 6, number_format($r['kcal'], 0),      1, 1, 'C');
}

// Fila de totales
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetX($leftMargin);
$pdf->Cell($w[0], 8, '', 1, 0, 'C');
$pdf->Cell($w[1], 8, '', 1, 0, 'C');
$pdf->Cell($w[2], 8, '', 1, 0, 'C');
$pdf->Cell($w[3], 8, utf8_decode('TOTAL:'), 1, 0, 'C');
$pdf->Cell($w[4], 8, number_format($totalKcal, 0), 1, 1, 'C');

$pdf->Ln(8);

// ========== GRÁFICA INSERIDA EN PDF ==========
$yGraph = $pdf->GetY();

// Si no hay espacio, crear nueva página
if ($yGraph > 180) {
    $pdf->AddPage();
    $yGraph = $pdf->GetY();
}

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, utf8_decode('Gráfica de consumo por nutriente'), 0, 1, 'C');
$pdf->Ln(2);

$yGraph = $pdf->GetY();

// Insertar imagen PNG generada con GD Library
if (file_exists($graphFile)) {
    $pdf->Image($graphFile, 5, $yGraph, 200, 120);
}

// Descargar PDF automáticamente al navegador
$pdf->Output('D', 'reporte_diario_' . $_SESSION['user']['nombre'] . '_' . $_SESSION['user']['apellidos'] . '_' . $hoy . '.pdf');
exit;
