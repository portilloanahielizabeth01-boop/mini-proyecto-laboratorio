<?php

namespace Src\Services;

use Src\Models\Order;
// Si necesitás importar las otras clases de servicios, podés hacerlo según la estructura del proyecto

final class OrdersService // o OrderFacade según prefieras mantener el nombre de la clase del archivo
{
    public function __construct(
        private $validator,
        private $calculator,
        private $repository,
        private $events
    ) {}

    public function createOrder(Order $order): void
    {
        $this->validator->validate($order);
        $total = $this->calculator->calculate($order->amount);
        $this->repository->save($order);
        $this->events->notify($order);
    }
}