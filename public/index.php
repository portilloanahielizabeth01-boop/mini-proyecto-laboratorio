<?php

/**
 * Punto de entrada único de la aplicación (Front Controller)
 * Refactorizado aplicando Autoload, Configuración Externa y Tabla de Rutas.
 */

// 1. Cargar el autoloader de Composer (Autoloading automático)
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Cargar configuración externa (credenciales y parámetros)
$config = require_once __DIR__ . '/../config/config.php';

// 3. Obtener la ruta solicitada desde la URL (ej. ?route=home o manejo básico de URI)
$route = $_GET['route'] ?? 'home';

// 4. Definición de la Tabla de Rutas (Mapeo limpio de URLs a Controladores)
$routes = [
    'home'       => [App\Controllers\OrderController::class, 'index'],
    'orders'     => [App\Controllers\OrderController::class, 'list'],
    'orders/create' => [App\Controllers\OrderController::class, 'create'],
    'report'     => [App\Controllers\ReportController::class, 'generate'],
];

// 5. Despachador de rutas (Dispatcher) en lugar de IFs encadenados
if (array_key_exists($route, $routes)) {
    [$controllerClass, $action] = $routes[$route];
    
    if (class_exists($controllerClass) && method_exists($controllerClass, $action)) {
        $controller = new $controllerClass();
        $controller->$action();
    } else {
        http_response_code(500);
        echo "Error 500: El controlador o la acción configurados no existen.";
    }
} else {
    http_response_code(404);
    echo "Error 404: Página no encontrada.";
}