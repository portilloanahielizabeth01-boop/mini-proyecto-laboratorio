<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Order;

/**
 * Actualiza el tablero interno del laboratorio (simulado).
 */
final class DashboardObserver implements OrderObserver
{
    public function update(Order $order): void
    {
        echo "[DASHBOARD] actualizado para el pedido {$order->id}<br>";
    }
}
