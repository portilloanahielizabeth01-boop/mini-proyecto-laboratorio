<?php

declare(strict_types=1);

namespace App\Pricing;

use InvalidArgumentException;

/**
 * Traduce el tipo de paciente que llega en el pedido ('obra_social',
 * 'particular', ...) a la estrategia de precio que corresponde.
 *
 * Es el ÚNICO lugar del sistema que conoce esa relación: antes estaba
 * repetida como if/switch en el modelo, el servicio, el controlador y la vista.
 */
final class PricingStrategyResolver
{
    /** @var array<string, callable(): PricingStrategy> */
    private array $map;

    public function __construct()
    {
        $this->map = [
            'particular'  => fn (): PricingStrategy => new PrivatePatientStrategy(),
            'obra_social' => fn (): PricingStrategy => new InsuranceStrategy(),
            'jubilado'    => fn (): PricingStrategy => new RetireeStrategy(),
            'prepaga'     => fn (): PricingStrategy => new PrepaidStrategy(),
        ];
    }

    public function forPatientType(string $patientType): PricingStrategy
    {
        if (!isset($this->map[$patientType])) {
            throw new InvalidArgumentException("Tipo de paciente no soportado: {$patientType}");
        }

        return ($this->map[$patientType])();
    }
}
