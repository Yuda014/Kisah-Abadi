<?php
// === UBAH 4 BARIS INI SESUAI DATABASE DI HOSTING ===
define('DB_HOST','localhost');
define('DB_NAME','nama_database');
define('DB_USER','user_database');
define('DB_PASS','password_database');
// ===================================================
define('WA_NUMBER','6285724333781');
define('BRAND','KisahAbadi');
function db(){static $p;if(!$p){$p=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}return $p;}
