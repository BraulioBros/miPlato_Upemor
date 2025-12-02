<?php
/**
 * MODELO BACKUP
 * 
 * Maneja la creación y restauración de respaldos de la base de datos.
 * Los respaldos se guardan como archivos SQL en app/config/backups/
 * 
 * Funcionalidad:
 * - backup_tables(): Genera un dump SQL completo de la BD
 * - restoreFromPath(): Restaura la BD desde un archivo SQL
 */
class Backup extends Model
{
    /**
     * Genera un respaldo completo de la base de datos
     * 
     * Proceso:
     * 1. Conexión directa con mysqli (no usa PDO)
     * 2. Exporta estructura de todas las tablas (CREATE TABLE)
     * 3. Exporta todos los datos (INSERT statements)
     * 4. Genera archivo SQL con timestamp: db-backup-YYYY-MM-DD_HH-MM-SS.sql
     * 5. Guarda en app/config/backups/
     * 6. Desactiva FKs al principio para poder restaurar
     * 
     * @param string $host - Host de BD (localhost)
     * @param string $user - Usuario de BD
     * @param string $pass - Contraseña de BD
     * @param string $name - Nombre de la BD
     * @param string $tables - Tablas a respaldar ('*' para todas)
     * @return string - Ruta completa del archivo .sql generado
     * @throws Exception - Si falla la conexión
     */
    public function backup_tables($host, $user, $pass, $name, $tables = '*')
    {
        // Crea conexión mysqli
        $link = new mysqli($host, $user, $pass, $name);
        if ($link->connect_error) {
            throw new Exception("Error de conexión para respaldo: " . $link->connect_error);
        }

        // Inicia el contenido del archivo SQL desactivando FKs
        $return = "SET FOREIGN_KEY_CHECKS=0;\n";

        // Obtener todas las tablas si se pasan '*'
        if ($tables == '*') {
            $tables = [];
            $result = $link->query('SHOW TABLES');
            while ($row = mysqli_fetch_row($result)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }

        // Procesa cada tabla
        foreach ($tables as $table) {
            $result = $link->query('SELECT * FROM ' . $table);
            $num_fields = $result->field_count;

            // Obtiene el CREATE TABLE y lo agrega al SQL
            $row2 = mysqli_fetch_row($link->query('SHOW CREATE TABLE ' . $table));
            $return .= "\n\nDROP TABLE IF EXISTS `$table`;\n";
            $return .= $row2[1] . ";\n\n";

            // Genera INSERT statements para todos los registros
            for ($i = 0; $i < $num_fields; $i++) {
                while ($row = mysqli_fetch_row($result)) {
                    $return .= 'INSERT INTO `' . $table . '` VALUES(';
                    for ($j = 0; $j < $num_fields; $j++) {
                        // Escapa caracteres especiales
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace("/\n/", "\\n", $row[$j]);
                        if (isset($row[$j])) {
                            $return .= '"' . $row[$j] . '"';
                        } else {
                            $return .= '""';
                        }
                        if ($j < ($num_fields - 1)) {
                            $return .= ',';
                        }
                    }
                    $return .= ");\n";
                }
            }
            $return .= "\n\n";
        }

        // Finaliza reactivando FKs
        $return .= "SET FOREIGN_KEY_CHECKS=1;\n";

        // Crea la carpeta de respaldos si no existe
        $fecha = date("Y-m-d_H-i-s");
        $dirBackups = __DIR__ . '/../config/backups';

        if (!is_dir($dirBackups)) {
            mkdir($dirBackups, 0777, true);
        }

        // Guarda el archivo SQL
        $filePath = $dirBackups . '/db-backup-' . $fecha . '.sql';
        file_put_contents($filePath, $return);

        return $filePath;
    }

    /**
     * Restaura la base de datos desde un archivo SQL
     * 
     * Proceso:
     * 1. Verifica que el archivo exista
     * 2. Lee el contenido del archivo SQL
     * 3. Conexión directa con mysqli
     * 4. Desactiva FKs para evitar errores de constraint
     * 5. Ejecuta todos los statements SQL
     * 6. Reactiva FKs
     * 
     * Notas:
     * - El archivo SQL debe estar generado por backup_tables()
     * - Sobrescribe todos los datos existentes
     * - Usualmente se llama desde BackupController::restaurar()
     * 
     * @param string $host - Host de BD
     * @param string $user - Usuario de BD
     * @param string $pass - Contraseña de BD
     * @param string $name - Nombre de la BD
     * @param string $ruta - Ruta completa del archivo .sql
     * @return bool - True si se restauró exitosamente, false si falló
     */
    public function restoreFromPath($host, $user, $pass, $name, $ruta)
    {
        // Verifica que el archivo exista
        if (!file_exists($ruta)) {
            return false;
        }

        // Lee el contenido del archivo SQL
        $sql = file_get_contents($ruta);
        if ($sql === false) {
            return false;
        }

        // Crea conexión mysqli
        $link = new mysqli($host, $user, $pass, $name);
        if ($link->connect_error) {
            return false;
        }

        // Desactiva FKs para poder hacer DROP TABLE aunque haya relaciones
        $link->query('SET FOREIGN_KEY_CHECKS=0');

        $ok = false;

        // Ejecuta todos los statements del SQL
        if ($link->multi_query($sql)) {
            // Procesa todos los resultados
            do {
                if ($result = $link->store_result()) {
                    $result->free();
                }
            } while ($link->more_results() && $link->next_result());

            $ok = true;
        }

        // Reactiva FKs
        $link->query('SET FOREIGN_KEY_CHECKS=1');

        return $ok;
    }
}
