<?php
/**
 * Contrato comun de todos los algoritmos de precio.
 *
 * Cada tipo de paciente es una clase que implementa esta interfaz.
 * PriceCalculator solo conoce este contrato, nunca las clases concretas.
 */
interface PricingStrategy
{
    public function calculate(float $amount): float;
}