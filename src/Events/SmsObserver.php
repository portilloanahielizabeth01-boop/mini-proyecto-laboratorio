<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Order;
use App\Notifications\NotificationInterface;

/**
 * Avisa por SMS. Se agregó SIN tocar OrderSubject (Ejercicio 5).
 */
final class SmsObserver implements OrderObserver
{
    public function __construct(
        private NotificationInterface $notification,
        private string $recipient = '3704000000'
    ) {
    }

    public function update(Order $order): void
    {
        $this->notification->send($this->recipient, "Pedido {$order->id} creado");
    }
}
