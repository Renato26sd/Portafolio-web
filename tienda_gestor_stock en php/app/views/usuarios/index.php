<?php
$pageTitle = 'Usuarios';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-users me-2"></i>Usuarios</h2>
                <?php if (Session::hasPermission('usuarios.crear')): ?>
                <a href="/usuarios/crear.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Usuario
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usuariosTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= $usuario['id_usuario'] ?></td>
                            <td><?= htmlspecialchars($usuario['nombres']) ?></td>
                            <td><?= htmlspecialchars($usuario['apellidos']) ?></td>
                            <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                            <td><?= htmlspecialchars($usuario['correo']) ?></td>
                            <td><span class="badge bg-primary"><?= htmlspecialchars($usuario['nombre_rol']) ?></span></td>
                            <td>
                                <span class="badge status-<?= $usuario['estado'] ?>">
                                    <?= ucfirst($usuario['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (Session::hasPermission('usuarios.editar')): ?>
                                <a href="/usuarios/editar.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-outline-primary action-btn" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (Session::hasPermission('usuarios.eliminar') && $usuario['id_usuario'] != Session::getUserId()): ?>
                                <button onclick="deleteRecord('/usuarios/eliminar.php?id=<?= $usuario['id_usuario'] ?>', '¿Está seguro de eliminar este usuario?')" class="btn btn-sm btn-outline-danger action-btn" title="Eliminar">
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
                        <a class="page-link" href="/usuarios/index.php?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
