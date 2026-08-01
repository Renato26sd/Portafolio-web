<?php
/**
 * Página de registrar movimiento de inventario
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

require_once __DIR__ . '/../app/models/Producto.php';

$productoModel = new Producto();
$productos = $productoModel->obtenerTodos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lógica para guardar movimiento de inventario
    $id_producto = $_POST['id_producto'] ?? null;
    $tipo_movimiento = $_POST['tipo_movimiento'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;
    $motivo = $_POST['motivo'] ?? null;
    
    if (!$id_producto || !$tipo_movimiento || !$cantidad) {
        Session::setFlash('error', 'Complete todos los campos requeridos');
        header('Location: /inventario/registrar.php');
        exit;
    }
    
    // Aquí iría la lógica para registrar el movimiento
    // Por ahora solo mostramos la vista
}

require_once __DIR__ . '/../app/views/inventario/registrar.php';
