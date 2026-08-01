<?php
/**
 * Página para generar ticket de venta
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/Session.php';
require_once __DIR__ . '/../app/models/Venta.php';

Session::start();

// Verificar autenticación
if (!Session::isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die('ID de venta no proporcionado');
}

// Obtener venta y detalles
$ventaModel = new Venta();
$venta = $ventaModel->obtenerPorId($id);
$detalles = $ventaModel->obtenerDetalles($id);

if (!$venta) {
    die('Venta no encontrada');
}

// Procesar nombre del cliente
if ($venta['cliente_nombres'] && $venta['cliente_apellidos']) {
    $venta['nombre_cliente'] = $venta['cliente_nombres'] . ' ' . $venta['cliente_apellidos'];
} else {
    $venta['nombre_cliente'] = null;
}

// Cargar vista de ticket
require_once __DIR__ . '/../app/views/ventas/ticket.php';
