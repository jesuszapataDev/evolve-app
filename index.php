<?php

// ==================================================================
// ARCHIVO: index.php (Punto de entrada de la aplicación)
// ==================================================================

// PASO 1: INCLUIR EL AUTOLOADER DE COMPOSER
// Esta es la única línea de inclusión que necesitas.
require_once __DIR__ . '/vendor/autoload.php';

define('APP_ROOT', __DIR__ . '/'); // Define la ruta raíz de la aplicación

// PASO 2: IMPORTAR LAS CLASES QUE VAS A UTILIZAR
// Esto hace que el código sea más legible.
use App\Router;
use App\Core\ViewRenderer;
use App\Controllers\CountryController; // Asegúrate de que la ruta sea correcta
use App\Middlewares\AuthMiddleware; // Asumiendo que creas esta clase en /middleware/

// --- INICIALIZACIÓN Y DEFINICIÓN DE RUTAS ---

$viewRenderer = new ViewRenderer();
$router = new Router($viewRenderer);

// Usamos ::class para referirnos a las clases. Es una mejor práctica
// porque los editores de código pueden detectar errores si la clase no existe.
$router->get('/', [
    'vista' => '/auth/login',
    'vistaData' => ['title' => $traducciones['login_title'] ?? 'Login', 'layout' => false]
]);
$router->get('/login', [
    'vista' => '/auth/login',
    'vistaData' => ['title' => $traducciones['login_title'] ?? 'Login']
]);

// Rutas para modulos

$router->get('/home', [
    'vista' => '/modules/home',
    'vistaData' => ['title' => $traducciones['home_title'] ?? 'home']
]);


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

    /**
     * RUTA API 2: Protegida por Rol
     * Devuelve datos sensibles. Requiere autenticación y el rol 'admin'.
     */

});

// Grupo de rutas para el panel de administración
// $router->group(['prefix' => '/admin', 'middleware' => AuthMiddleware::class], function ($router) {

//     $router->get('/dashboard', ['vista' => 'admin/dashboard']);

// });

// --- EJECUTAR EL ENRUTADOR ---
$router->route();