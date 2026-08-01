<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-3">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </h2>
            <p class="text-muted">Bienvenido, <?= Session::get('user')['nombres'] ?></p>
        </div>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shopping-cart fa-3x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title">Ventas Hoy</h6>
                            <h3 class="mb-0"><?= number_format($resumen['ventas_hoy'], 0) ?></h3>
                            <small><?= CURRENCY_SYMBOL . number_format($resumen['total_ventas_hoy'], 2) ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line fa-3x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title">Ventas Mes</h6>
                            <h3 class="mb-0"><?= number_format($resumen['ventas_mes'], 0) ?></h3>
                            <small><?= CURRENCY_SYMBOL . number_format($resumen['total_ventas_mes'], 2) ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title">Stock Bajo</h6>
                            <h3 class="mb-0"><?= number_format($resumen['productos_bajo_stock'], 0) ?></h3>
                            <small>Productos necesitan reposición</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-boxes fa-3x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title">Total Productos</h6>
                            <h3 class="mb-0"><?= number_format($resumen['total_productos'], 0) ?></h3>
                            <small><?= number_format($resumen['productos_sin_stock'], 0) ?> sin stock</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Gráfico de ventas por método de pago -->
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>Ventas por Método de Pago
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="metodoPagoChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Gráfico de ventas por vendedor -->
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>Ventas por Vendedor
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="vendedorChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Últimas ventas -->
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt me-2"></i>Últimas Ventas
                    </h5>
                    <a href="/ventas/index.php" class="btn btn-sm btn-outline-primary">Ver todas</a>
                </div>
                <div class="card-body">
                    <?php if (empty($ultimasVentas)): ?>
                        <p class="text-muted text-center">No hay ventas registradas</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Vendedor</th>
                                        <th>Total</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimasVentas as $venta): ?>
                                    <tr>
                                        <td>#<?= $venta['id_venta'] ?></td>
                                        <td><?= $venta['nombres'] . ' ' . $venta['apellidos'] ?></td>
                                        <td><?= CURRENCY_SYMBOL . number_format($venta['total'], 2) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Productos más vendidos -->
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-fire me-2"></i>Productos Más Vendidos
                    </h5>
                    <a href="/reportes/index.php" class="btn btn-sm btn-outline-primary">Ver reportes</a>
                </div>
                <div class="card-body">
                    <?php if (empty($productosMasVendidos)): ?>
                        <p class="text-muted text-center">No hay datos disponibles</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Código</th>
                                        <th>Vendidos</th>
                                        <th>Ingresos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productosMasVendidos as $producto): ?>
                                    <tr>
                                        <td><?= $producto['nombre'] ?></td>
                                        <td><?= $producto['codigo'] ?></td>
                                        <td><?= number_format($producto['total_vendido'], 0) ?></td>
                                        <td><?= CURRENCY_SYMBOL . number_format($producto['total_ingresos'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfico de ventas por método de pago
const metodoPagoCtx = document.getElementById('metodoPagoChart').getContext('2d');
const metodoPagoChart = new Chart(metodoPagoCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($ventasPorMetodoPago, 'metodo_pago')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($ventasPorMetodoPago, 'total_monto')) ?>,
            backgroundColor: [
                '#0d6efd',
                '#198754',
                '#dc3545',
                '#ffc107'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfico de ventas por vendedor
const vendedorCtx = document.getElementById('vendedorChart').getContext('2d');
const vendedorChart = new Chart(vendedorCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_map(function($v) { return $v['nombres'] . ' ' . $v['apellidos']; }, $ventasPorVendedor)) ?>,
        datasets: [{
            label: 'Total Ventas (S/)',
            data: <?= json_encode(array_column($ventasPorVendedor, 'total_monto')) ?>,
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
