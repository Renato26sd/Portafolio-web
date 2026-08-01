<?php
/**
 * Página de crear usuario
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Session.php';

Session::start();

// Verificar autenticación
if (!Session::get('user_id')) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../app/models/Usuario.php';
require_once __DIR__ . '/../app/models/Rol.php';

$usuarioModel = new Usuario();
$rolModel = new Rol();

$roles = $rolModel->obtenerTodos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lógica para guardar usuario
    $datos = [
        'nombres' => $_POST['nombres'] ?? null,
        'apellidos' => $_POST['apellidos'] ?? null,
        'correo' => $_POST['correo'] ?? null,
        'usuario' => $_POST['usuario'] ?? null,
        'contraseña' => $_POST['contraseña'] ?? null,
        'id_rol' => $_POST['id_rol'] ?? null,
        'telefono' => $_POST['telefono'] ?? null,
        'direccion' => $_POST['direccion'] ?? null,
    ];
    
    if (!$datos['nombres'] || !$datos['apellidos'] || !$datos['correo'] || !$datos['usuario'] || !$datos['contraseña'] || !$datos['id_rol']) {
        Session::setFlash('error', 'Complete los campos requeridos');
        header('Location: /usuarios/crear.php');
        exit;
    }
    
    $idUsuario = $usuarioModel->crear($datos);
    
    if ($idUsuario) {
        Session::setFlash('success', 'Usuario creado exitosamente');
        header('Location: /usuarios/index.php');
    } else {
        Session::setFlash('error', 'Error al crear usuario');
        header('Location: /usuarios/crear.php');
    }
    exit;
}

require_once __DIR__ . '/../app/views/usuarios/crear.php';
