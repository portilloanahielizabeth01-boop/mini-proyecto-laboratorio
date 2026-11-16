<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use InvalidArgumentException;

/**
 * Reglas de negocio para aceptar un pedido.
 * Antes estaban mezcladas dentro de procesarPedidoCompleto().
 */
final class OrderValidator
{
    public function validate(Order $order): void
    {
        if ($order->amount <= 0) {
            throw new InvalidArgumentException('Monto inválido: tiene que ser mayor a cero.');
        }
    }
}
