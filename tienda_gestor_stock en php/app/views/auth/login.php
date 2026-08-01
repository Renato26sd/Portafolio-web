<?php
$pageTitle = 'Iniciar Sesión';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="login-container">
    <div class="login-box">
        <div class="text-center mb-4">
            <i class="fas fa-warehouse fa-3x text-primary"></i>
            <h2 class="mt-3"><?= SITE_NAME ?></h2>
            <p class="text-muted">Sistema de Ventas e Inventario</p>
        </div>
        
        <div class="card shadow">
            <div class="card-body p-4">
                <form method="POST" action="/login.php">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">
                            <i class="fas fa-user me-2"></i>Usuario
                        </label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contraseña" class="form-label">
                            <i class="fas fa-lock me-2"></i>Contraseña
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="contraseña" name="contraseña" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </button>
                </form>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="fas fa-shield-alt me-1"></i>
                Sistema seguro con encriptación de contraseñas
            </small>
        </div>
    </div>
</div>

<style>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
}

.login-box {
    width: 100%;
    max-width: 400px;
}

.login-box .card {
    border: none;
    border-radius: 15px;
}
</style>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('contraseña');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
