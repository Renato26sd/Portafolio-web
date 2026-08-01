<?php
/**
 * Página de crear venta
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/Session.php';
require_once __DIR__ . '/../app/models/Venta.php';
require_once __DIR__ . '/../app/models/Cliente.php';
require_once __DIR__ . '/../app/models/Producto.php';

Session::start();

// Verificar autenticación
if (!Session::isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar método de pago
    $metodoPago = $_POST['metodo_pago'] ?? '';
    if (!in_array($metodoPago, ['efectivo', 'yape', 'plin', 'tarjeta'])) {
        Session::setFlash('error', 'Método de pago inválido');
        header('Location: /ventas/crear.php');
        exit;
    }
    
    // Validar detalles
    if (empty($_POST['detalles']) || !is_array($_POST['detalles'])) {
        Session::setFlash('error', 'Debe agregar al menos un producto a la venta');
        header('Location: /ventas/crear.php');
        exit;
    }
    
    // Calcular totales
    $subtotal = 0;
    foreach ($_POST['detalles'] as $detalle) {
        $subtotal += $detalle['subtotal'];
    }
    
    $igv = $subtotal * IGV_PERCENTAGE;
    $total = $subtotal + $igv;
    
    // Aplicar descuento si existe
    $descuento = $_POST['descuento'] ?? 0;
    $total -= $descuento;
    
    $datosVenta = [
        'id_usuario' => Session::getUserId(),
        'id_cliente' => $_POST['id_cliente'] ?? null,
        'subtotal' => $subtotal,
        'igv' => $igv,
        'total' => $total,
        'descuento' => $descuento,
        'metodo_pago' => $metodoPago,
        'estado' => 'completada',
        'observaciones' => $_POST['observaciones'] ?? null,
        'detalles' => $_POST['detalles']
    ];
    
    $ventaModel = new Venta();
    $idVenta = $ventaModel->crear($datosVenta);
    
    if ($idVenta) {
        Session::setFlash('success', 'Venta registrada exitosamente');
        header('Location: /ventas/ver.php?id=' . $idVenta);
    } else {
        Session::setFlash('error', 'Error al registrar venta');
        header('Location: /ventas/crear.php');
    }
    exit;
}

// GET: Mostrar formulario
$clienteModel = new Cliente();
$clientes = $clienteModel->obtenerTodos();

$pageTitle = 'Nueva Venta';
require_once __DIR__ . '/../app/views/ventas/crear.php';
