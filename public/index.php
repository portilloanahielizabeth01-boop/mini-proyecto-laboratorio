<?php

/**
 * ============================================================================
 *  laboratorio-pedidos — PUNTO DE ENTRADA (Front Controller)
 *  Refactorizado con: Autoload + configuración externa + tabla de rutas.
 * ============================================================================
 */

declare(strict_types=1);

// 1. Autoload: si se corrió `composer install` se usa el de Composer (PSR-4).
//    Si no (XAMPP sin Composer), un autoloader mínimo con la misma regla:
//    App\Algo\Clase  =>  src/Algo/Clase.php
$composer = __DIR__ . '/../vendor/autoload.php';
if (is_file($composer)) {
    require_once $composer;
} else {
    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\';
        if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
            return;
        }
        $file = __DIR__ . '/../src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    });
}

// 2. Configuración externa: config/database.php NO se versiona (.gitignore).
//    Si no existe, se usa la plantilla config/database.example.php.
$configFile = __DIR__ . '/../config/database.php';
$config = require (is_file($configFile) ? $configFile : __DIR__ . '/../config/database.example.php');

// 3. Tabla de rutas (ver src/Bootstrap.php): reemplaza los if encadenados.
$router = App\Bootstrap::router($config, __DIR__ . '/../views');

$accion = (string) ($_GET['accion'] ?? 'crear');
if (!$router->has($accion)) {
    http_response_code(404);
}

try {
    echo $router->dispatch($accion);
} catch (InvalidArgumentException $error) {
    // Datos inválidos (monto <= 0, tipo de paciente desconocido...): se informa, no se oculta.
    http_response_code(400);
    echo 'Error: ' . htmlspecialchars($error->getMessage(), ENT_QUOTES, 'UTF-8');
}
