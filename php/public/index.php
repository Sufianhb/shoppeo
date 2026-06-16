<?php

/**
 * Front Controller (punto de entrada unico)
 * Toda peticion HTTP pasa por aquí gracias al .htaccess
 */

declare(strict_types=1);

// Arranque
define('ROOT_PATH',   dirname(__DIR__));
define('APP_PATH',    ROOT_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

// Autoloader PSR-4 sencillo (sin Composer para mantener dependencias mínimas)
spl_autoload_register(function (string $class): void {
    $file = APP_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Variables de entorno
define('DB_HOST', $_ENV['DB_HOST'] ?? 'db');
define('DB_PORT', $_ENV['DB_PORT'] ?? '5432');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'shoppeo');
define('DB_USER', $_ENV['DB_USER'] ?? 'tmuser');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'tmpassword');
define('APP_SECRET',  $_ENV['APP_SECRET']  ?? 's3cr3t_k3y_tfg_2024');
define('WS_URL',      'http://websocket:3000');
define('WS_SECRET',   'ws_secret_tfg_2024');
define('REDIS_HOST',  $_ENV['REDIS_HOST']  ?? 'redis');
define('REDIS_PORT',  (int)($_ENV['REDIS_PORT'] ?? 6379));

// Sesion segura
session_set_cookie_params([
    'lifetime' => 7200,
    'path' => '/',
    'secure' => false,   // true en produccion con HTTPS
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

// Router
require_once APP_PATH . '/core/Router.php';

$router = new core\Router();

// Rutas publicas
$router->get('/', 'PublicController', 'index');
$router->get('/buscar', 'PublicController', 'buscar');
$router->get('/producto/{id}', 'PublicController', 'producto');
$router->get('/mapa', 'PublicController', 'mapa');
$router->get('/ayuda', 'PublicController', 'ayuda');
$router->post('/ayuda', 'PublicController', 'ayuda');

// Rutas de autenticacion
$router->get('/login', 'AuthController', 'loginForm');
$router->post('/login', 'AuthController', 'login');
$router->get('/logout', 'AuthController', 'logout');
$router->get('/registro', 'AuthController', 'registroForm');
$router->post('/registro', 'AuthController', 'registro');

// Rutas de perfil de usuario
$router->get('/configuracion', 'UserController', 'configuracion');
$router->post('/configuracion', 'UserController', 'guardarConfiguracion');

// Rutas de favoritos
$router->get('/favoritos', 'FavoritosController', 'index');
$router->post('/favoritos/toggle', 'FavoritosController', 'toggle');

// Rutas de puntos
$router->get('/puntos', 'PuntosController', 'index');
$router->post('/puntos/canjear', 'PuntosController', 'canjear');

// Rutas de descuentos
$router->get('/descuentos', 'DescuentosController', 'index');
$router->post('/descuentos/usar', 'DescuentosController', 'usar');

// Rutas de administracion
$router->get('/admin', 'AdminController', 'dashboard');
$router->get('/admin/productos', 'AdminController', 'productos');
$router->get('/admin/productos/crear', 'AdminController', 'crearProductoForm');
$router->post('/admin/productos/crear', 'AdminController', 'crearProducto');
$router->get('/admin/productos/{id}/editar', 'AdminController', 'editarProductoForm');
$router->post('/admin/productos/{id}/editar', 'AdminController', 'editarProducto');
$router->post('/admin/productos/{id}/eliminar', 'AdminController', 'eliminarProducto');
$router->get('/admin/precios', 'AdminController', 'precios');
$router->post('/admin/precios/actualizar', 'AdminController', 'actualizarPrecio');
$router->get('/admin/tiendas', 'AdminController', 'tiendas');
$router->post('/admin/tiendas/crear', 'AdminController', 'crearTienda');
$router->get('/admin/usuarios', 'AdminController', 'usuarios');
$router->get('/admin/usuarios/{id}/editar', 'AdminController', 'editarUsuarioForm');
$router->post('/admin/usuarios/{id}/editar', 'AdminController', 'editarUsuario');
$router->post('/admin/usuarios/{id}/toggle', 'AdminController', 'toggleUsuario');
$router->post('/admin/usuarios/{id}/eliminar', 'AdminController', 'eliminarUsuario');
$router->post('/admin/usuarios/{id}/reset-password', 'AdminController', 'resetPasswordUsuario');
$router->get('/admin/actividad', 'AdminController', 'actividad');
$router->get('/admin/configuracion', 'AdminController', 'configuracion');
$router->post('/admin/configuracion', 'AdminController', 'guardarConfiguracion');
$router->get('/admin/logs', 'AdminController', 'logs');
$router->get('/admin/puntos', 'AdminController', 'puntosAdmin');
$router->post('/admin/puntos/otorgar', 'AdminController', 'otorgarPuntos');
$router->get('/admin/canjes', 'AdminController', 'canjesAdmin');
$router->post('/admin/canjes/{id}/estado', 'AdminController', 'actualizarCanje');

// Rutas API (AJAX)
$router->get('/api/sugerencias',    'ApiController', 'sugerencias');
$router->get('/api/productos',      'ApiController', 'productos');
$router->get('/api/productos/{id}', 'ApiController', 'producto');
$router->get('/api/tiendas',        'ApiController', 'tiendas');
$router->get('/api/mapa',           'ApiController', 'mapa');
$router->get('/api/tags-populares', 'ApiController', 'tagsPopulares');
$router->get('/api/chollos',        'ApiController', 'chollos');
$router->post('/api/ws-notify',     'ApiController', 'wsNotify');
$router->post('/api/location',      'ApiController', 'location');

// Despachar la peticion actual
$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);
