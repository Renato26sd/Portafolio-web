<?php
/**
 * Buscador de productos (AJAX)
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Session.php';

Session::start();

// Verificar autenticación
if (!Session::get('user_id')) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../app/models/Producto.php';

$productoModel = new Producto();
$busqueda = $_GET['q'] ?? '';

$productos = $productoModel->buscarParaVenta($busqueda);

header('Content-Type: application/json');
echo json_encode($productos);
