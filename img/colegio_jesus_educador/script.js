// ===== DOM ELEMENTS =====
document.addEventListener('DOMContentLoaded', function() {
    // Navigation toggle
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');
    const navLinks = document.querySelectorAll('.nav-link');

    // Header scroll effect
    const header = document.querySelector('.header');
    
    // Gallery containers
    const actividadesGallery = document.getElementById('actividades-gallery');
    const paseosGallery = document.getElementById('paseos-gallery');
    const olimpiadasGallery = document.getElementById('olimpiadas-gallery');

    // Form
    const contactForm = document.querySelector('.form');

    // ===== NAVIGATION MOBILE TOGGLE =====
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navToggle.classList.toggle('active');
            navMenu.classList.toggle('active');
        });

        // Close menu when clicking on a link
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navToggle.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }

    // ===== HEADER SCROLL EFFECT =====
    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.98)';
            header.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.15)';
        } else {
            header.style.background = 'rgba(255, 255, 255, 0.95)';
            header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
        }
    });

    // ===== ACTIVE NAVIGATION LINK =====
    function setActiveNavLink() {
        const sections = document.querySelectorAll('section[id]');
        const scrollY = window.pageYOffset;

        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 100;
            const sectionId = section.getAttribute('id');
            const navLink = document.querySelector(`.nav-link[href="#${sectionId}"]`);

            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                navLinks.forEach(link => link.classList.remove('active'));
                if (navLink) {
                    navLink.classList.add('active');
                }
            }
        });
    }

    window.addEventListener('scroll', setActiveNavLink);

    // ===== LOAD GALLERY IMAGES =====
    function loadGalleryImages() {
        // Actividades Escolares
        const actividadesImages = [];
        for (let i = 1; i <= 11; i++) {
            actividadesImages.push(`actividades_escolares/actividad${i}.jpg`);
        }
        createGallery(actividadesGallery, actividadesImages, 'Actividad Escolar');

        // Paseos Escolares
        const paseosImages = [];
        for (let i = 1; i <= 9; i++) {
            paseosImages.push(`imganes_paseo/paseo${i}.jpg`);
        }
        createGallery(paseosGallery, paseosImages, 'Paseo Educativo');

        // Olimpiadas
        const olimpiadasImages = [];
        for (let i = 1; i <= 12; i++) {
            olimpiadasImages.push(`olimpiadas/olimpiada${i}.jpg`);
        }
        createGallery(olimpiadasGallery, olimpiadasImages, 'Olimpiada Deportiva');
        
        // Force image loading by creating temporary Image objects
        forceLoadImages([...actividadesImages, ...paseosImages, ...olimpiadasImages]);
    }

    function forceLoadImages(imageUrls) {
        imageUrls.forEach(url => {
            const tempImg = new Image();
            tempImg.src = url;
        });
    }

    function createGallery(container, images, altText) {
        if (!container) return;

        images.forEach((src, index) => {
            const galleryItem = document.createElement('div');
            galleryItem.className = 'gallery-item';
            galleryItem.setAttribute('data-aos', 'fade-up');
            galleryItem.setAttribute('data-aos-delay', index * 100);

            const img = document.createElement('img');
            img.src = src;
            img.alt = `${altText} ${index + 1}`;
            img.loading = 'lazy';
            // No establecer opacity aquí, el lazy loading se encargará
            img.style.transition = 'opacity 0.5s ease';
            
            // Handle image load - solo para efectos visuales, no para mostrar
            img.addEventListener('load', function() {
                // La imagen ya está visible, solo añadimos un efecto sutil
                img.style.filter = 'brightness(1.0)';
            });
            
            // Handle image error
            img.addEventListener('error', function() {
                console.error(`Error loading image: ${src}`);
                // La imagen ya está visible, solo cambiamos el fondo
                img.style.background = `linear-gradient(135deg, 
                    hsl(${index * 30}, 70%, 60%) 0%, 
                    hsl(${index * 30 + 60}, 70%, 50%) 100%)`;
                img.src = ''; // Remove broken image icon
            });

            const overlay = document.createElement('div');
            overlay.className = 'gallery-overlay';

            const text = document.createElement('div');
            text.className = 'gallery-text';
            text.innerHTML = `<h4>${altText} ${index + 1}</h4>`;

            overlay.appendChild(text);
            galleryItem.appendChild(img);
            galleryItem.appendChild(overlay);

            // Click event for lightbox
            galleryItem.addEventListener('click', function() {
                openLightbox(src, `${altText} ${index + 1}`);
            });

            container.appendChild(galleryItem);
        });
    }

    // ===== LIGHTBOX =====
    function openLightbox(imageSrc, imageAlt) {
        // Create lightbox elements
        const lightbox = document.createElement('div');
        lightbox.className = 'lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <span class="lightbox-close">&times;</span>
                <img src="${imageSrc}" alt="${imageAlt}" class="lightbox-image">
                <div class="lightbox-caption">${imageAlt}</div>
            </div>
        `;

        // Add lightbox styles
        const lightboxStyles = `
            .lightbox {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.9);
                z-index: 2000;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
            .lightbox.active {
                opacity: 1;
                visibility: visible;
            }
            .lightbox-content {
                position: relative;
                max-width: 90%;
                max-height: 90%;
                text-align: center;
            }
            .lightbox-image {
                max-width: 100%;
                max-height: 80vh;
                border-radius: 10px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            }
            .lightbox-close {
                position: absolute;
                top: -40px;
                right: 0;
                font-size: 2rem;
                color: white;
                cursor: pointer;
                transition: transform 0.3s ease;
            }
            .lightbox-close:hover {
                transform: scale(1.2);
            }
            .lightbox-caption {
                color: white;
                margin-top: 1rem;
                font-size: 1.1rem;
                font-family: 'Montserrat', sans-serif;
            }
        `;

        // Add styles to head
        const styleSheet = document.createElement('style');
        styleSheet.textContent = lightboxStyles;
        document.head.appendChild(styleSheet);

        // Add to body
        document.body.appendChild(lightbox);

        // Show lightbox with animation
        setTimeout(() => {
            lightbox.classList.add('active');
        }, 10);

        // Close events
        const closeLightbox = function() {
            lightbox.classList.remove('active');
            setTimeout(() => {
                document.body.removeChild(lightbox);
                document.head.removeChild(styleSheet);
            }, 300);
        };

        lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });

        // Close with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });
    }

    // ===== FORM SUBMISSION =====
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(contactForm);
            const formValues = {};
            
            // Convert FormData to object
            for (let [key, value] of formData.entries()) {
                formValues[key] = value;
            }
            
            // Show success message
            showNotification('¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.', 'success');
            
            // Reset form
            contactForm.reset();
        });
    }

    // ===== NOTIFICATION SYSTEM =====
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        // Add notification styles
        const notificationStyles = `
            .notification {
                position: fixed;
                top: 100px;
                right: 20px;
                background: white;
                padding: 1rem 1.5rem;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                z-index: 3000;
                transform: translateX(400px);
                transition: transform 0.3s ease;
                max-width: 350px;
            }
            .notification.show {
                transform: translateX(0);
            }
            .notification-success {
                border-left: 4px solid #10b981;
            }
            .notification-error {
                border-left: 4px solid #ef4444;
            }
            .notification-content {
                display: flex;
                align-items: center;
                gap: 1rem;
            }
            .notification-success i {
                color: #10b981;
                font-size: 1.2rem;
            }
            .notification-error i {
                color: #ef4444;
                font-size: 1.2rem;
            }
            .notification span {
                color: #374151;
                font-family: 'Open Sans', sans-serif;
            }
        `;

        // Add styles to head
        let notificationStyleSheet = document.getElementById('notification-styles');
        if (!notificationStyleSheet) {
            notificationStyleSheet = document.createElement('style');
            notificationStyleSheet.id = 'notification-styles';
            notificationStyleSheet.textContent = notificationStyles;
            document.head.appendChild(notificationStyleSheet);
        }

        // Add to body
        document.body.appendChild(notification);

        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Hide after 5 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 5000);
    }

    // ===== SMOOTH SCROLL FOR ANCHOR LINKS =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const offsetTop = target.offsetTop - 80; // Account for fixed header
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ===== LAZY LOADING FOR IMAGES =====
    function setupLazyLoading() {
        const images = document.querySelectorAll('img[loading="lazy"]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.style.transition = 'opacity 0.5s ease';
                    img.style.opacity = '1';
                    observer.unobserve(img);
                }
            });
        });

        images.forEach(img => {
            img.style.opacity = '0';
            imageObserver.observe(img);
        });
    }

    // ===== ANIMATION ON SCROLL =====
    function setupScrollAnimations() {
        const animatedElements = document.querySelectorAll('[data-aos]');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });

        animatedElements.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(element);
        });
    }

    // ===== WHATSAPP BUTTON ANIMATION =====
    function setupWhatsAppButton() {
        const whatsappBtn = document.querySelector('.whatsapp-btn');
        if (whatsappBtn) {
            // Add pulse animation on hover
            whatsappBtn.addEventListener('mouseenter', function() {
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'whatsappPulse 2s infinite';
                }, 10);
            });
        }
    }

    // ===== INITIALIZATION =====
    function init() {
        loadGalleryImages();
        setupLazyLoading();
        setupScrollAnimations();
        setupWhatsAppButton();
        setActiveNavLink();
    }

    // Initialize everything
    init();
});
