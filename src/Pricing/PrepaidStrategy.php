<?php

declare(strict_types=1);

namespace App\Pricing;

/**
 * Paciente con prepaga: 20% de descuento.
 *
 * Se agregó SIN modificar PriceCalculator (principio Abierto/Cerrado).
 */
final class PrepaidStrategy implements PricingStrategy
{
    public function __construct(private float $discount = 0.20)
    {
    }

    public function calculate(float $amount): float
    {
        return $amount * (1 - $this->discount);
    }
}
