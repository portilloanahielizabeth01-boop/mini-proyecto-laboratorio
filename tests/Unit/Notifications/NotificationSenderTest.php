<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use App\Notifications\EmailNotification;
use App\Notifications\NotificationSender;
use App\Notifications\SmsNotification;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Funcionalidad F2 — Creación de notificaciones (patrón Factory).
 */
final class NotificationSenderTest extends TestCase
{
    #[Test]
    #[TestDox('CP-08 (+) La fábrica crea el tipo de notificación pedido (sin importar mayúsculas)')]
    public function fabricaCreaElTipoPedido(): void
    {
        $this->assertInstanceOf(EmailNotification::class, NotificationSender::make('email'));
        $this->assertInstanceOf(SmsNotification::class, NotificationSender::make('SMS'));
    }

    #[Test]
    #[TestDox('CP-09 (+) Enviar por email devuelve true y muestra el aviso')]
    public function enviarPorEmailDevuelveTrue(): void
    {
        $this->expectOutputString('[EMAIL] para juan@mail.com: Pedido 7 creado<br>');

        $this->assertTrue(NotificationSender::send('email', 'juan@mail.com', 'Pedido 7 creado'));
    }

    #[Test]
    #[TestDox('CP-10 (-) Un tipo de notificación no soportado es rechazado')]
    public function tipoNoSoportadoLanzaExcepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tipo de notificación no soportado: whatsapp');

        NotificationSender::make('whatsapp');
    }
}
