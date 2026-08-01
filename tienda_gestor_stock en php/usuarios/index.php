<?php
/**
 * Página de usuarios
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/Session.php';
require_once __DIR__ . '/../app/models/Usuario.php';

Session::start();

// Verificar autenticación
if (!Session::isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

// Obtener usuarios
$usuarioModel = new Usuario();
$pagina = $_GET['pagina'] ?? 1;
$usuarios = $usuarioModel->obtenerTodos($pagina);
$total = $usuarioModel->contarTotal();
$totalPaginas = ceil($total / ITEMS_PER_PAGE);

// Cargar vista
$pageTitle = 'Usuarios';
require_once __DIR__ . '/../app/views/usuarios/index.php';
