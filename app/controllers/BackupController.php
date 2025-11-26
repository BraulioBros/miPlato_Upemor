<?php
require_once __DIR__.'/../models/Backup.php';

class BackupController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new Backup();
    }

    // Descargar respaldo de la BD (genera y fuerza la descarga)
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

    // Restaura la BD usando el respaldo más reciente en app/config/backups
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
