<?php

declare(strict_types=1);

namespace App\Notifications;

final class SmsNotification implements NotificationInterface
{
    public function send(string $recipient, string $message): bool
    {
        // Envío simulado: en el sistema real acá iría el proveedor de SMS.
        echo "[SMS] para {$recipient}: {$message}<br>";

        return true;
    }
}
