<?php
/**
 * Página de logout
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/helpers/Session.php';

Session::start();

Session::destroy();
header('Location: /login.php');
exit;
