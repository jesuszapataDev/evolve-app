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
    $_SESSION['lang'] = strtoupper($_GET['lang']);
}

$lang = $_SESSION['lang'] ?? 'EN';
$traducciones = Language::loadLanguage($lang);




// PASO 2: IMPORTAR LAS CLASES QUE VAS A UTILIZAR
// Esto hace que el código sea más legible.
use App\Router;
use App\Core\ViewRenderer;
use App\Controllers\CountryController; // Asegúrate de que la ruta sea correcta
use App\Controllers\AuthController;
use App\Controllers\SessionConfigController;
use App\Controllers\AuditLogController;
use App\Controllers\RecoveryPasswordController;
use App\Controllers\SessionManagementController;
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
    $router->post('/register', [
        'controlador' => AuthController::class,
        'accion' => 'registrar'
    ]);

    $router->get('/logout', [
        'controlador' => AuthController::class,
        'accion' => 'logout',
    ]);

    // RECUPERAR CONTRASEÑA

    $router->post('/password-recovery/verify-email', [
        'controlador' => RecoveryPasswordController::class,
        'accion' => 'verifyEmail'
    ]);
    $router->post('/password-recovery/verify-answers', [
        'controlador' => RecoveryPasswordController::class,
        'accion' => 'verifySecurityAnswers'
    ]);
    $router->post('/password-recovery/update-password', [
        'controlador' => RecoveryPasswordController::class,
        'accion' => 'updatePassword'
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

    // USERS
    $router->get('/users', [
        'controlador' => UserController::class,
        'accion' => 'showAll'
    ]);
    $router->get('/users/{id}', [
        'controlador' => UserController::class,
        'accion' => 'showById'
    ]);
    $router->post('/users', [
        'controlador' => UserController::class,
        'accion' => 'create'
    ]);
    $router->put('/users/{id}', [
        'controlador' => UserController::class,
        'accion' => 'update'
    ]);
    $router->delete('/users/{id}', [
        'controlador' => UserController::class,
        'accion' => 'delete'
    ]);
});

// API PROTEGIDAS POR MIDDLEWARE
$router->group(['prefix' => '/api', 'middleware' => AuthMiddleware::class], function ($router) {

    // GESTIÓN DE SESIONES

    $router->get('/session-audit', [
        'controlador' => SessionManagementController::class,
        'accion' => 'showAll'
    ]);
    $router->post('/session-status', [
        'controlador' => SessionManagementController::class,
        'accion' => 'checkStatus'
    ]);
    $router->get('/session-config', [
        'controlador' => SessionConfigController::class,
        'accion' => 'show'
    ]);

    $router->post('/session-config', [
        'controlador' => SessionConfigController::class,
        'accion' => 'update'
    ]);

    $router->post('/session-audit/kick/1', [
        'controlador' => SessionManagementController::class,
        'accion' => 'kick'
    ]);

    $router->get('/session-audit/export/1', [
        'controlador' => SessionManagementController::class,
        'accion' => 'export'
    ]);

    // AUDIT LOG 
    $router->get('/auditlog', [
        'controlador' => AuditLogController::class,
        'accion' => 'getAll'
    ]);
    $router->get('/auditlog/{id}', [
        'controlador' => AuditLogController::class,
        'accion' => 'getById'
    ]);
    $router->get('/auditlog/export/{id}', [
        'controlador' => AuditLogController::class,
        'accion' => 'exportCSV'
    ]);





});

// GRUPO DE RUTAS PARA VISTAS PROTEGIDAS POR MIDDLEWARE
$router->group(['middleware' => AuthMiddleware::class], function ($router) use ($traducciones) {



    $router->get('/home', [
        'vista' => '/modules/home',
        'vistaData' => ['title' => $traducciones['home_title'] ?? 'home']
    ]);
    // USERS
    $router->get('/users', [
        'vista' => '/modules/users_view',
        'vistaData' => ['title' => $traducciones['users_title'] ?? 'Users']
    ]);
    $router->get('/session_management', [
        'vista' => '/modules/session_management',
        'vistaData' => ['title' => $traducciones['home_title'] ?? 'home']
    ]);
    $router->get('/audit_log', [
        'vista' => '/modules/audit_log',
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
    $router->get('/recovery_password', [
        'vista' => '/auth/recovery_password',
        'vistaData' => ['title' => $traducciones['login_title'] ?? 'Login', 'layout' => false]
    ]);
});



// Grupo de rutas para el panel de administración
// $router->group(['prefix' => '/admin', 'middleware' => AuthMiddleware::class], function ($router) {

//     $router->get('/dashboard', ['vista' => 'admin/dashboard']);

// });

// --- EJECUTAR EL ENRUTADOR ---
$router->route();