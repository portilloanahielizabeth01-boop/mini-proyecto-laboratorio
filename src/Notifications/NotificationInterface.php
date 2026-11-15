<?php

declare(strict_types=1);

namespace App\Notifications;

/**
 * Contrato común de todas las notificaciones.
 * Mismo método send() para todas => se pueden intercambiar (polimorfismo).
 */
interface NotificationInterface
{
    public function send(string $recipient, string $message): bool;
}
