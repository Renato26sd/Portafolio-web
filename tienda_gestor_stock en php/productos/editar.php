<?php
/**
 * Página de editar producto
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

$id = $_GET['id'] ?? null;

if (!$id) {
    Session::setFlash('error', 'ID de producto no proporcionado');
    header('Location: /productos/index.php');
    exit;
}

$producto = $productoModel->obtenerPorId($id);

if (!$producto) {
    Session::setFlash('error', 'Producto no encontrado');
    header('Location: /productos/index.php');
    exit;
}

$categorias = $categoriaModel->obtenerTodos();
$proveedores = $proveedorModel->obtenerTodos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lógica para actualizar producto
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
        header('Location: /productos/editar.php?id=' . $id);
        exit;
    }
    
    $resultado = $productoModel->actualizar($id, $datos);
    
    if ($resultado) {
        Session::setFlash('success', 'Producto actualizado exitosamente');
        header('Location: /productos/index.php');
    } else {
        Session::setFlash('error', 'Error al actualizar producto');
        header('Location: /productos/editar.php?id=' . $id);
    }
    exit;
}

require_once __DIR__ . '/../app/views/productos/editar.php';
