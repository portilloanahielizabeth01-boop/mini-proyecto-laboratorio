<?php

declare(strict_types=1);

namespace Tests\Unit\Legacy;

use App\Legacy\LegacyNotifier;
use App\Legacy\LegacyNotifierAdapter;
use App\Notifications\NotificationInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Funcionalidad F7 — Uso del sistema heredado (patrón Adapter).
 */
final class LegacyNotifierAdapterTest extends TestCase
{
    #[Test]
    #[TestDox('CP-25 (+) El adapter traduce send() al sendMessage() del sistema heredado')]
    public function adapterTraduceLaLlamada(): void
    {
        $notificacion = new LegacyNotifierAdapter(new LegacyNotifier());

        $this->expectOutputString('[LEGACY] recepcion: Pedido creado<br>');

        $this->assertTrue($notificacion->send('recepcion', 'Pedido creado'));
    }

    #[Test]
    #[TestDox('CP-26 (-) El sistema heredado solo no cumple nuestro contrato (por eso hace falta el adapter)')]
    public function elSistemaHeredadoNoCumpleNuestroContrato(): void
    {
        $this->assertNotInstanceOf(NotificationInterface::class, new LegacyNotifier());
        $this->assertInstanceOf(
            NotificationInterface::class,
            new LegacyNotifierAdapter(new LegacyNotifier())
        );
    }
}
