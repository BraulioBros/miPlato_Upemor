<?php
/**
 * ARCHIVO DE CONFIGURACIÓN
 * 
 * Contiene:
 * - Configuración de zona horaria
 * - Credenciales de base de datos
 * - URLs base de la aplicación
 * - Funciones auxiliares de seguridad
 */

/**
 * Establece la zona horaria de la aplicación
 * 
 * Se usa para que todas las fechas y horas registradas en la BD
 * correspondan a la zona horaria de México
 */
date_default_timezone_set('America/Mexico_City');

/**
 * CONFIGURACIÓN DE BASE DE DATOS
 * 
 * DB_HOST - Servidor donde está hospedada la BD (localhost = máquina local)
 * DB_NAME - Nombre de la base de datos (miplato_upemor)
 * DB_USER - Usuario de acceso a la BD (root = usuario por defecto de XAMPP)
 * DB_PASS - Contraseña de acceso (vacía por defecto en XAMPP local)
 */
define('DB_HOST','localhost');
define('DB_NAME','miplato_upemor');
define('DB_USER','root');
define('DB_PASS','');

/**
 * URL BASE DE LA APLICACIÓN
 * 
 * Se usa para construir URLs completas en redirecciones y enlaces
 * Ejemplo: 'http://localhost/Estancia' o 'https://midominio.com'
 * Dejar vacío si se ejecuta en raíz del servidor
 */
define('BASE_URL','');

/**
 * Deshabilita el cacheo de páginas en el navegador
 * 
 * Utilidad:
 * - Asegura que el navegador SIEMPRE obtenga la última versión del servidor
 * - Evita que usuarios vean páginas desactualizadas después de logout
 * - Importante para seguridad en aplicaciones autenticadas
 * 
 * Proceso:
 * 1. Cache-Control: no-store -> No guardar en caché
 * 2. Cache-Control: no-cache -> Validar con servidor antes de usar
 * 3. Cache-Control: must-revalidate -> Obligatorio revalidar
 * 4. Pragma: no-cache -> Compatibilidad con navegadores antiguos
 * 
 * Uso en código:
 *   nocache_headers_safe();  // Llamar al inicio de cada página autenticada
 */
function nocache_headers_safe(){
    // Indica que no debe almacenarse en caché
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    
    // Validación adicional para proxies y navegadores
    header("Cache-Control: post-check=0, pre-check=0",false);
    
    // Compatibilidad con navegadores antiguos (HTTP/1.0)
    header("Pragma: no-cache");
}
