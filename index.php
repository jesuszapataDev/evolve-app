<?php

// --- AUTOLOADER BÁSICO ---
// Carga las clases necesarias (Router.php, controladores, etc.)
spl_autoload_register(function ($className) {
    if (file_exists($className . '.php')) {
        require_once $className . '.php';
    }
});


// --- CLASES DE EJEMPLO (En un proyecto real, estarían en sus propios archivos) ---

class ViewRenderer
{
    public function render($vista, $data = [])
    {
        // Extrae los datos para que estén disponibles como variables en la vista
        extract($data);
        echo "<h1>Renderizando la vista: {$vista}</h1>";
        if (!empty($data)) {
            echo "<pre>Datos: " . print_r($data, true) . "</pre>";
        }
    }
}

class HomeController
{
    public function index()
    {
        echo "Estás en la página de inicio (desde HomeController).";
    }
    public function contact()
    {
        echo "Página de contacto.";
    }
}

class UserController
{
    public function show($params)
    {
        echo "Mostrando perfil del usuario con ID: " . htmlspecialchars($params['id']);
    }
    public function store()
    {
        echo "Procesando datos del formulario de registro de usuario...<br>";
        echo "Datos recibidos por POST: <pre>" . print_r($_POST, true) . "</pre>";
    }
}

class AuthMiddleware
{
    private $rolesPermitidos;
    public function __construct($roles = [])
    {
        $this->rolesPermitidos = $roles;
    }
    public function handle()
    {
        // Lógica de autenticación de ejemplo
        $usuarioAutenticado = true; // Simula un usuario logueado
        $rolUsuario = 'admin'; // Simula el rol del usuario

        if (!$usuarioAutenticado) {
            header('HTTP/1.1 401 Unauthorized');
            echo "Error 401: No estás autorizado para ver esta página.";
            exit();
        }

        if (!empty($this->rolesPermitidos) && !in_array($rolUsuario, $this->rolesPermitidos)) {
            header('HTTP/1.1 403 Forbidden');
            echo "Error 403: No tienes los permisos necesarios (Rol requerido: " . implode(', ', $this->rolesPermitidos) . ").";
            exit();
        }
        // Si todo está bien, la ejecución continúa
    }
}


// --- INICIALIZACIÓN Y DEFINICIÓN DE RUTAS ---

$viewRenderer = new ViewRenderer();
$router = new Router($viewRenderer);

// Ruta simple que renderiza una vista
$router->get('/', ['controlador' => 'HomeController', 'accion' => 'index']);

// Ruta a otra acción de un controlador
$router->get('/contacto', ['controlador' => 'HomeController', 'accion' => 'contact']);

// Ruta con un parámetro dinámico
$router->get('/usuario/{id}', ['controlador' => 'UserController', 'accion' => 'show']);

// Ruta para procesar un formulario por POST
$router->post('/usuario/crear', ['controlador' => 'UserController', 'accion' => 'store']);

// Grupo de rutas para un panel de administración
$router->group(['prefix' => '/admin', 'middleware' => 'AuthMiddleware'], function ($router) {

    // Ruta: /admin/dashboard
    $router->get('/dashboard', ['vista' => 'admin/dashboard']);

    // Ruta: /admin/usuarios (requiere rol específico)
    $router->get('/usuarios', [
        'vista' => 'admin/lista_usuarios',
        'roles' => ['admin'] // Solo usuarios con rol 'admin' pueden acceder
    ]);
});


// --- EJECUTAR EL ENRUTADOR ---
$router->route();
