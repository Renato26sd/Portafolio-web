<?php
require_once __DIR__ . '/../../helpers/Session.php';
Session::start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?><?= SITE_NAME ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
<?php if (Session::isLoggedIn()): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="/dashboard.php">
            <i class="fas fa-warehouse me-2"></i><?= SITE_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard.php">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>
                <?php if (Session::hasPermission('productos.ver') || Session::hasPermission('ventas.crear')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-box me-1"></i> Operaciones
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (Session::hasPermission('ventas.crear')): ?>
                        <li><a class="dropdown-item" href="/ventas/crear.php"><i class="fas fa-shopping-cart me-2"></i>Nueva Venta</a></li>
                        <?php endif; ?>
                        <?php if (Session::hasPermission('inventario.entradas')): ?>
                        <li><a class="dropdown-item" href="/inventario/registrar.php"><i class="fas fa-plus-circle me-2"></i>Registrar Entrada</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if (Session::hasPermission('productos.ver')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/productos/index.php">
                        <i class="fas fa-boxes me-1"></i> Productos
                    </a>
                </li>
                <?php endif; ?>
                <?php if (Session::hasPermission('ventas.ver')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/ventas/index.php">
                        <i class="fas fa-receipt me-1"></i> Ventas
                    </a>
                </li>
                <?php endif; ?>
                <?php if (Session::hasPermission('inventario.ver')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/inventario/index.php">
                        <i class="fas fa-clipboard-list me-1"></i> Inventario
                    </a>
                </li>
                <?php endif; ?>
                <?php if (Session::hasPermission('reportes.ver')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/reportes/index.php">
                        <i class="fas fa-chart-bar me-1"></i> Reportes
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (Session::getUserRole() === 'Administrador'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-cogs me-1"></i> Administración
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/usuarios/index.php"><i class="fas fa-users me-2"></i>Usuarios</a></li>
                        <li><a class="dropdown-item" href="/categorias/index.php"><i class="fas fa-tags me-2"></i>Categorías</a></li>
                        <li><a class="dropdown-item" href="/proveedores/index.php"><i class="fas fa-truck me-2"></i>Proveedores</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i> <?= Session::get('user')['nombres'] ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text"><small class="text-muted"><?= Session::getUserRole() ?></small></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="main-content">
<?php endif; ?>
