<?php

use App\Controllers\UserController;
use App\Middlewares\SessionRedirectMiddleware;


if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Iniciar sesión si no está iniciada
}
define('APP_ROOT', __DIR__ . '/'); // Define la ruta raíz de la aplicación


require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Language;

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'en';
$traducciones = Language::loadLanguage($lang);




// PASO 2: IMPORTAR LAS CLASES QUE VAS A UTILIZAR
// Esto hace que el código sea más legible.
use App\Router;
use App\Core\ViewRenderer;
use App\Controllers\CountryController; // Asegúrate de que la ruta sea correcta
use App\Controllers\AuthController;
use App\Middlewares\AuthMiddleware; // Asumiendo que creas esta clase en /middleware/

// --- INICIALIZACIÓN Y DEFINICIÓN DE RUTAS ---

$viewRenderer = new ViewRenderer($traducciones);
$router = new Router($viewRenderer);




// --- GRUPO DE RUTAS API (RESPUESTAS JSON) ---
// Estas rutas devuelven datos en lugar de una vista HTML.
$router->group(['prefix' => '/api'], function ($router) {

    /**
     * RUTA API 1: Pública
     * Devuelve una lista de usuarios en formato JSON. No requiere autenticación.
     */
    $router->get('/countries', [
        'controlador' => CountryController::class,
        'accion' => 'showAll'
    ]);

    // Inicisar sesión
    $router->post('/login', [
        'controlador' => AuthController::class,
        'accion' => 'login'
    ]);

    $router->get('/logout', [
        'controlador' => AuthController::class,
        'accion' => 'logout',

    ]);

    // Verificar telefono
    $router->post('/check/telephone', [
        'controlador' => UserController::class,
        'accion' => 'showByTelephone'
    ]);
    /**
     * RUTA API 2: Protegida por Rol
     * Devuelve datos sensibles. Requiere autenticación y el rol 'admin'.
     */
});

// GRUPO DE RUTAS PARA VISTAS PROTEGIDAS POR MIDDLEWARE
$router->group(['middleware' => AuthMiddleware::class], function ($router) use ($traducciones) {



    $router->get('/home', [
        'vista' => '/modules/home',
        'vistaData' => ['title' => $traducciones['home_title'] ?? 'home']
    ]);

});

// REDERIGIR A LA VISTA HOME SI HAY UNA SESIÓN ACTIVA VERICADA POR MIDDLEWARE
$router->group(['middleware' => SessionRedirectMiddleware::class], function ($router) use ($traducciones) {

    $router->get('/', [
        'vista' => '/auth/login',
        'vistaData' => ['title' => $traducciones['login_title'] ?? 'Login', 'layout' => false]
    ]);
    $router->get('/login', [
        'vista' => '/auth/login',
        'vistaData' => ['title' => $traducciones['login_title'] ?? 'Login', 'layout' => false]
    ]);
});



// Grupo de rutas para el panel de administración
// $router->group(['prefix' => '/admin', 'middleware' => AuthMiddleware::class], function ($router) {

//     $router->get('/dashboard', ['vista' => 'admin/dashboard']);

// });

// --- EJECUTAR EL ENRUTADOR ---
$router->route();