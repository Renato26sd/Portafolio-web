<?php
/**
 * Página principal - Redirecciona al login o dashboard
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/helpers/Session.php';

Session::start();

if (Session::get('user_id')) {
    header('Location: /dashboard.php');
} else {
    header('Location: /login.php');
}

exit;
