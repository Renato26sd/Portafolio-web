<?php
/**
 * Página de login
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/Session.php';
require_once __DIR__ . '/app/models/Usuario.php';

Session::start();

// Redireccionar si ya está logueado
if (Session::isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';
    
    if (empty($usuario) || empty($contraseña)) {
        Session::setFlash('error', 'Por favor ingrese usuario y contraseña');
        header('Location: /login.php');
        exit;
    }
    
    $usuarioModel = new Usuario();
    $resultado = $usuarioModel->autenticar($usuario, $contraseña);
    
    if (isset($resultado['error'])) {
        Session::setFlash('error', $resultado['error']);
        header('Location: /login.php');
        exit;
    }
    
    // Iniciar sesión y guardar datos del usuario
    Session::regenerate();
    Session::set('user_id', $resultado['id_usuario']);
    Session::set('user', $resultado);
    Session::set('user_role', $resultado['nombre_rol']);
    Session::set('user_permissions', $resultado['permisos']);
    Session::set('last_activity', time());
    
    Session::setFlash('success', 'Bienvenido, ' . $resultado['nombres']);
    header('Location: /dashboard.php');
    exit;
}

// GET: Mostrar formulario
require_once __DIR__ . '/app/views/auth/login.php';
