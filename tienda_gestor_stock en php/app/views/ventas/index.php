<?php
$pageTitle = 'Ventas';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-shopping-cart me-2"></i>Ventas</h2>
                <?php if (Session::hasPermission('ventas.crear')): ?>
                <a href="/ventas/crear.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nueva Venta
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="/ventas/index.php" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" class="form-control" name="fecha_inicio" value="<?= htmlspecialchars($filtros['fecha_inicio']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" class="form-control" name="fecha_fin" value="<?= htmlspecialchars($filtros['fecha_fin']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="">Todos</option>
                        <option value="completada" <?= $filtros['estado'] === 'completada' ? 'selected' : '' ?>>Completada</option>
                        <option value="anulada" <?= $filtros['estado'] === 'anulada' ? 'selected' : '' ?>>Anulada</option>
                        <option value="pendiente" <?= $filtros['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Método Pago</label>
                    <select class="form-select" name="metodo_pago">
                        <option value="">Todos</option>
                        <option value="efectivo" <?= $filtros['metodo_pago'] === 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                        <option value="yape" <?= $filtros['metodo_pago'] === 'yape' ? 'selected' : '' ?>>Yape</option>
                        <option value="plin" <?= $filtros['metodo_pago'] === 'plin' ? 'selected' : '' ?>>Plin</option>
                        <option value="tarjeta" <?= $filtros['metodo_pago'] === 'tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="ventasTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Método Pago</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><?= $venta['id_venta'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                            <td><?= htmlspecialchars($venta['nombre_cliente'] ?? 'Consumidor Final') ?></td>
                            <td>S/ <?= number_format($venta['total'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= $venta['metodo_pago'] === 'efectivo' ? 'success' : ($venta['metodo_pago'] === 'yape' ? 'primary' : ($venta['metodo_pago'] === 'plin' ? 'info' : 'warning')) ?>">
                                    <?= ucfirst($venta['metodo_pago']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge status-<?= $venta['estado'] ?>">
                                    <?= ucfirst($venta['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (Session::hasPermission('ventas.ver')): ?>
                                <a href="/ventas/ver.php?id=<?= $venta['id_venta'] ?>" class="btn btn-sm btn-outline-primary action-btn" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (Session::hasPermission('ventas.ver')): ?>
                                <a href="/ventas/ticket.php?id=<?= $venta['id_venta'] ?>" class="btn btn-sm btn-outline-secondary action-btn" title="Ticket">
                                    <i class="fas fa-receipt"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (Session::hasPermission('ventas.eliminar') && $venta['estado'] === 'completada'): ?>
                                <button onclick="anularVenta('/ventas/anular.php?id=<?= $venta['id_venta'] ?>', '¿Está seguro de anular esta venta?')" class="btn btn-sm btn-outline-danger action-btn" title="Anular">
                                    <i class="fas fa-ban"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($totalPaginas > 1): ?>
            <nav aria-label="Paginación">
                <ul class="pagination justify-content-center mt-3">
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="/ventas/index.php?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function anularVenta(url, mensaje) {
    if (confirm(mensaje)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const csrfToken = document.querySelector('input[name="csrf_token"]');
        if (csrfToken) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'csrf_token';
            input.value = csrfToken.value;
            form.appendChild(input);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
