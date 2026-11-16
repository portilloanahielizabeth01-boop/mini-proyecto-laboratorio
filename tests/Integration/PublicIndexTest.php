<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Prueba de humo (smoke test) de punta a punta: ejecuta public/index.php
 * en un proceso PHP aparte, como lo haría Apache, y revisa la respuesta.
 */
final class PublicIndexTest extends TestCase
{
    /**
     * @param array<string, string> $get
     */
    private function request(array $get): string
    {
        $script = sprintf(
            '$_GET = %s; require %s;',
            var_export($get, true),
            var_export(dirname(__DIR__, 2) . '/public/index.php', true)
        );

        return (string) shell_exec(PHP_BINARY . ' -r ' . escapeshellarg($script) . ' 2>&1');
    }

    #[Test]
    #[TestDox('CP-39 (+) accion=listar muestra los dos pedidos de ejemplo con su total')]
    public function listarMuestraLosPedidosDeEjemplo(): void
    {
        $html = $this->request(['accion' => 'listar']);

        $this->assertStringContainsString('<td>Juan Perez</td>', $html);
        $this->assertStringContainsString('<td>10500</td>', $html);
        $this->assertStringContainsString('<td>Ana Gomez</td>', $html);
        $this->assertStringContainsString('<td>22000</td>', $html);
    }

    #[Test]
    #[TestDox('CP-40 (-) accion=crear con monto 0 muestra un error y no crea el pedido')]
    public function crearConMontoCeroMuestraError(): void
    {
        $html = $this->request(['accion' => 'crear', 'monto' => '0']);

        $this->assertStringContainsString('Error: Monto inválido', $html);
        $this->assertStringNotContainsString('Pedido creado', $html);
    }
}
