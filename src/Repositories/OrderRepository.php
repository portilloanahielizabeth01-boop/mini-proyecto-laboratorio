<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Connection;
use App\Models\Order;

/**
 * ============================================================================
 *  REPOSITORIO DE PEDIDOS
 *  Patrón aplicado: REPOSITORY
 * ============================================================================
 *
 *  Antes el modelo Order tenía un método guardar() que armaba SQL concatenando
 *  texto (riesgo de inyección SQL) y mezclaba dominio con persistencia.
 *
 *  Ahora toda la persistencia vive acá. Si mañana se cambia el motor de base
 *  de datos, cambia esta clase y el modelo queda igual (SRP).
 * ============================================================================
 */
final class OrderRepository
{
    private const TABLE = 'orders';

    public function __construct(private Connection $connection)
    {
    }

    public function save(Order $order, float $total): void
    {
        $this->connection->insert(self::TABLE, [
            'id'       => $order->id,
            'paciente' => $order->patient,
            'monto'    => $order->amount,
            'tipo'     => $order->patientType,
            'total'    => $total,
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findAll(): array
    {
        return $this->connection->all(self::TABLE);
    }
}
