const API_URL = 'http://localhost:5000/api';

let products = [];
let orders = [];
let stats = {};
let currentView = 'products';
let editingProduct = null;

// Initialize dashboard
async function init() {
    await Promise.all([loadProducts(), loadOrders(), loadStats()]);
    renderApp();
}

// Load data from API
async function loadProducts() {
    try {
        const response = await fetch(`${API_URL}/products`);
        products = await response.json();
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

async function loadOrders() {
    try {
        const response = await fetch(`${API_URL}/orders`);
        orders = await response.json();
    } catch (error) {
        console.error('Error loading orders:', error);
    }
}

async function loadStats() {
    try {
        const response = await fetch(`${API_URL}/stats`);
        stats = await response.json();
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Product operations
async function createProduct(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    const productData = {
        name: formData.get('name'),
        description: formData.get('description'),
        price: parseFloat(formData.get('price')),
        stock: parseInt(formData.get('stock')),
        category: formData.get('category'),
        image_url: formData.get('image_url')
    };

    try {
        const response = await fetch(`${API_URL}/products`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(productData)
        });

        if (response.ok) {
            await loadProducts();
            await loadStats();
            editingProduct = null;
            renderApp();
        }
    } catch (error) {
        console.error('Error creating product:', error);
    }
}

async function updateProduct(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    const productData = {
        name: formData.get('name'),
        description: formData.get('description'),
        price: parseFloat(formData.get('price')),
        stock: parseInt(formData.get('stock')),
        category: formData.get('category'),
        image_url: formData.get('image_url')
    };

    try {
        const response = await fetch(`${API_URL}/products/${editingProduct.id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(productData)
        });

        if (response.ok) {
            await loadProducts();
            editingProduct = null;
            renderApp();
        }
    } catch (error) {
        console.error('Error updating product:', error);
    }
}

async function deleteProduct(productId) {
    if (!confirm('¿Estás seguro de eliminar este producto?')) return;

    try {
        const response = await fetch(`${API_URL}/products/${productId}`, {
            method: 'DELETE'
        });

        if (response.ok) {
            await loadProducts();
            await loadStats();
            renderApp();
        }
    } catch (error) {
        console.error('Error deleting product:', error);
    }
}

function editProduct(product) {
    editingProduct = product;
    renderApp();
}

// Order operations
async function updateOrderStatus(orderId, status) {
    try {
        const response = await fetch(`${API_URL}/orders/${orderId}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status })
        });

        if (response.ok) {
            await loadOrders();
            await loadStats();
            renderApp();
        }
    } catch (error) {
        console.error('Error updating order status:', error);
    }
}

// Render the dashboard
function renderApp() {
    const app = document.getElementById('app');
    
    app.innerHTML = `
        <nav class="bg-gray-800 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-chart-line"></i>
                    Dashboard Admin
                </h1>
                <div class="flex items-center gap-4">
                    <a href="index.html" class="hover:text-gray-300 transition">
                        <i class="fas fa-home"></i> Tienda
                    </a>
                    <a href="dashboard.html" class="text-blue-400">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 py-8">
            ${renderStats()}
            
            <div class="mt-8 flex gap-4 mb-6">
                <button onclick="setView('products')" class="px-4 py-2 rounded-lg ${currentView === 'products' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'}">
                    <i class="fas fa-box mr-2"></i> Productos
                </button>
                <button onclick="setView('orders')" class="px-4 py-2 rounded-lg ${currentView === 'orders' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'}">
                    <i class="fas fa-shopping-bag mr-2"></i> Pedidos
                </button>
            </div>

            ${currentView === 'products' ? renderProductsView() : renderOrdersView()}
        </main>
    `;
}

function renderStats() {
    return `
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Productos</p>
                        <p class="text-3xl font-bold text-gray-800">${stats.total_products || 0}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-box text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Pedidos</p>
                        <p class="text-3xl font-bold text-gray-800">${stats.total_orders || 0}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Ingresos Totales</p>
                        <p class="text-3xl font-bold text-gray-800">$${(stats.total_revenue || 0).toFixed(2)}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Pedidos Pendientes</p>
                        <p class="text-3xl font-bold text-gray-800">${stats.pending_orders || 0}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderProductsView() {
    return `
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-box mr-2"></i> Gestión de Productos
                </h2>
                <button onclick="showProductForm()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i> Nuevo Producto
                </button>
            </div>
            
            ${editingProduct ? renderProductForm() : ''}
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${products.map(product => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-box text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">${product.name}</div>
                                            <div class="text-sm text-gray-500">${product.description.substring(0, 30)}...</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        ${product.category}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900">$${product.price.toFixed(2)}</td>
                                <td class="px-6 py-4">
                                    <span class="${product.stock < 10 ? 'text-red-600' : 'text-green-600'} font-semibold">
                                        ${product.stock}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="editProduct(${JSON.stringify(product).replace(/"/g, '&quot;')})" class="text-blue-600 hover:text-blue-800 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteProduct(${product.id})" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function renderProductForm() {
    const product = editingProduct;
    return `
        <div class="p-6 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold mb-4">
                <i class="fas fa-${product ? 'edit' : 'plus'} mr-2"></i>
                ${product ? 'Editar Producto' : 'Nuevo Producto'}
            </h3>
            <form onsubmit="${product ? 'updateProduct(event)' : 'createProduct(event)'}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="name" required value="${product?.name || ''}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <input type="text" name="category" value="${product?.category || ''}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                    <input type="number" step="0.01" name="price" required value="${product?.price || ''}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <input type="number" name="stock" required value="${product?.stock || ''}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">${product?.description || ''}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL de Imagen</label>
                    <input type="url" name="image_url" value="${product?.image_url || ''}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i> Guardar
                    </button>
                    <button type="button" onclick="cancelEdit()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    `;
}

function renderOrdersView() {
    return `
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-shopping-bag mr-2"></i> Gestión de Pedidos
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${orders.map(order => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">#${order.id}</td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-medium text-gray-900">${order.customer_name}</div>
                                        <div class="text-sm text-gray-500">${order.customer_email}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900">$${order.total.toFixed(2)}</td>
                                <td class="px-6 py-4">
                                    <select onchange="updateOrderStatus(${order.id}, this.value)" class="px-2 py-1 rounded-full text-sm font-medium ${
                                        order.status === 'completed' ? 'bg-green-100 text-green-800' :
                                        order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                        'bg-gray-100 text-gray-800'
                                    }">
                                        <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>Pendiente</option>
                                        <option value="processing" ${order.status === 'processing' ? 'selected' : ''}>Procesando</option>
                                        <option value="completed" ${order.status === 'completed' ? 'selected' : ''}>Completado</option>
                                        <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Cancelado</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    ${new Date(order.created_at).toLocaleDateString()}
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="viewOrderDetails(${order.id})" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function showProductForm() {
    editingProduct = null;
    renderApp();
}

function cancelEdit() {
    editingProduct = null;
    renderApp();
}

function setView(view) {
    currentView = view;
    renderApp();
}

function viewOrderDetails(orderId) {
    const order = orders.find(o => o.id === orderId);
    if (!order) return;

    alert(`
Pedido #${order.id}
Cliente: ${order.customer_name}
Email: ${order.customer_email}
Total: $${order.total.toFixed(2)}
Estado: ${order.status}

Items:
${order.items.map(item => `- ${item.product_name} x${item.quantity}: $${item.subtotal.toFixed(2)}`).join('\n')}
    `);
}

// Start the dashboard
init();
