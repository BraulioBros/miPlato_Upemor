<?php
class Database{private static $c=null;public static function getConnection(){if(self::$c===null){$dsn='mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';self::$c=new PDO($dsn,DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}return self::$c;}}
