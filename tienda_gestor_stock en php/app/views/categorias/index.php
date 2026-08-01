<?php
$pageTitle = 'Categorías';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-tags me-2"></i>Categorías</h2>
                <?php if (Session::hasPermission('categorias.crear')): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
                    <i class="fas fa-plus me-2"></i>Nueva Categoría
                </button>
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
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?= $categoria['id_categoria'] ?></td>
                            <td><?= htmlspecialchars($categoria['nombre']) ?></td>
                            <td><?= htmlspecialchars($categoria['descripcion'] ?? '-') ?></td>
                            <td>
                                <span class="badge status-<?= $categoria['estado'] ?>">
                                    <?= ucfirst($categoria['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (Session::hasPermission('categorias.editar')): ?>
                                <a href="/categorias/editar.php?id=<?= $categoria['id_categoria'] ?>" class="btn btn-sm btn-outline-primary action-btn" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (Session::hasPermission('categorias.eliminar')): ?>
                                <button onclick="deleteRecord('/categorias/eliminar.php?id=<?= $categoria['id_categoria'] ?>', '¿Está seguro de eliminar esta categoría?')" class="btn btn-sm btn-outline-danger action-btn" title="Eliminar">
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
