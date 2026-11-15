<?php

declare(strict_types=1);

namespace App\Notifications;

final class EmailNotification implements NotificationInterface
{
    public function send(string $recipient, string $message): bool
    {
        // Envío simulado: en el sistema real acá iría el cliente de correo.
        echo "[EMAIL] para {$recipient}: {$message}<br>";

        return true;
    }
}
