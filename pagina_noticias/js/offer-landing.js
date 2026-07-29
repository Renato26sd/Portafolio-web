// Countdown Timer for Offer
function initCountdown() {
  const countdownElement = document.getElementById('countdown');
  if (!countdownElement) return;

  // Set target time (24 hours from now)
  const targetTime = new Date().getTime() + (24 * 60 * 60 * 1000);

  function updateCountdown() {
    const now = new Date().getTime();
    const distance = targetTime - now;

    if (distance < 0) {
      countdownElement.innerHTML = "EXPIRADO";
      return;
    }

    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    countdownElement.innerHTML = 
      String(hours).padStart(2, '0') + ':' +
      String(minutes).padStart(2, '0') + ':' +
      String(seconds).padStart(2, '0');
  }

  // Update immediately
  updateCountdown();
  
  // Update every second
  setInterval(updateCountdown, 1000);
}

// Lead Form Handler
function initLeadForm() {
  const leadForm = document.getElementById('leadForm');
  if (!leadForm) return;

  leadForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(leadForm);
    const name = leadForm.querySelector('input[type="text"]').value;
    const email = leadForm.querySelector('input[type="email"]').value;
    const phone = leadForm.querySelector('input[type="tel"]').value;
    const interest = leadForm.querySelector('select').value;
    
    // Validate form
    if (!name || !email) {
      showNotification('Por favor completa los campos obligatorios', 'error');
      return;
    }

    // Show success message
    showNotification('¡Gracias! Tu oferta exclusiva ha sido activada. Revisa tu email.', 'success');
    
    // Reset form
    leadForm.reset();
    
    // Store lead (in real app, this would be sent to a server)
    const leadData = {
      name,
      email,
      phone,
      interest,
      timestamp: new Date().toISOString()
    };
    
    console.log('Nuevo lead capturado:', leadData);
    
    // Optionally store in localStorage
    let leads = JSON.parse(localStorage.getItem('leads') || '[]');
    leads.push(leadData);
    localStorage.setItem('leads', JSON.stringify(leads));
  });
}

// Notification System
function showNotification(message, type = 'info') {
  // Remove existing notifications
  const existingNotification = document.querySelector('.notification-toast');
  if (existingNotification) {
    existingNotification.remove();
  }

  // Create notification element
  const notification = document.createElement('div');
  notification.className = `notification-toast notification-${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : type === 'error' ? 'bi-x-circle-fill' : 'bi-info-circle-fill'}"></i>
      <span>${message}</span>
    </div>
  `;

  // Add to page
  document.body.appendChild(notification);

  // Auto remove after 5 seconds
  setTimeout(() => {
    notification.remove();
  }, 5000);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  initCountdown();
  initLeadForm();
});

// Add notification styles dynamically
const notificationStyles = `
<style>
.notification-toast {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  min-width: 300px;
  max-width: 400px;
  padding: 1rem;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  animation: slideInRight 0.3s ease-out;
  backdrop-filter: blur(10px);
}

.notification-success {
  background: linear-gradient(135deg, rgba(40, 167, 69, 0.9), rgba(76, 175, 80, 0.9));
  color: white;
  border: 1px solid rgba(40, 167, 69, 0.3);
}

.notification-error {
  background: linear-gradient(135deg, rgba(220, 53, 69, 0.9), rgba(244, 67, 54, 0.9));
  color: white;
  border: 1px solid rgba(220, 53, 69, 0.3);
}

.notification-info {
  background: linear-gradient(135deg, rgba(23, 162, 184, 0.9), rgba(33, 150, 243, 0.9));
  color: white;
  border: 1px solid rgba(23, 162, 184, 0.3);
}

.notification-content {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
</style>
`;

// Add styles to head
document.head.insertAdjacentHTML('beforeend', notificationStyles);
