<?php
$pageTitle = 'Proveedores';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-truck me-2"></i>Proveedores</h2>
                <?php if (Session::hasPermission('proveedores.crear')): ?>
                <a href="/proveedores/crear.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Proveedor
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>RUC</th>
                            <th>Razón Social</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proveedores as $proveedor): ?>
                        <tr>
                            <td><?= htmlspecialchars($proveedor['ruc']) ?></td>
                            <td><?= htmlspecialchars($proveedor['razon_social']) ?></td>
                            <td><?= htmlspecialchars($proveedor['nombre_contacto'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($proveedor['telefono'] ?? '-') ?></td>
                            <td>
                                <span class="badge status-<?= $proveedor['estado'] ?>">
                                    <?= ucfirst($proveedor['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (Session::hasPermission('proveedores.editar')): ?>
                                <a href="/proveedores/editar.php?id=<?= $proveedor['id_proveedor'] ?>" class="btn btn-sm btn-outline-primary action-btn" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (Session::hasPermission('proveedores.eliminar')): ?>
                                <button onclick="deleteRecord('/proveedores/eliminar.php?id=<?= $proveedor['id_proveedor'] ?>', '¿Está seguro de eliminar este proveedor?')" class="btn btn-sm btn-outline-danger action-btn" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
