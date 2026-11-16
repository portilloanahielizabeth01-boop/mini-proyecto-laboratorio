<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * ============================================================================
 *  MODELO Order
 * ============================================================================
 *
 *  - Propiedades readonly: una vez creado, el pedido no se puede modificar.
 *  - Se valida en el constructor: el objeto no puede nacer inválido.
 *  - Ya NO se guarda a sí mismo (eso es de OrderRepository) ni calcula
 *    precios (eso es de las PricingStrategy).
 * ============================================================================
 */
final class Order
{
    public function __construct(
        public readonly int $id,
        public readonly string $patient,
        public readonly float $amount,
        public readonly string $patientType
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('El monto no puede ser negativo.');
        }

        if (trim($patient) === '') {
            throw new InvalidArgumentException('El paciente es obligatorio.');
        }
    }
}
