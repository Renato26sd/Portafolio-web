<?php
$pageTitle = 'Nueva Venta';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Registrar Nueva Venta</h5>
                </div>
                <div class="card-body">
                    <form id="ventaForm" method="POST" action="/ventas/crear.php">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="cliente" class="form-label">Cliente</label>
                                <select class="form-select" id="id_cliente" name="id_cliente">
                                    <option value="">Cliente general</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['id_cliente'] ?>">
                                        <?= htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apellidos']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="metodo_pago" class="form-label">Método de Pago *</label>
                                <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="yape">Yape</option>
                                    <option value="plin">Plin</option>
                                    <option value="tarjeta">Tarjeta</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="terminoBusqueda" placeholder="Buscar producto por nombre o código...">
                                    <button class="btn btn-primary" type="button" onclick="buscarProducto()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="codigoBarras" placeholder="Escanear código de barras..." onkeypress="if(event.key === 'Enter') buscarPorCodigo()">
                            </div>
                        </div>
                        
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="carritoBody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Agregue productos a la venta</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="descuento" class="form-label">Descuento</label>
                                <input type="number" step="0.01" class="form-control" id="descuento" name="descuento" value="0" oninput="calcularTotales()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="form-control" id="subtotalDisplay" readonly value="S/ 0.00">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">IGV (18%)</label>
                                <input type="text" class="form-control" id="igvDisplay" readonly value="S/ 0.00">
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total a Pagar</label>
                                <input type="text" class="form-control form-control-lg fw-bold" id="totalDisplay" readonly value="S/ 0.00">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/ventas/index.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i>Completar Venta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de búsqueda de productos -->
<div class="modal fade" id="modalProductos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="productosResultados">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let carrito = [];
let modalProductos;

document.addEventListener('DOMContentLoaded', function() {
    modalProductos = new bootstrap.Modal(document.getElementById('modalProductos'));
    
    document.getElementById('ventaForm').addEventListener('submit', function(e) {
        console.log('Carrito al enviar formulario:', carrito);
        console.log('Longitud del carrito:', carrito.length);
        
        if (carrito.length === 0) {
            e.preventDefault();
            showAlert('error', 'Debe agregar al menos un producto');
            return;
        }
        
        // Agregar carrito al formulario
        const detallesInput = document.createElement('input');
        detallesInput.type = 'hidden';
        detallesInput.name = 'detalles';
        detallesInput.value = JSON.stringify(carrito);
        this.appendChild(detallesInput);
    });
});

function buscarProducto() {
    const termino = document.getElementById('terminoBusqueda').value;
    if (termino.length < 2) return;
    
    fetch(`/productos/buscar.php?termino=${encodeURIComponent(termino)}`)
        .then(response => response.json())
        .then(data => {
            mostrarProductos(data);
        });
}

function buscarPorCodigo() {
    const codigo = document.getElementById('codigoBarras').value;
    if (codigo.length < 2) return;
    
    fetch(`/productos/buscar.php?termino=${encodeURIComponent(codigo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 1) {
                agregarAlCarrito(data[0]);
                document.getElementById('codigoBarras').value = '';
            } else {
                mostrarProductos(data);
            }
        });
}

function mostrarProductos(productos) {
    const tbody = document.getElementById('productosResultados');
    tbody.innerHTML = '';
    
    productos.forEach((prod, index) => {
        tbody.innerHTML += `
            <tr>
                <td>${prod.codigo}</td>
                <td>${prod.nombre}</td>
                <td>S/ ${parseFloat(prod.precio_venta).toFixed(2)}</td>
                <td>${prod.stock}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary" onclick="agregarAlCarrito(${index})">
                        <i class="fas fa-plus"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    window.productosTemp = productos;
    modalProductos.show();
}

function agregarAlCarrito(index) {
    const producto = window.productosTemp[index];
    const precioVenta = parseFloat(producto.precio_venta);
    const existente = carrito.find(item => item.id_producto === producto.id_producto);
    
    if (existente) {
        if (existente.cantidad < producto.stock) {
            existente.cantidad++;
            existente.subtotal = existente.cantidad * existente.precio_unitario;
        } else {
            showAlert('warning', 'No hay más stock disponible');
        }
    } else {
        carrito.push({
            id_producto: producto.id_producto,
            nombre: producto.nombre,
            codigo: producto.codigo,
            stock: producto.stock,
            cantidad: 1,
            precio_unitario: precioVenta,
            subtotal: precioVenta
        });
    }
    
    console.log('Carrito después de agregar:', carrito);
    modalProductos.hide();
    actualizarCarrito();
}

function actualizarCarrito() {
    const tbody = document.getElementById('carritoBody');
    
    if (carrito.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Agregue productos a la venta</td></tr>';
    } else {
        tbody.innerHTML = carrito.map((item, index) => `
            <tr>
                <td>${item.nombre || 'Producto ' + (index + 1)}</td>
                <td>S/ ${parseFloat(item.precio_unitario).toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" style="width: 70px" 
                           value="${item.cantidad}" min="1" onchange="actualizarCantidad(${index}, this.value)">
                </td>
                <td>S/ ${parseFloat(item.subtotal).toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDelCarrito(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }
    
    calcularTotales();
}

function actualizarCantidad(index, cantidad) {
    if (cantidad < 1) return;
    carrito[index].cantidad = parseInt(cantidad);
    carrito[index].subtotal = carrito[index].cantidad * carrito[index].precio_unitario;
    actualizarCarrito();
}

function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
}

function calcularTotales() {
    const subtotal = carrito.reduce((sum, item) => sum + (parseFloat(item.subtotal) || 0), 0);
    const descuento = parseFloat(document.getElementById('descuento').value) || 0;
    const subtotalConDescuento = subtotal - descuento;
    const igv = subtotalConDescuento * 0.18;
    const total = subtotalConDescuento + igv;
    
    document.getElementById('subtotalDisplay').value = 'S/ ' + subtotal.toFixed(2);
    document.getElementById('igvDisplay').value = 'S/ ' + igv.toFixed(2);
    document.getElementById('totalDisplay').value = 'S/ ' + total.toFixed(2);
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
