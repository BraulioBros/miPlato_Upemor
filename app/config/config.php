<?php
date_default_timezone_set('America/Mexico_City');
define('DB_HOST','localhost');define('DB_NAME','miplato_upemor');define('DB_USER','root');define('DB_PASS','');define('BASE_URL','');
function nocache_headers_safe(){header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0",false);header("Pragma: no-cache");}
