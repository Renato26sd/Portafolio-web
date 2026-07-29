// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initScrollAnimations();
    initFormHandler();
    initMobileMenu();
    initSmoothScroll();
    initLoadingAnimations();
});

// Scroll animations for elements
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('loaded');
            }
        });
    }, observerOptions);

    // Observe all sections and cards
    const elementsToObserve = document.querySelectorAll('section, .service-card, .feature, .testimonial, .badge');
    elementsToObserve.forEach(el => {
        el.classList.add('loading');
        observer.observe(el);
    });
}

// Form handling with validation and submission
function initFormHandler() {
    const form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        // Validate form
        if (!validateForm(data)) {
            return;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        submitBtn.disabled = true;
        
        // Simulate form submission (replace with actual endpoint)
        setTimeout(() => {
            // Create WhatsApp message
            const whatsappMessage = createWhatsAppMessage(data);
            
            // Open WhatsApp with pre-filled message
            const whatsappUrl = `https://wa.me/51977851120?text=${encodeURIComponent(whatsappMessage)}`;
            window.open(whatsappUrl, '_blank');
            
            // Reset form
            form.reset();
            
            // Show success message
            showNotification('¡Mensaje enviado! Nos comunicaremos pronto.', 'success');
            
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 1500);
    });
    
    // Add real-time validation
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                validateField(this);
            }
        });
    });
}

// Form validation
function validateForm(data) {
    let isValid = true;
    const errors = [];
    
    if (!data.name || data.name.trim().length < 3) {
        errors.push('El nombre debe tener al menos 3 caracteres');
        isValid = false;
    }
    
    if (!data.phone || !isValidPhone(data.phone)) {
        errors.push('Por favor, ingrese un número de teléfono válido');
        isValid = false;
    }
    
    if (!data.message || data.message.trim().length < 10) {
        errors.push('La descripción debe tener al menos 10 caracteres');
        isValid = false;
    }
    
    if (!isValid) {
        showNotification(errors.join('<br>'), 'error');
    }
    
    return isValid;
}

// Field validation
function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Remove previous error styling
    field.classList.remove('error');
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    switch(field.type) {
        case 'text':
        case 'textarea':
            if (field.name === 'name' && value.length < 3) {
                isValid = false;
                errorMessage = 'El nombre debe tener al menos 3 caracteres';
            } else if (field.name === 'message' && value.length < 10) {
                isValid = false;
                errorMessage = 'La descripción debe tener al menos 10 caracteres';
            }
            break;
        case 'tel':
            if (!isValidPhone(value)) {
                isValid = false;
                errorMessage = 'Ingrese un número de teléfono válido';
            }
            break;
        case 'email':
            if (value && !isValidEmail(value)) {
                isValid = false;
                errorMessage = 'Ingrese un email válido';
            }
            break;
    }
    
    if (!isValid) {
        field.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = errorMessage;
        errorDiv.style.color = '#e74c3c';
        errorDiv.style.fontSize = '0.85rem';
        errorDiv.style.marginTop = '5px';
        field.parentNode.appendChild(errorDiv);
    }
    
    return isValid;
}

// Phone validation
function isValidPhone(phone) {
    const phoneRegex = /^[\d\s\-\+\(\)]+$/;
    return phoneRegex.test(phone) && phone.replace(/\D/g, '').length >= 9;
}

// Email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Create WhatsApp message from form data
function createWhatsAppMessage(data) {
    let message = '📋 *Solicitud de Servicio Técnico*\n\n';
    message += `👤 *Nombre:* ${data.name}\n`;
    message += `📞 *Teléfono:* ${data.phone}\n`;
    
    if (data.email) {
        message += `📧 *Email:* ${data.email}\n`;
    }
    
    if (data.location) {
        message += `📍 *Ubicación:* ${data.location}\n`;
    }
    
    message += `🔧 *Descripción del problema:* ${data.message}\n\n`;
    message += `📅 *Fecha:* ${new Date().toLocaleDateString('es-PE')}\n`;
    message += `⏰ *Hora:* ${new Date().toLocaleTimeString('es-PE')}\n\n`;
    message += 'Por favor, necesito asistencia técnica para mi fotocopiadora. ¡Gracias!';
    
    return message;
}

