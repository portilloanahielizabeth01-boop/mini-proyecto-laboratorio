<?php

namespace App\Notifications;

class SmsNotification implements NotificationInterface {
    public function send(string $recipient, string $message): bool {
        // Lógica para enviar SMS
        return true;
    }
}