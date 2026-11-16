<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\OrderSubject;
use App\Models\Order;
use App\Pricing\PriceCalculator;
use App\Pricing\PricingStrategyResolver;
use App\Repositories\OrderRepository;

/**
 * ============================================================================
 *  SERVICIO DE PEDIDOS
 *  Patrón aplicado: FACADE (estructural) + SRP
 * ============================================================================
 *
 *  Antes: procesarPedidoCompleto() validaba, calculaba, guardaba, notificaba,
 *  generaba el reporte e imprimía HTML, todo en un solo método.
 *
 *  Ahora cada paso tiene su propia clase y esta fachada solo los COORDINA:
 *
 *     Controller -> OrderService -> Validator | Pricing | Repository | Events
 *
 *  Las dependencias llegan por constructor (no se crean con new adentro),
 *  así en las pruebas se pueden reemplazar.
 *
 *  ⚠️ Consecuencia negativa: la complejidad no desaparece, se reparte en más
 *  clases. Y si la fachada empieza a DECIDIR reglas de negocio, vuelve a ser
 *  la clase que hace todo.
 * ============================================================================
 */
final class OrderService
{
    public function __construct(
        private OrderValidator $validator,
        private PricingStrategyResolver $pricing,
        private OrderRepository $repository,
        private OrderSubject $events
    ) {
    }

    /**
     * Crea el pedido y devuelve el total a pagar.
     */
    public function createOrder(Order $order): float
    {
        $this->validator->validate($order);
        $calculator = new PriceCalculator($this->pricing->forPatientType($order->patientType));
        $total = $calculator->calculate($order->amount);
        $this->repository->save($order, $total);
        $this->events->notify($order);

        return $total;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listOrders(): array
    {
        return $this->repository->findAll();
    }
}
