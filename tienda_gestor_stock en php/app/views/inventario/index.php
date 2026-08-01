<?php
$pageTitle = 'Movimientos de Inventario';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-clipboard-list me-2"></i>Movimientos de Inventario</h2>
                <?php if (Session::hasPermission('inventario.entradas')): ?>
                <a href="/inventario/registrar.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Registrar Movimiento
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="/inventario/index.php" class="row g-3">
                <div class="col-md-3">
                    <select class="form-select" name="tipo_movimiento">
                        <option value="">Todos los tipos</option>
                        <option value="entrada" <?= ($filtros['tipo_movimiento'] ?? '') == 'entrada' ? 'selected' : '' ?>>Entrada</option>
                        <option value="salida" <?= ($filtros['tipo_movimiento'] ?? '') == 'salida' ? 'selected' : '' ?>>Salida</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="producto">
                        <option value="">Todos los productos</option>
                        <?php foreach ($productos as $prod): ?>
                        <option value="<?= $prod['id_producto'] ?>" <?= ($filtros['producto'] ?? '') == $prod['id_producto'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($prod['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="fecha_inicio" value="<?= $filtros['fecha_inicio'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="fecha_fin" value="<?= $filtros['fecha_fin'] ?? '' ?>">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Stock Anterior</th>
                            <th>Stock Nuevo</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimientos as $mov): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($mov['fecha'])) ?></td>
                            <td><?= htmlspecialchars($mov['producto_nombre']) ?></td>
                            <td>
                                <span class="badge <?= $mov['tipo_movimiento'] == 'entrada' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ucfirst($mov['tipo_movimiento']) ?>
                                </span>
                            </td>
                            <td><?= $mov['cantidad'] ?></td>
                            <td><?= $mov['stock_anterior'] ?></td>
                            <td><?= $mov['stock_nuevo'] ?></td>
                            <td><?= htmlspecialchars($mov['nombres'] . ' ' . $mov['apellidos']) ?></td>
                            <td>
                                <a href="/inventario/kardex.php?id=<?= $mov['id_producto'] ?>" class="btn btn-sm btn-outline-info action-btn" title="Ver Kardex">
                                    <i class="fas fa-list"></i>
                                </a>
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
                        <a class="page-link" href="/inventario/index.php?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