// Mobile menu functionality
function initMobileMenu() {
    // Add mobile menu toggle if needed
    const header = document.querySelector('.header');
    if (!header) return;
    
    // Create mobile menu button
    const mobileMenuBtn = document.createElement('button');
    mobileMenuBtn.className = 'mobile-menu-btn';
    mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    mobileMenuBtn.style.display = 'none';
    
    // Check screen size and show/hide mobile menu
    function checkScreenSize() {
        if (window.innerWidth <= 768) {
            mobileMenuBtn.style.display = 'block';
        } else {
            mobileMenuBtn.style.display = 'none';
        }
    }
    
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
    
    // Add mobile menu button to header
    const headerContent = header.querySelector('.header-content');
    if (headerContent) {
        headerContent.appendChild(mobileMenuBtn);
    }
}

// Smooth scroll for anchor links
function initSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                const headerHeight = document.querySelector('.header').offsetHeight;
                const targetPosition = targetElement.offsetTop - headerHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Loading animations
function initLoadingAnimations() {
    // Add staggered animation to service cards
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Add staggered animation to features
    const features = document.querySelectorAll('.feature');
    features.forEach((feature, index) => {
        feature.style.animationDelay = `${index * 0.1}s`;
    });
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${getNotificationIcon(type)}"></i>
            <span>${message}</span>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${getNotificationColor(type)};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        z-index: 10000;
        max-width: 400px;
        animation: slideIn 0.3s ease;
    `;
    
    const notificationContent = notification.querySelector('.notification-content');
    notificationContent.style.cssText = `
        display: flex;
        align-items: center;
        gap: 10px;
    `;
    
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.style.cssText = `
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        margin-left: auto;
        font-size: 1.2rem;
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, 5000);
    
    // Manual close
    closeBtn.addEventListener('click', () => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    });
}

// Get notification icon based on type
function getNotificationIcon(type) {
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    return icons[type] || icons.info;
}

