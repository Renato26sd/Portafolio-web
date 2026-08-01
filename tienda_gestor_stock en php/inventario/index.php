<?php
/**
 * Página de inventario
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Session.php';

Session::start();

// Verificar autenticación
if (!Session::get('user_id')) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../app/models/MovimientoInventario.php';

$movimientoModel = new MovimientoInventario();
$pagina = $_GET['pagina'] ?? 1;
$movimientos = $movimientoModel->obtenerTodos($pagina);
$total = $movimientoModel->contarTotal();
$totalPaginas = ceil($total / ITEMS_PER_PAGE);

require_once __DIR__ . '/../app/views/inventario/index.php';
