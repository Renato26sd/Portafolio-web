const API_URL = 'http://localhost:5000/api';

let products = [];
let cart = [];

// Initialize app
async function init() {
    await loadProducts();
    renderApp();
}

// Load products from API
async function loadProducts() {
    try {
        const response = await fetch(`${API_URL}/products`);
        products = await response.json();
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// Add to cart
function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const existingItem = cart.find(item => item.product_id === productId);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            product_id: productId,
            product_name: product.name,
            price: product.price,
            quantity: 1
        });
    }
    renderApp();
}

// Remove from cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.product_id !== productId);
    renderApp();
}

// Update quantity
function updateQuantity(productId, change) {
    const item = cart.find(item => item.product_id === productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(productId);
        } else {
            renderApp();
        }
    }
}

// Get cart total
function getCartTotal() {
    return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
}

// Get cart count
function getCartCount() {
    return cart.reduce((count, item) => count + item.quantity, 0);
}

// Submit order
async function submitOrder(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const orderData = {
        customer_name: formData.get('name'),
        customer_email: formData.get('email'),
        total: getCartTotal(),
        items: cart
    };

    try {
        const response = await fetch(`${API_URL}/orders`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        });

        if (response.ok) {
            alert('¡Pedido realizado con éxito!');
            cart = [];
            renderApp();
        } else {
            alert('Error al realizar el pedido');
        }
    } catch (error) {
        console.error('Error submitting order:', error);
        alert('Error al realizar el pedido');
    }
}

// Render the application
function renderApp() {
    const app = document.getElementById('app');
    
    app.innerHTML = `
        <nav class="bg-blue-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-store"></i>
                    Tienda Virtual
                </h1>
                <div class="flex items-center gap-4">
                    <a href="index.html" class="hover:text-blue-200 transition">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                    <a href="dashboard.html" class="hover:text-blue-200 transition">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    <button onclick="toggleCart()" class="relative hover:text-blue-200 transition">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        ${cart.length > 0 ? `<span class="cart-badge">${getCartCount()}</span>` : ''}
                    </button>
                </div>
            </div>
        </nav>

        ${renderCartModal()}
        
        <main class="max-w-7xl mx-auto px-4 py-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Nuestros Productos</h2>
                <p class="text-gray-600">Explora nuestra selección de productos de calidad</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                ${products.map(product => renderProductCard(product)).join('')}
            </div>
        </main>

        <footer class="bg-gray-800 text-white mt-12 py-8">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p>&copy; 2024 Tienda Virtual. Todos los derechos reservados.</p>
            </div>
        </footer>
    `;
}

// Render product card
function renderProductCard(product) {
    return `
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
            <div class="h-48 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                <i class="fas fa-box text-6xl text-blue-400"></i>
            </div>
            <div class="p-4">
                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mb-2">
                    ${product.category}
                </span>
                <h3 class="font-semibold text-lg text-gray-800 mb-2">${product.name}</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">${product.description}</p>
                <div class="flex justify-between items-center">
                    <span class="text-2xl font-bold text-blue-600">$${product.price.toFixed(2)}</span>
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-box"></i> ${product.stock} disponibles
                    </span>
                </div>
                <button 
                    onclick="addToCart(${product.id})"
                    class="w-full mt-4 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 ${product.stock === 0 ? 'opacity-50 cursor-not-allowed' : ''}"
                    ${product.stock === 0 ? 'disabled' : ''}
                >
                    <i class="fas fa-cart-plus"></i>
                    ${product.stock === 0 ? 'Agotado' : 'Agregar al carrito'}
                </button>
            </div>
        </div>
    `;
}

// Render cart modal
function renderCartModal() {
    return `
        <div id="cartModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden">
                <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Carrito de Compras
                    </h2>
                    <button onclick="toggleCart()" class="text-2xl hover:text-blue-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    ${cart.length === 0 ? `
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                            <p>Tu carrito está vacío</p>
                        </div>
                    ` : `
                        <div class="space-y-4">
                            ${cart.map(item => `
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800">${item.product_name}</h4>
                                        <p class="text-blue-600 font-bold">$${item.price.toFixed(2)}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button onclick="updateQuantity(${item.product_id}, -1)" class="w-8 h-8 bg-gray-200 rounded-full hover:bg-gray-300 transition">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span class="w-8 text-center font-semibold">${item.quantity}</span>
                                        <button onclick="updateQuantity(${item.product_id}, 1)" class="w-8 h-8 bg-gray-200 rounded-full hover:bg-gray-300 transition">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="ml-4 text-right">
                                        <p class="font-bold text-gray-800">$${(item.price * item.quantity).toFixed(2)}</p>
                                        <button onclick="removeFromCart(${item.product_id})" class="text-red-500 hover:text-red-700 text-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                        
                        <div class="mt-6 pt-4 border-t">
                            <div class="flex justify-between items-center text-xl font-bold">
                                <span>Total:</span>
                                <span class="text-blue-600">$${getCartTotal().toFixed(2)}</span>
                            </div>
                        </div>
                    `}
                </div>
                
                ${cart.length > 0 ? `
                    <div class="border-t p-6">
                        <form onsubmit="submitOrder(event)" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tu nombre completo">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="tu@email.com">
                            </div>
                            <button type="submit" class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors font-semibold">
                                <i class="fas fa-check mr-2"></i>
                                Confirmar Pedido
                            </button>
                        </form>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
}

// Toggle cart modal
function toggleCart() {
    const modal = document.getElementById('cartModal');
    modal.classList.toggle('hidden');
}

// Start the app
init();
