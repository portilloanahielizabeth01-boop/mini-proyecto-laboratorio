<?php

namespace App\Notifications;

class NotificationSender {
    
    /**
     * Método fábrica (Factory Method) para crear la notificación según el tipo requerido,
     * eliminando los IFs repetidos en múltiples archivos.
     */
    public static function make(string $type): NotificationInterface {
        return match (strtolower($type)) {
            'email' => new EmailNotification(),
            'sms'   => new SmsNotification(),
            default => throw new \InvalidArgumentException("Tipo de notificación no soportado: {$type}"),
        };
    }

    /**
     * Método de conveniencia para enviar directamente la notificación
     */
    public static function send(string $type, string $recipient, string $message): bool {
        $notification = self::make($type);
        return $notification->send($recipient, $message);
    }
}