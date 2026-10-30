<?php

namespace App\Notifications;

interface NotificationInterface {
    public function send(string $recipient, string $message): bool;
}