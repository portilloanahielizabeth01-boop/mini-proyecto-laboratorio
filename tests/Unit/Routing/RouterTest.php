<?php

declare(strict_types=1);

namespace Tests\Unit\Routing;

use App\Routing\Router;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Funcionalidad F11 — Tabla de rutas (reemplaza los if de index.php).
 */
final class RouterTest extends TestCase
{
    #[Test]
    #[TestDox('CP-37 (+) Una acción registrada ejecuta su controlador')]
    public function accionRegistradaSeEjecuta(): void
    {
        $router = new Router();
        $router->add('listar', fn (): string => 'listado');

        $this->assertTrue($router->has('listar'));
        $this->assertSame('listado', $router->dispatch('listar'));
    }

    #[Test]
    #[TestDox('CP-38 (-) Una acción que no existe responde "Accion no encontrada"')]
    public function accionInexistenteNoSeEncuentra(): void
    {
        $router = new Router();

        $this->assertFalse($router->has('borrar'));
        $this->assertSame('Accion no encontrada', $router->dispatch('borrar'));
    }
}
