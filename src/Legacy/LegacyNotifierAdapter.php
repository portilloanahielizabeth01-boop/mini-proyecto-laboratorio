<?php

declare(strict_types=1);

namespace App\Legacy;

use App\Notifications\NotificationInterface;

/**
 * ============================================================================
 *  Patrón aplicado: ADAPTER (estructural)
 * ============================================================================
 *
 *  Nuestro sistema habla con NotificationInterface::send().
 *  El sistema heredado solo entiende sendMessage().
 *  El adapter implementa NUESTRO contrato y traduce la llamada, sin tocar
 *  la clase del proveedor. La incompatibilidad queda encerrada acá.
 *
 *  Si mañana el proveedor cambia sendMessage() por dispatch(), cambia
 *  un solo archivo: este.
 * ============================================================================
 */
final class LegacyNotifierAdapter implements NotificationInterface
{
    // COMPOSICIÓN: el adapter TIENE el sistema viejo adentro
    public function __construct(private LegacyNotifier $legacy)
    {
    }

    public function send(string $recipient, string $message): bool
    {
        $this->legacy->sendMessage("{$recipient}: {$message}");

        return true;
    }
}
