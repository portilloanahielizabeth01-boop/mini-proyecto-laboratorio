<?php
/**
 * Paciente con obra social: 30% de descuento.
 *
 * El porcentaje vive en UN solo lugar (el valor por defecto del constructor).
 * Si el laboratorio lo cambia al 25%, se cambia aca o se pasa por parametro:
 *   new InsuranceStrategy(0.25)
 */
final class InsuranceStrategy implements PricingStrategy
{
    public function __construct(private float $discount = 0.30)
    {
    }

    public function calculate(float $amount): float
    {
        return $amount * (1 - $this->discount);
    }
}