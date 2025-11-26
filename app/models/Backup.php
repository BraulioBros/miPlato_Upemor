<?php
class Backup extends Model
{
    /**
     * Genera backup de todas las tablas y lo guarda en app/config/backups
     * Devuelve la ruta completa del archivo .sql
     */
    public function backup_tables($host, $user, $pass, $name, $tables = '*')
    {
        $link = new mysqli($host, $user, $pass, $name);
        if ($link->connect_error) {
            throw new Exception("Error de conexión para respaldo: " . $link->connect_error);
        }

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

        foreach ($tables as $table) {
            $result = $link->query('SELECT * FROM ' . $table);
            $num_fields = $result->field_count;

            // CREATE + DROP TABLE
            $row2 = mysqli_fetch_row($link->query('SHOW CREATE TABLE ' . $table));
            $return .= "\n\nDROP TABLE IF EXISTS `$table`;\n";
            $return .= $row2[1] . ";\n\n";

            // INSERTs
            for ($i = 0; $i < $num_fields; $i++) {
                while ($row = mysqli_fetch_row($result)) {
                    $return .= 'INSERT INTO `' . $table . '` VALUES(';
                    for ($j = 0; $j < $num_fields; $j++) {
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

        $return .= "SET FOREIGN_KEY_CHECKS=1;\n";

        // Ruta de backups
        $fecha = date("Y-m-d_H-i-s");
        $dirBackups = __DIR__ . '/../config/backups';

        if (!is_dir($dirBackups)) {
            mkdir($dirBackups, 0777, true);
        }

        $filePath = $dirBackups . '/db-backup-' . $fecha . '.sql';
        file_put_contents($filePath, $return);

        return $filePath;
    }

    /**
     * Restaura BD desde un archivo .sql
     */
    public function restoreFromPath($host, $user, $pass, $name, $ruta)
    {
        if (!file_exists($ruta)) {
            return false;
        }

        $sql = file_get_contents($ruta);
        if ($sql === false) {
            return false;
        }

        $link = new mysqli($host, $user, $pass, $name);
        if ($link->connect_error) {
            return false;
        }

        // DESACTIVAR FKs para poder hacer DROP TABLE aunque haya relaciones
        $link->query('SET FOREIGN_KEY_CHECKS=0');

        $ok = false;

        if ($link->multi_query($sql)) {
            do {
                if ($result = $link->store_result()) {
                    $result->free();
                }
            } while ($link->more_results() && $link->next_result());

            $ok = true;
        }

        // REACTIVAR FKs
        $link->query('SET FOREIGN_KEY_CHECKS=1');

        return $ok;
    }
}
