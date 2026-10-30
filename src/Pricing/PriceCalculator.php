<?php
/**
 * ============================================================================
 *  CALCULO DE PRECIOS
 *  Patron aplicado: STRATEGY (comportamiento)
 * ============================================================================
 *
 *  PriceCalculator ya no conoce los tipos de paciente.
 *  Recibe una PricingStrategy por constructor (composicion) y le delega
 *  el calculo (polimorfismo).
 *
 *  Agregar un tipo de paciente nuevo = AGREGAR una clase que implemente
 *  PricingStrategy. Esta clase no se modifica (Abierto/Cerrado).
 *
 *  Uso:
 *    $calculadora = new PriceCalculator(new InsuranceStrategy());
 *    $calculadora->calculate(15000);         // 10500
 *    $calculadora->calculateWithVat(15000);  // 12705
 * ============================================================================
 */

final class PriceCalculator
{
    private const VAT_RATE = 1.21;

    // COMPOSICION: PriceCalculator TIENE una estrategia (no hereda de ella)
    public function __construct(private PricingStrategy $strategy)
    {
    }

    public function calculate(float $amount): float
    {
        return $this->strategy->calculate($amount);   // POLIMORFISMO
    }

    /**
     * Reutiliza calculate(): el descuento ya no se escribe dos veces.
     */
    public function calculateWithVat(float $amount): float
    {
        return $this->calculate($amount) * self::VAT_RATE;
    }
}