<?php

declare(strict_types=1);

namespace App\Notifications;

use InvalidArgumentException;

/**
 * ============================================================================
 *  ENVÍO DE NOTIFICACIONES
 *  Patrón aplicado: FACTORY (creacional)
 * ============================================================================
 *
 *  Antes el if por tipo de notificación estaba repetido en TRES archivos
 *  (acá, en OrderService y en OrderController).
 *
 *  Ahora la decisión de QUÉ objeto crear vive en un único método: make().
 *  El resto del sistema solo conoce NotificationInterface.
 *
 *  ⚠️ Consecuencia negativa: agregar WhatsApp obliga a tocar el match de
 *  make() (además de crear la clase nueva).
 * ============================================================================
 */
final class NotificationSender
{
    /**
     * Método fábrica: devuelve la notificación según el tipo pedido.
     */
    public static function make(string $type): NotificationInterface
    {
        return match (strtolower($type)) {
            'email' => new EmailNotification(),
            'sms'   => new SmsNotification(),
            default => throw new InvalidArgumentException("Tipo de notificación no soportado: {$type}"),
        };
    }

    /**
     * Método de conveniencia: crea la notificación y la envía.
     */
    public static function send(string $type, string $recipient, string $message): bool
    {
        return self::make($type)->send($recipient, $message);
    }
}
