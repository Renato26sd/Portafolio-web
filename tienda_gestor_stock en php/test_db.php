<?php
/**
 * Script de prueba para verificar la conexión a la base de datos
 */

require_once __DIR__ . '/config/config.php';

echo "<h1>Prueba de Conexión a Base de Datos</h1>";

try {
    // Probar conexión
    echo "<p style='color: green;'>✓ Conexión exitosa a la base de datos: <strong>$dbname</strong></p>";
    
    // Mostrar información del servidor
    echo "<h2>Información del Servidor</h2>";
    echo "<ul>";
    echo "<li>Host: $host</li>";
    echo "<li>Base de datos: $dbname</li>";
    echo "<li>Usuario: $username</li>";
    echo "<li>Charset: $charset</li>";
    echo "</ul>";
    
    // Listar todas las tablas
    echo "<h2>Tablas en la Base de Datos</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    
    if (count($tables) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>Nombre de Tabla</th><th>Registros</th></tr>";
        
        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            $countStmt = $pdo->query("SELECT COUNT(*) FROM `$tableName`");
            $count = $countStmt->fetchColumn();
            echo "<tr><td>$tableName</td><td>$count</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>✗ No se encontraron tablas en la base de datos</p>";
    }
    
    // Probar una consulta simple
    echo "<h2>Prueba de Consulta</h2>";
    $stmt = $pdo->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    echo "<p>Versión de MySQL: <strong>{$version['version']}</strong></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error de conexión: " . $e->getMessage() . "</p>";
}
?>
