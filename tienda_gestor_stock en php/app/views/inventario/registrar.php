<?php
$pageTitle = 'Registrar Movimiento';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Registrar Movimiento de Inventario</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/inventario/registrar.php">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_producto" class="form-label">Producto *</label>
                                <select class="form-select" id="id_producto" name="id_producto" required onchange="mostrarStock()">
                                    <option value="">Seleccione un producto</option>
                                    <?php foreach ($productos as $prod): ?>
                                    <option value="<?= $prod['id_producto'] ?>" data-stock="<?= $prod['stock'] ?>">
                                        <?= htmlspecialchars($prod['nombre']) ?> (Stock: <?= $prod['stock'] ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tipo_movimiento" class="form-label">Tipo de Movimiento *</label>
                                <select class="form-select" id="tipo_movimiento" name="tipo_movimiento" required>
                                    <option value="entrada">Entrada</option>
                                    <option value="salida">Salida</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="cantidad" class="form-label">Cantidad *</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" required min="1">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock Actual</label>
                                <input type="text" class="form-control" id="stockActual" readonly>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock Después</label>
                                <input type="text" class="form-control" id="stockDespues" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="referencia" class="form-label">Referencia</label>
                                <input type="text" class="form-control" id="referencia" name="referencia" placeholder="Ej: Factura #123">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="observacion" class="form-label">Observación</label>
                                <input type="text" class="form-control" id="observacion" name="observacion">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/inventario/index.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Registrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarStock() {
    const select = document.getElementById('id_producto');
    const selectedOption = select.options[select.selectedIndex];
    const stock = selectedOption.getAttribute('data-stock');
    document.getElementById('stockActual').value = stock;
    calcularStockDespues();
}

document.getElementById('cantidad').addEventListener('input', calcularStockDespues);
document.getElementById('tipo_movimiento').addEventListener('change', calcularStockDespues);

function calcularStockDespues() {
    const stockActual = parseInt(document.getElementById('stockActual').value) || 0;
    const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
    const tipo = document.getElementById('tipo_movimiento').value;
    
    let stockDespues;
    if (tipo === 'entrada') {
        stockDespues = stockActual + cantidad;
    } else {
        stockDespues = stockActual - cantidad;
    }
    
    document.getElementById('stockDespues').value = stockDespues;
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
