<?php
/**
 * CONTROLADOR DE RESPALDOS (BACKUPS)
 * 
 * Maneja la creación y restauración de respaldos de la base de datos:
 * - Generar y descargar respaldos de la BD
 * - Restaurar la BD desde un respaldo anterior
 * 
 * Solo accesible por usuarios con rol 'admin'
 */

require_once __DIR__.'/../models/Backup.php';

class BackupController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new Backup();
    }

    /**
     * Genera un respaldo de la base de datos y lo descarga
     * 
     * Proceso:
     * 1. Verifica que el usuario sea admin
     * 2. Genera un archivo SQL con todos los datos de la BD
     * 3. Fuerza la descarga del archivo
     * 4. El archivo se elimina después de la descarga
     */
    public function descargar()
    {
        $this->requireRole('admin');

        $server   = DB_HOST;
        $user     = DB_USER;
        $password = DB_PASS;
        $db       = DB_NAME;

        try {
            $filePath = $this->model->backup_tables($server, $user, $password, $db);
        } catch (Exception $e) {
            redirect('admin', 'dashboard', ['error' => 'Error al generar el respaldo: ' . $e->getMessage()]);
        }

        if (!file_exists($filePath)) {
            redirect('admin', 'dashboard', ['error' => 'No se pudo encontrar el archivo de respaldo.']);
        }

        $nombreDescarga = basename($filePath);

        header("Content-Disposition: attachment; filename=\"{$nombreDescarga}\"");
        header("Content-Type: application/sql");
        header("Content-Length: " . filesize($filePath));
        readfile($filePath);
        exit;
    }

    /**
     * Restaura la base de datos desde el respaldo más reciente
     * 
     * Proceso:
     * 1. Busca todos los respaldos en app/config/backups/
     * 2. Selecciona el más reciente por nombre (formato: db-backup-YYYY-MM-DD_HH-MM-SS.sql)
     * 3. Restaura la BD completamente desde ese respaldo
     * 4. Redirige al dashboard con mensaje de éxito o error
     */
    public function restaurar()
    {
        $this->requireRole('admin');

        $dir = __DIR__ . '/../config/backups';
        $pattern = $dir . '/db-backup-*.sql';
        $files = glob($pattern);

        if (!$files) {
            redirect('admin', 'dashboard', ['error' => 'No hay archivos de respaldo en app/config/backups.']);
        }

        // Tomar el más reciente por nombre
        rsort($files);
        $ultimo = $files[0];

        $ok = $this->model->restoreFromPath(DB_HOST, DB_USER, DB_PASS, DB_NAME, $ultimo);

        if ($ok) {
            redirect('admin', 'dashboard', [
                'ok' => 'Base de datos restaurada desde: ' . basename($ultimo)
            ]);
        } else {
            redirect('admin', 'dashboard', [
                'error' => 'Ocurrió un error al restaurar la base de datos.'
            ]);
        }
    }
}
