<?php
/**
 * Script para ejecutar el script SQL de la base de datos
 */

require_once __DIR__ . '/config/config.php';

echo "<h1>Configuración de Base de Datos</h1>";

try {
    // Leer el script SQL
    $sqlFile = __DIR__ . '/database/script.sql';
    if (!file_exists($sqlFile)) {
        die("<p style='color: red;'>✗ No se encontró el archivo: $sqlFile</p>");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Eliminar los DELIMITER y dividir el script en consultas individuales
    $sql = str_replace('DELIMITER //', '', $sql);
    $sql = str_replace('DELIMITER ;', '', $sql);
    $sql = str_replace('//', ';', $sql);
    
    // Dividir por punto y coma pero ignorar los que están dentro de procedimientos
    $queries = [];
    $currentQuery = '';
    $inProcedure = false;
    
    foreach (explode(';', $sql) as $part) {
        $part = trim($part);
        if (empty($part)) continue;
        
        if (strpos(strtoupper($part), 'CREATE PROCEDURE') !== false || 
            strpos(strtoupper($part), 'CREATE TRIGGER') !== false) {
            $inProcedure = true;
        }
        
        if ($inProcedure) {
            $currentQuery .= $part . ';';
            if (strpos(strtoupper($part), 'END') !== false) {
                $inProcedure = false;
                $queries[] = $currentQuery;
                $currentQuery = '';
            }
        } else {
            $queries[] = $part;
        }
    }
    
    echo "<p>Ejecutando " . count($queries) . " consultas...</p>";
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (empty($query)) continue;
        
        try {
            $pdo->exec($query);
            $successCount++;
        } catch (PDOException $e) {
            $errorCount++;
            echo "<p style='color: orange;'>⚠ Error (puede ser normal si ya existe): " . substr($e->getMessage(), 0, 100) . "...</p>";
        }
    }
    
    echo "<h2>Resumen:</h2>";
    echo "<p style='color: green;'>✓ Consultas exitosas: $successCount</p>";
    echo "<p style='color: orange;'>⚠ Errores (esperados si ya existe): $errorCount</p>";
    
    // Verificar si el usuario admin fue creado
    $sql = "SELECT COUNT(*) FROM usuarios WHERE usuario = 'admin'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        echo "<p style='color: green; font-weight: bold;'>✓ Usuario admin creado correctamente</p>";
        echo "<p><a href='/login.php'>Ir al login</a> | Usuario: admin | Contraseña: admin123</p>";
    } else {
        echo "<p style='color: red;'>✗ El usuario admin no fue creado</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}
?>
