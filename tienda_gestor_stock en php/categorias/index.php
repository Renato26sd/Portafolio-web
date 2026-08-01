<?php
/**
 * Página de categorías
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

require_once __DIR__ . '/../app/models/Categoria.php';

$categoriaModel = new Categoria();
$categorias = $categoriaModel->obtenerTodos();

require_once __DIR__ . '/../app/views/categorias/index.php';
