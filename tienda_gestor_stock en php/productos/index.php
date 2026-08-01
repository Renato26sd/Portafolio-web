<?php
/**
 * Página de productos
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/Session.php';
require_once __DIR__ . '/../app/models/Producto.php';
require_once __DIR__ . '/../app/models/Categoria.php';
require_once __DIR__ . '/../app/models/Proveedor.php';

Session::start();

// Verificar autenticación
if (!Session::isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

// Obtener productos
$productoModel = new Producto();
$categoriaModel = new Categoria();
$proveedorModel = new Proveedor();

$pagina = $_GET['pagina'] ?? 1;
$filtros = [
    'busqueda' => $_GET['busqueda'] ?? '',
    'categoria' => $_GET['categoria'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'stock_bajo' => isset($_GET['stock_bajo'])
];

$productos = $productoModel->obtenerTodos($pagina, ITEMS_PER_PAGE, $filtros);
$total = $productoModel->contarTotal($filtros);
$totalPaginas = ceil($total / ITEMS_PER_PAGE);
$categorias = $categoriaModel->obtenerTodos();
$proveedores = $proveedorModel->obtenerTodos();

// Cargar vista
$pageTitle = 'Productos';
require_once __DIR__ . '/../app/views/productos/index.php';