// Get notification color based on type
function getNotificationColor(type) {
    const colors = {
        success: '#27ae60',
        error: '#e74c3c',
        warning: '#f39c12',
        info: '#3498db'
    };
    return colors[type] || colors.info;
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .error {
        border-color: #e74c3c !important;
    }
    
    .error-message {
        color: #e74c3c !important;
        font-size: 0.85rem !important;
        margin-top: 5px !important;
    }
    
    @media (max-width: 768px) {
        .notification {
            right: 10px !important;
            left: 10px !important;
            max-width: none !important;
        }
    }
`;
document.head.appendChild(style);

// Header scroll effect
window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (window.scrollY > 100) {
        header.style.background = 'linear-gradient(135deg, rgba(30, 60, 114, 0.95) 0%, rgba(42, 82, 152, 0.95) 100%)';
        header.style.backdropFilter = 'blur(10px)';
    } else {
        header.style.background = 'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)';
        header.style.backdropFilter = 'none';
    }
});

// Add hover effects to interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.5);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});

// Add ripple animation
const rippleStyle = document.createElement('style');
rippleStyle.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(rippleStyle);

// Sistema de Productos
let currentBrand = "all";
let currentCat = "all";

const products = [
    ...["2004","2504","3004","3504","5504","6004","4503","5503","6005"]
    .map(m=>({name:`Ricoh MPC ${m}`,brand:"ricoh",cat:"equipo"})),
    
    ...["2555","3055","4055","6055","2554","3054","3554","4054","5054","6054","7503","6503","9003","9002","7502"]
    .map(m=>({name:`Ricoh MP ${m}`,brand:"ricoh",cat:"equipo"})),
    
    ...["2500","3500","4500","5500"]
    .map(m=>({name:`Ricoh IM ${m}`,brand:"ricoh",cat:"equipo"})),
    
    ...["256","356","255","355","475","5240","5235","5255","5560","5540","5550","5535"]
    .map(m=>({name:`Canon ${m}`,brand:"canon",cat:"equipo"})),
    
    { name:"Cilindro Canon 5560", brand:"canon", cat:"repuesto"},
    { name:"Cilindro Ricoh", brand:"ricoh", cat:"repuesto"},
    { name:"Revelador", brand:"ricoh", cat:"repuesto"},
    { name:"Cuchilla Toner", brand:"ricoh", cat:"repuesto"},
    { name:"Faja Transferencia Canon", brand:"canon", cat:"repuesto"},
    { name:"Almohadilla Ricoh", brand:"ricoh", cat:"repuesto"},
    { name:"Pelicula Difusor Ricoh", brand:"ricoh", cat:"repuesto"},
    { name:"Unidad de Imagen Ricoh", brand:"ricoh", cat:"repuesto"},
    { name:"Unidad Fusor Ricoh", brand:"ricoh", cat:"repuesto"},
    { name:"Rodillo de Presión", brand:"ricoh", cat:"repuesto"},
    { name:"Unidad de Revelado Negro", brand:"ricoh", cat:"repuesto"},
    { name:"Toner Ricoh", brand:"ricoh", cat:"insumo"},
    { name:"Toner Canon", brand:"canon", cat:"insumo"}
];

function openProducts() {
    document.getElementById("productsModal").style.display = "block";
    renderProducts();
}

function closeProducts() {
    document.getElementById("productsModal").style.display = "none";
}

function setBrand(b) {
    currentBrand = b;
    renderProducts();
}

function setCat(c) {
    currentCat = c;
    renderProducts();
}

function renderProducts() {
    let grid = document.getElementById("productsGrid");
    grid.innerHTML = "";

    products.filter(p => 
        (currentBrand === "all" || p.brand === currentBrand) &&
        (currentCat === "all" || p.cat === currentCat)
    ).forEach(p => {
        
        // 🔥 Generación automática de rutas
        let imagePath = generateImagePath(p);
        
        // 🐛 Debug: mostrar rutas en consola para Canon
        if (p.brand === 'canon') {
            console.log(`Producto Canon: ${p.name} → Ruta: ${imagePath}`);
        }
        
        grid.innerHTML += `
            <div class="product-card">
                <img src="${imagePath}" alt="${p.name}" onerror="this.src='IMAGENES/no-image.jpg'">
                <h5>${p.name}</h5>
                <button onclick="waProduct('${p.name}')">Consultar WhatsApp</button>
            </div>
        `;
    });
}

// 🧠 Función inteligente de generación de rutas
function generateImagePath(product) {
    let fileName = product.name
        .toLowerCase()                          // minúsculas
        .replace(/\s+/g, '-')                  // espacios por guiones
        .replace(/[^a-z0-9\-]/g, '');          // solo letras, números y guiones
    
    // 📁 Detección automática de carpeta según categoría
    let folder = '';
    if (product.cat === 'equipo') {
        folder = `/${product.brand}`;            // /ricoh o /canon
    } else if (product.cat === 'repuesto') {
        folder = '/repuestos';                   // /repuestos
    } else if (product.cat === 'insumo') {
        folder = '/insumos';                    // /insumos
    }
    
    // 🔥 CORRECCIÓN ESPECIAL PARA CANON
    if (product.brand === 'canon' && product.cat === 'equipo') {
        fileName = `canon-ir-${fileName.replace('canon-', '')}`;
        
        // 🔥 CORRECCIÓN ESPECIAL PARA CANON 5235
        if (product.name === 'Canon 5235') {
            fileName = 'canon5235';
        }
    }
    
    // 🔥 CORRECCIÓN ESPECIAL PARA CUCHILLA TONER
    if (product.name === 'Cuchilla Toner') {
        fileName = 'cuchilla-toner';
        console.log(`Cuchilla Toner → Ruta: IMAGENES/repuestos/${fileName}.jpg`);
    }
    
    // 🔥 CORRECCIÓN ESPECIAL PARA CILINDRO RICOH
    if (product.name === 'Cilindro Ricoh') {
        fileName = 'cilindro-ricoh';
        console.log(`Cilindro Ricoh → Ruta: IMAGENES/repuestos/${fileName}.jpg`);
    }
    
    // � CORRECCIONES ESPECIALES PARA NUEVOS REPUESTOS
    if (product.name === 'Faja Transferencia Canon') {
        fileName = 'canon_faja_trasferencia';
    }
    if (product.name === 'Almohadilla Ricoh') {
        fileName = 'ricoh almohadilla';
    }
    if (product.name === 'Pelicula Difusor Ricoh') {
        fileName = 'ricoh_pelicula_difusor';
    }
    if (product.name === 'Unidad de Imagen Ricoh') {
        fileName = 'ricoh_unidad_de_imagen';
    }
    if (product.name === 'Unidad Fusor Ricoh') {
        fileName = 'ricoh_unidad_fusor';
    }
    if (product.name === 'Rodillo de Presión') {
        fileName = 'rodillo_de_precion';
    }
    if (product.name === 'Unidad de Revelado Negro') {
        fileName = 'unidad_de_revelado_negro';
    }
    
    // ��️ Construcción de ruta completa (ruta relativa)
    let finalPath = `IMAGENES${folder}/${fileName}.jpg`;
    console.log(`Producto: ${product.name} → Ruta final: ${finalPath}`);
    return finalPath;
}

// 📱 WhatsApp automático
function waProduct(name) {
    let msg = `Hola, deseo información del producto: ${name}`;
    window.open(`https://wa.me/51977851120?text=${encodeURIComponent(msg)}`);
}
