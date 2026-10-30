<?php

namespace App\Notifications;

class EmailNotification implements NotificationInterface {
    public function send(string $recipient, string $message): bool {
        // Lógica para enviar Email
        return true;
    }
}