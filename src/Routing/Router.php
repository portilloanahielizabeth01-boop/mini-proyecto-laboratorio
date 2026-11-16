<?php

declare(strict_types=1);

namespace App\Routing;

/**
 * Tabla de rutas: reemplaza la cadena de if/elseif de public/index.php.
 * Agregar una pantalla = agregar una línea con add(), sin tocar dispatch().
 */
final class Router
{
    /** @var array<string, callable(): string> */
    private array $routes = [];

    /**
     * @param callable(): string $handler
     */
    public function add(string $accion, callable $handler): void
    {
        $this->routes[$accion] = $handler;
    }

    public function has(string $accion): bool
    {
        return isset($this->routes[$accion]);
    }

    public function dispatch(string $accion): string
    {
        if (!$this->has($accion)) {
            return 'Accion no encontrada';
        }

        return ($this->routes[$accion])();
    }
}
