<?php
/**
 * Página de editar usuario
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

$id = $_GET['id'] ?? null;

if (!$id) {
    Session::setFlash('error', 'ID de usuario no proporcionado');
    header('Location: /usuarios/index.php');
    exit;
}

$usuario = $usuarioModel->obtenerPorId($id);

if (!$usuario) {
    Session::setFlash('error', 'Usuario no encontrado');
    header('Location: /usuarios/index.php');
    exit;
}

$roles = $rolModel->obtenerTodos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lógica para actualizar usuario
    $datos = [
        'nombres' => $_POST['nombres'] ?? null,
        'apellidos' => $_POST['apellidos'] ?? null,
        'usuario' => $_POST['usuario'] ?? null,
        'id_rol' => $_POST['id_rol'] ?? null,
    ];
    
    if ($_POST['contraseña']) {
        $datos['contraseña'] = $_POST['contraseña'];
    }
    
    if (!$datos['nombres'] || !$datos['apellidos'] || !$datos['usuario']) {
        Session::setFlash('error', 'Complete los campos requeridos');
        header('Location: /usuarios/editar.php?id=' . $id);
        exit;
    }
    
    $resultado = $usuarioModel->actualizar($id, $datos);
    
    if ($resultado) {
        Session::setFlash('success', 'Usuario actualizado exitosamente');
        header('Location: /usuarios/index.php');
    } else {
        Session::setFlash('error', 'Error al actualizar usuario');
        header('Location: /usuarios/editar.php?id=' . $id);
    }
    exit;
}

require_once __DIR__ . '/../app/views/usuarios/editar.php';
