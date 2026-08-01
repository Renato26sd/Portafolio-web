<?php
/**
 * Página de reportes
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

require_once __DIR__ . '/../app/models/Reporte.php';
require_once __DIR__ . '/../app/models/Producto.php';

$reporteModel = new Reporte();
$productoModel = new Producto();

$fechaInicio = date('Y-m-01');
$fechaFin = date('Y-m-t');
$ventasPorFecha = $reporteModel->ventasPorFecha($fechaInicio, $fechaFin);
$productosSinStock = $productoModel->obtenerBajoStock();
$productosBajoStock = $productoModel->obtenerBajoStock();
$productosMasVendidos = $productoModel->obtenerMasVendidos(5);
$ganancias = ['total_ganancia' => 0];

require_once __DIR__ . '/../app/views/reportes/index.php';
