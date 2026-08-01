<?php
/**
 * Página de crear producto
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
require_once __DIR__ . '/../app/models/Categoria.php';
require_once __DIR__ . '/../app/models/Proveedor.php';

$productoModel = new Producto();
$categoriaModel = new Categoria();
$proveedorModel = new Proveedor();

$categorias = $categoriaModel->obtenerTodos();
$proveedores = $proveedorModel->obtenerTodos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lógica para guardar producto
    $datos = [
        'codigo' => $_POST['codigo'] ?? null,
        'nombre' => $_POST['nombre'] ?? null,
        'id_categoria' => $_POST['id_categoria'] ?? null,
        'id_proveedor' => $_POST['id_proveedor'] ?? null,
        'precio_compra' => $_POST['precio_compra'] ?? null,
        'precio_venta' => $_POST['precio_venta'] ?? null,
        'stock' => $_POST['stock'] ?? null,
        'stock_minimo' => $_POST['stock_minimo'] ?? null,
    ];
    
    if (!$datos['codigo'] || !$datos['nombre'] || !$datos['precio_venta']) {
        Session::setFlash('error', 'Complete los campos requeridos');
        header('Location: /productos/crear.php');
        exit;
    }
    
    $idProducto = $productoModel->crear($datos);
    
    if ($idProducto) {
        Session::setFlash('success', 'Producto creado exitosamente');
        header('Location: /productos/index.php');
    } else {
        Session::setFlash('error', 'Error al crear producto');
        header('Location: /productos/crear.php');
    }
    exit;
}

require_once __DIR__ . '/../app/views/productos/crear.php';
