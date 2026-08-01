<?php

// Cargar variables de entorno desde .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    $host = $env['DB_HOST'] ?? 'localhost';
    $dbname = $env['DB_NAME'] ?? 'bodega_stock'; // lo cambias dependediendo el nombre de tu base de datos
    $username = $env['DB_USER'] ?? 'tu usuarios'; // lo cambias de acuerdo a tu configuración
    $password = $env['DB_PASS'] ?? '123456'; // lo cambias de acuerdo a tu configuración
    $charset = $env['DB_CHARSET'] ?? 'utf8mb4';
} else {
    // Valores por defecto si .env no existe
    $host = 'localhost';
    $dbname = 'bodega_stock';
    $username = 'tu usuarios';
    $password = '123456';
    $charset = 'utf8mb4';
}

// Definir constantes de base de datos
define('DB_HOST', $host);
define('DB_NAME', $dbname);
define('DB_USER', $username);
define('DB_PASS', $password);
define('DB_CHARSET', $charset);

// Definir constantes de sesión
define('SESSION_NAME', $env['SESSION_NAME'] ?? 'bodega_session');
define('SESSION_LIFETIME', $env['SESSION_LIFETIME'] ?? 7200);

// Definir constantes del sitio
define('SITE_NAME', $env['SITE_NAME'] ?? 'bodega_stock');
define('SITE_URL', $env['SITE_URL'] ?? 'http://localhost/bodega-system');

// Definir constantes de seguridad
define('HASH_ALGORITHM', $env['HASH_ALGORITHM'] ?? 'sha256');
define('MAX_LOGIN_ATTEMPTS', $env['MAX_LOGIN_ATTEMPTS'] ?? 5);
define('LOGIN_LOCKOUT_TIME', $env['LOGIN_LOCKOUT_TIME'] ?? 900);

// Definir constantes de negocio
define('IGV_PERCENTAGE', $env['IGV_PERCENTAGE'] ?? 18);
define('CURRENCY_SYMBOL', $env['CURRENCY_SYMBOL'] ?? 'S/');
define('ITEMS_PER_PAGE', $env['ITEMS_PER_PAGE'] ?? 10);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
