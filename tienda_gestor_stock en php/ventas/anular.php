<?php
/**
 * Página para anular venta
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
    Session::setFlash('error', 'ID de venta no proporcionado');
    header('Location: /ventas/index.php');
    exit;
}

// Anular venta
$ventaModel = new Venta();
$resultado = $ventaModel->anular($id);

if ($resultado) {
    Session::setFlash('success', 'Venta anulada exitosamente');
} else {
    Session::setFlash('error', 'Error al anular venta');
}

header('Location: /ventas/index.php');
exit;
