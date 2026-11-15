<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Order;

/**
 * ============================================================================
 *  AVISOS AL CREARSE UN PEDIDO
 *  Patrón aplicado: OBSERVER (comportamiento)
 * ============================================================================
 *
 *  Antes, quien creaba el pedido llamaba a mano a Email, SMS y Dashboard.
 *  Ahora el sujeto guarda una colección de observadores (interfaces) y les
 *  avisa a todos. Sumar un aviso nuevo = suscribir una clase nueva.
 *
 *  ⚠️ Consecuencia negativa: leyendo el código del pedido ya no se ve quién
 *  se entera del evento (perdimos trazabilidad). Lo compensamos con pruebas
 *  que verifican a quién se notifica.
 * ============================================================================
 */
final class OrderSubject
{
    /** @var list<OrderObserver> colección de INTERFACES, no de clases concretas */
    private array $observers = [];

    public function subscribe(OrderObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    public function notify(Order $order): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($order);   // POLIMORFISMO
        }
    }

    public function count(): int
    {
        return count($this->observers);
    }
}
