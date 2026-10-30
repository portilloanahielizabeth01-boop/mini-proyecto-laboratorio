<?php
/**
 * ============================================================================
 *  laboratorio-pedidos — PUNTO DE ENTRADA
 *  Metodología de Sistemas II — UTN FRRe (sede Formosa)
 * ============================================================================
 *
 *  ❌ DEUDA SEMBRADA EN ESTE ARCHIVO
 *     1. Carga manual de dependencias con require (se rompe al agregar clases).
 *     2. Credenciales de base de datos escritas en el código y versionadas.
 *     3. Ruteo resuelto con una cadena de if que crece con cada pantalla.
 *
 *  ✅ FORMA CORRECTA
 *     1. spl_autoload_register() o Composer con PSR-4.
 *     2. Configuración en /config/database.php, fuera del repositorio (.gitignore).
 *     3. Un mapa de rutas (array ruta => callable).
 *
 *  Unidad 1: los puntos 2 y 3 también son deuda de PROCESO, no solo de diseño.
 * ============================================================================
 */

// Autoload: PHP busca la clase en src/<Carpeta>/<Clase>.php la primera vez que
// se usa. Agregar una clase nueva ya no obliga a editar este archivo.
spl_autoload_register(function (string $clase): void {
    foreach (glob(__DIR__ . '/../src/*/' . $clase . '.php') as $archivo) {
        require_once $archivo;
        return;
    }
});

// ❌ MAL APLICADO: credenciales en el código fuente y dentro del repositorio.
//    Si el repo es público, la credencial es pública.
//    Además, cada integrante edita esta línea => conflicto en cada merge.
define('DB_HOST', 'localhost');
define('DB_NAME', 'laboratorio');
define('DB_USER', 'root');
define('DB_PASS', '');

/*
 * ✅ FORMA CORRECTA:
 *   $config = require __DIR__ . '/../config/database.php';   // ignorado por git
 *   El repositorio versiona config/database.example.php con valores vacíos.
 */

$accion = $_GET['accion'] ?? 'crear';

// Tabla de rutas: cada accion apunta a su metodo del controlador.
// Agregar una pantalla = agregar una linea aca, sin tocar ningun if.
$controller = new OrderController();

$rutas = [
    'crear'   => fn() => $controller->create(),
    'listar'  => fn() => $controller->index(),
    'reporte' => fn() => $controller->report(),
];

$ruta = $rutas[$accion] ?? fn() => print 'Accion no encontrada';
$ruta();