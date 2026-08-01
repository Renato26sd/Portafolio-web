<?php
/**
 * Página de proveedores
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

require_once __DIR__ . '/../app/models/Proveedor.php';

$proveedorModel = new Proveedor();
$proveedores = $proveedorModel->obtenerTodos();

require_once __DIR__ . '/../app/views/proveedores/index.php';
