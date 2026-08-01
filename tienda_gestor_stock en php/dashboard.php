<?php
/**
 * Página del dashboard
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/Session.php';
require_once __DIR__ . '/app/models/Reporte.php';
require_once __DIR__ . '/app/models/Producto.php';

Session::start();

// Verificar autenticación
if (!Session::isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

// Obtener datos del dashboard
$reporteModel = new Reporte();
$productoModel = new Producto();

$resumen = $reporteModel->resumenDashboard();
$ultimasVentas = $reporteModel->ultimasVentas(5);
$productosBajoStock = $productoModel->obtenerBajoStock();
$productosMasVendidos = $productoModel->obtenerMasVendidos(5);

// Datos para gráficas
$ventasPorMetodoPago = $reporteModel->ventasPorMetodoPago(
    date('Y-m-01'),
    date('Y-m-t')
);

$ventasPorVendedor = $reporteModel->ventasPorVendedor(
    date('Y-m-01'),
    date('Y-m-t')
);

$pageTitle = 'Dashboard';
require_once __DIR__ . '/app/views/dashboard/index.php';
