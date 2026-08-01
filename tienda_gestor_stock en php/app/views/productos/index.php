<?php
$pageTitle = 'Productos';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-boxes me-2"></i>Productos</h2>
                <?php if (Session::hasPermission('productos.crear')): ?>
                <a href="/productos/crear.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Producto
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="/productos/index.php" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="busqueda" placeholder="Buscar por nombre o código..." value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="categoria">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id_categoria'] ?>" <?= ($filtros['categoria'] ?? '') == $cat['id_categoria'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="activo" <?= ($filtros['estado'] ?? '') == 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= ($filtros['estado'] ?? '') == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Buscar
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
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>P. Compra</th>
                            <th>P. Venta</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?= htmlspecialchars($producto['codigo']) ?></td>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td><?= htmlspecialchars($producto['categoria_nombre'] ?? '-') ?></td>
                            <td><?= CURRENCY_SYMBOL . number_format($producto['precio_compra'], 2) ?></td>
                            <td><?= CURRENCY_SYMBOL . number_format($producto['precio_venta'], 2) ?></td>
                            <td>
                                <?php if ($producto['stock'] == 0): ?>
                                <span class="badge stock-critico"><?= $producto['stock'] ?></span>
                                <?php elseif ($producto['stock'] <= $producto['stock_minimo']): ?>
                                <span class="badge stock-bajo"><?= $producto['stock'] ?></span>
                                <?php else: ?>
                                <span class="badge stock-normal"><?= $producto['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge status-<?= $producto['estado'] ?>">
                                    <?= ucfirst($producto['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (Session::hasPermission('productos.editar')): ?>
                                <a href="/productos/editar.php?id=<?= $producto['id_producto'] ?>" class="btn btn-sm btn-outline-primary action-btn" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (Session::hasPermission('productos.eliminar')): ?>
                                <button onclick="deleteRecord('/productos/eliminar.php?id=<?= $producto['id_producto'] ?>', '¿Está seguro de eliminar este producto?')" class="btn btn-sm btn-outline-danger action-btn" title="Eliminar">
                                    <i class="fas fa-trash"></i>
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
                        <a class="page-link" href="/productos/index.php?pagina=<?= $i ?>&busqueda=<?= urlencode($filtros['busqueda'] ?? '') ?>&categoria=<?= $filtros['categoria'] ?? '' ?>&estado=<?= $filtros['estado'] ?? '' ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
