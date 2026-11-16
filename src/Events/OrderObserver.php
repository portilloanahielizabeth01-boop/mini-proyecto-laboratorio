<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Order;

/**
 * Contrato común de todos los interesados en "se creó un pedido"
 * (patrón OBSERVER). El sujeto conoce esta interfaz, no las clases concretas.
 */
interface OrderObserver
{
    public function update(Order $order): void;
}
