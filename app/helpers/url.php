<?php
/**
 * FUNCIONES HELPER DE URLs Y RUTAS
 * 
 * Archivo con funciones auxiliares para:
 * - Construcción de rutas de assets (CSS, JS, imágenes)
 * - Redirecciones dentro de la aplicación
 * - Gestión de parámetros GET
 */

/**
 * Construye la ruta hacia un asset (CSS, JavaScript, imagen)
 * 
 * Convierte una ruta relativa en una ruta completa dentro de la carpeta 'public'
 * 
 * Ejemplos de uso:
 *   asset('css/app.css')      -> 'public/css/app.css'
 *   asset('/css/app.css')     -> 'public/css/app.css'
 *   asset('img/logo.png')     -> 'public/img/logo.png'
 * 
 * @param string $p - Ruta relativa del asset (con o sin diagonal inicial)
 * @return string - Ruta completa del asset dentro de public/
 */
function asset($p){
    return 'public/'.ltrim($p,'/');
}

/**
 * Redirige a una ruta específica dentro de la aplicación
 * 
 * Construye una URL con parámetros GET y redirige el navegador
 * Utiliza el sistema de enrutamiento basado en controlador y acción
 * 
 * Proceso:
 * 1. Toma el nombre del controlador y acción
 * 2. Agrega parámetros adicionales opcionales
 * 3. Convierte todo a query string (URL encoding)
 * 4. Envía un header Location y detiene la ejecución
 * 
 * Ejemplos de uso:
 *   redirect('admin', 'dashboard')
 *      -> Navega a: index.php?controller=admin&action=dashboard
 * 
 *   redirect('estudiante', 'consumoAdd', ['error' => 'Datos incompletos'])
 *      -> Navega a: index.php?controller=estudiante&action=consumoAdd&error=Datos+incompletos
 * 
 *   redirect('auth', 'login', ['ok' => 'Cuenta creada'])
 *      -> Navega a: index.php?controller=auth&action=login&ok=Cuenta+creada
 * 
 * @param string $c - Nombre del controlador (ej: 'admin', 'estudiante', 'nutriologo')
 * @param string $a - Nombre de la acción/método (ej: 'dashboard', 'usuarios', 'consumoAdd')
 * @param array $params - Array asociativo con parámetros adicionales (opcional)
 *                        Ejemplos: ['error' => 'mensaje'], ['ok' => 'éxito'], ['id' => 5]
 * @return void - No retorna, redirige y detiene la ejecución
 */
function redirect($c,$a,$params=[]){
    // Combina los parámetros principales con los parámetros adicionales
    $q = http_build_query(array_merge(
        ['controller'=>$c,'action'=>$a],
        $params
    ));
    
    // Envía el header de redirección
    header('Location: index.php?'.$q);
    
    // Detiene la ejecución del script
    exit;
}
