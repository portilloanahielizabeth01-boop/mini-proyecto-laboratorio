<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Order;
use Error;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Funcionalidad F4 — Modelo de pedido (encapsulamiento y validación).
 */
final class OrderTest extends TestCase
{
    #[Test]
    #[TestDox('CP-16 (+) Un pedido válido conserva sus datos')]
    public function pedidoValidoConservaSusDatos(): void
    {
        $order = new Order(1, 'Juan Perez', 15000, 'obra_social');

        $this->assertSame(1, $order->id);
        $this->assertSame('Juan Perez', $order->patient);
        $this->assertSame(15000.0, $order->amount);
        $this->assertSame('obra_social', $order->patientType);
    }

    #[Test]
    #[TestDox('CP-17 (-) No se puede crear un pedido con monto negativo')]
    public function montoNegativoLanzaExcepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El monto no puede ser negativo.');

        new Order(1, 'Juan Perez', -50000, 'particular');
    }

    #[Test]
    #[TestDox('CP-18 (-) No se puede crear un pedido sin paciente')]
    public function pacienteVacioLanzaExcepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El paciente es obligatorio.');

        new Order(1, '   ', 15000, 'particular');
    }

    #[Test]
    #[TestDox('CP-19 (-) Un pedido creado no se puede modificar (readonly)')]
    public function noSePuedeModificarUnPedido(): void
    {
        $order = new Order(1, 'Juan Perez', 15000, 'particular');

        $this->expectException(Error::class);

        /** @phpstan-ignore-next-line a propósito: queremos ver que falla */
        $order->amount = -50000;
    }
}
