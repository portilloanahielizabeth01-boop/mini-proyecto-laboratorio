<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Order;
use App\Notifications\NotificationInterface;

/**
 * Avisa por email al paciente cuando se crea su pedido.
 */
final class EmailObserver implements OrderObserver
{
    public function __construct(
        private NotificationInterface $notification,
        private string $recipient = 'paciente@mail.com'
    ) {
    }

    public function update(Order $order): void
    {
        $this->notification->send($this->recipient, "Pedido {$order->id} creado");
    }
}
