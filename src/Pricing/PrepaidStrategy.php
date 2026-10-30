<?php
/**
 * Paciente con prepaga: 20% de descuento.
 *
 * EJERCICIO 1: este tipo de paciente se agrego SIN modificar PriceCalculator.
 * Solo se creo esta clase (y su require en public/index.php).
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