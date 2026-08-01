/**
 * JavaScript principal
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

// Mostrar alertas
function showAlert(type, message) {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const icon = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };
    
    const alertHtml = `
        <div class="alert ${alertClass[type]} alert-dismissible fade show" role="alert">
            <i class="fas ${icon[type]} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Crear contenedor de alertas si no existe
    let alertContainer = document.querySelector('.alert-container');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.className = 'alert-container position-fixed top-0 end-0 p-3';
        alertContainer.style.zIndex = '9999';
        document.body.appendChild(alertContainer);
    }
    
    const alertElement = document.createElement('div');
    alertElement.innerHTML = alertHtml;
    alertContainer.appendChild(alertElement);
    
    // Auto eliminar después de 5 segundos
    setTimeout(() => {
        const alert = alertElement.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Confirmar acción
function confirmAction(message) {
    return confirm(message || '¿Está seguro de realizar esta acción?');
}

// Formatear moneda
function formatCurrency(amount) {
    return 'S/ ' + parseFloat(amount).toFixed(2);
}

// Formatear fecha
function formatDate(date) {
    const d = new Date(date);
    return d.toLocaleDateString('es-PE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Formatear fecha y hora
function formatDateTime(date) {
    const d = new Date(date);
    return d.toLocaleString('es-PE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Mostrar spinner de carga
function showSpinner() {
    const spinner = document.createElement('div');
    spinner.className = 'spinner-overlay';
    spinner.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    `;
    document.body.appendChild(spinner);
}

// Ocultar spinner de carga
function hideSpinner() {
    const spinner = document.querySelector('.spinner-overlay');
    if (spinner) {
        spinner.remove();
    }
}

// Validar formulario
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Buscar productos para venta
function searchProducts(term, callback) {
    fetch(`/productos/buscar.php?termino=${encodeURIComponent(term)}`)
        .then(response => response.json())
        .then(data => callback(data))
        .catch(error => console.error('Error:', error));
}

// Agregar producto al carrito
function addToCart(productId, quantity, price) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    const existingItem = cart.find(item => item.id_producto === productId);
    
    if (existingItem) {
        existingItem.cantidad += quantity;
        existingItem.subtotal = existingItem.cantidad * existingItem.precio;
    } else {
        cart.push({
            id_producto: productId,
            cantidad: quantity,
            precio: price,
            subtotal: quantity * price
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
}

// Actualizar display del carrito
function updateCartDisplay() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartCount = document.getElementById('cart-count');
    const cartTotal = document.getElementById('cart-total');
    
    if (cartCount) {
        cartCount.textContent = cart.length;
    }
    
    if (cartTotal) {
        const total = cart.reduce((sum, item) => sum + item.subtotal, 0);
        cartTotal.textContent = formatCurrency(total);
    }
}

// Limpiar carrito
function clearCart() {
    localStorage.removeItem('cart');
    updateCartDisplay();
}

// Eliminar item del carrito
function removeFromCart(productId) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const updatedCart = cart.filter(item => item.id_producto !== productId);
    localStorage.setItem('cart', JSON.stringify(updatedCart));
    updateCartDisplay();
}

// Actualizar cantidad de item en carrito
function updateCartItemQuantity(productId, quantity) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const item = cart.find(item => item.id_producto === productId);
    
    if (item) {
        item.cantidad = quantity;
        item.subtotal = item.cantidad * item.precio;
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartDisplay();
    }
}

// Previsualizar imagen
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Exportar a Excel
function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    const wb = XLSX.utils.table_to_book(table, {sheet: "Sheet 1"});
    XLSX.writeFile(wb, filename);
}

// Imprimir
function printElement(elementId) {
    const element = document.getElementById(elementId);
    const originalContent = document.body.innerHTML;
    
    document.body.innerHTML = element.innerHTML;
    window.print();
    document.body.innerHTML = originalContent;
    
    location.reload();
}

// Copiar al portapapeles
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('success', 'Copiado al portapapeles');
    }).catch(err => {
        showAlert('error', 'Error al copiar');
    });
}

// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Inicializar popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Actualizar display del carrito al cargar
    updateCartDisplay();
});

// Función para eliminar registro con confirmación
function deleteRecord(url, message) {
    if (confirmAction(message)) {
        showSpinner();
        window.location.href = url;
    }
}

// Función para cambiar estado
function changeStatus(url, status) {
    if (confirmAction(`¿Desea cambiar el estado a ${status}?`)) {
        showSpinner();
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'estado';
        statusInput.value = status;
        
        const csrfToken = document.querySelector('input[name="csrf_token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = csrfToken.value;
            form.appendChild(csrfInput);
        }
        
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Paginación AJAX
function loadPage(url) {
    showSpinner();
    window.location.href = url;
}

// Filtrar tabla
function filterTable(tableId, searchTerm) {
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    const term = searchTerm.toLowerCase();
    
    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length; j++) {
            if (cells[j].textContent.toLowerCase().indexOf(term) > -1) {
                found = true;
                break;
            }
        }
        
        rows[i].style.display = found ? '' : 'none';
    }
}

// Calcular totales en tiempo real
function calculateTotals() {
    const inputs = document.querySelectorAll('.calculate-total');
    let total = 0;
    
    inputs.forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    
    const totalElement = document.getElementById('grand-total');
    if (totalElement) {
        totalElement.textContent = formatCurrency(total);
    }
    
    return total;
}

// Validar RUC peruano
function validateRUC(ruc) {
    if (ruc.length !== 11) return false;
    if (!/^\d+$/.test(ruc)) return false;
    
    // Validación básica de RUC peruano
    return true;
}

// Validar DNI peruano
function validateDNI(dni) {
    if (dni.length !== 8) return false;
    if (!/^\d+$/.test(dni)) return false;
    
    return true;
}

// Generar código único
function generateUniqueCode(prefix) {
    const timestamp = Date.now().toString(36);
    const random = Math.random().toString(36).substr(2, 9);
    return `${prefix}-${timestamp}-${random}`.toUpperCase();
}

// Debounce function para búsquedas
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
