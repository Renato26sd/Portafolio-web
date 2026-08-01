<?php
/**
 * Página para ver venta
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/Session.php';
require_once __DIR__ . '/../app/models/Venta.php';

Session::start();

// Verificar autenticación
if (!Session::isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    Session::setFlash('error', 'ID de venta no proporcionado');
    header('Location: /ventas/index.php');
    exit;
}

// Obtener venta y detalles
$ventaModel = new Venta();
$venta = $ventaModel->obtenerPorId($id);
$detalles = $ventaModel->obtenerDetalles($id);

if (!$venta) {
    Session::setFlash('error', 'Venta no encontrada');
    header('Location: /ventas/index.php');
    exit;
}

// Procesar nombre del cliente
if ($venta['cliente_nombres'] && $venta['cliente_apellidos']) {
    $venta['nombre_cliente'] = $venta['cliente_nombres'] . ' ' . $venta['cliente_apellidos'];
} else {
    $venta['nombre_cliente'] = null;
}

// Cargar vista
$pageTitle = 'Detalle de Venta #' . $venta['id_venta'];
require_once __DIR__ . '/../app/views/layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-receipt me-2"></i>Venta #<?= $venta['id_venta'] ?></h2>
                <div>
                    <a href="/ventas/index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    <a href="/ventas/ticket.php?id=<?= $venta['id_venta'] ?>" class="btn btn-primary" target="_blank">
                        <i class="fas fa-print me-2"></i>Imprimir Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Productos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles as $detalle): ?>
                                <tr>
                                    <td><?= htmlspecialchars($detalle['producto_nombre']) ?></td>
                                    <td><?= $detalle['cantidad'] ?></td>
                                    <td>S/ <?= number_format($detalle['precio_unitario'], 2) ?></td>
                                    <td>S/ <?= number_format($detalle['subtotal'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Fecha:</strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Cliente:</strong></td>
                            <td><?= htmlspecialchars($venta['nombre_cliente'] ?? 'Consumidor Final') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Vendedor:</strong></td>
                            <td><?= htmlspecialchars($venta['nombres'] . ' ' . $venta['apellidos']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Método Pago:</strong></td>
                            <td><?= ucfirst($venta['metodo_pago']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong></td>
                            <td>
                                <span class="badge status-<?= $venta['estado'] ?>">
                                    <?= ucfirst($venta['estado']) ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Resumen</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td class="text-end">S/ <?= number_format($venta['subtotal'], 2) ?></td>
                        </tr>
                        <tr>
                            <td><strong>IGV (18%):</strong></td>
                            <td class="text-end">S/ <?= number_format($venta['igv'], 2) ?></td>
                        </tr>
                        <?php if ($venta['descuento'] > 0): ?>
                        <tr>
                            <td><strong>Descuento:</strong></td>
                            <td class="text-end text-danger">- S/ <?= number_format($venta['descuento'], 2) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="table-active">
                            <td><strong>Total:</strong></td>
                            <td class="text-end"><strong>S/ <?= number_format($venta['total'], 2) ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if ($venta['observaciones']): ?>
    <div class="card shadow mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Observaciones</h5>
        </div>
        <div class="card-body">
            <?= nl2br(htmlspecialchars($venta['observaciones'])) ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../app/views/layouts/footer.php'; ?>
