<?php

declare(strict_types=1);

namespace Tests\Unit\Pricing;

use App\Pricing\InsuranceStrategy;
use App\Pricing\PrepaidStrategy;
use App\Pricing\PriceCalculator;
use App\Pricing\PricingStrategyResolver;
use App\Pricing\PrivatePatientStrategy;
use App\Pricing\RetireeStrategy;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Funcionalidad F1 — Cálculo de precio (patrón Strategy).
 * Casos documentados en docs/casos-de-prueba.md.
 */
final class PricingStrategyTest extends TestCase
{
    #[Test]
    #[TestDox('CP-01 (+) Paciente particular paga el monto completo')]
    public function particularPagaElMontoCompleto(): void
    {
        $calculadora = new PriceCalculator(new PrivatePatientStrategy());

        $this->assertSame(15000.0, $calculadora->calculate(15000));
    }

    #[Test]
    #[TestDox('CP-02 (+) Paciente con obra social paga el 70% (descuento del 30%)')]
    public function obraSocialTieneTreintaPorCientoDeDescuento(): void
    {
        $calculadora = new PriceCalculator(new InsuranceStrategy());

        $this->assertEqualsWithDelta(10500.0, $calculadora->calculate(15000), 0.001);
    }

    #[Test]
    #[TestDox('CP-03 (+) Paciente jubilado paga el 50%')]
    public function jubiladoTieneCincuentaPorCientoDeDescuento(): void
    {
        $calculadora = new PriceCalculator(new RetireeStrategy());

        $this->assertEqualsWithDelta(7500.0, $calculadora->calculate(15000), 0.001);
    }

    #[Test]
    #[TestDox('CP-04 (+) Prepaga (agregada sin modificar PriceCalculator) paga el 80%')]
    public function prepagaTieneVeintePorCientoDeDescuento(): void
    {
        $calculadora = new PriceCalculator(new PrepaidStrategy());

        $this->assertEqualsWithDelta(12000.0, $calculadora->calculate(15000), 0.001);
    }

    #[Test]
    #[TestDox('CP-05 (+) El precio con IVA reutiliza la estrategia (el descuento no se duplica)')]
    public function precioConIvaReutilizaLaEstrategia(): void
    {
        $calculadora = new PriceCalculator(new InsuranceStrategy());

        $this->assertEqualsWithDelta(12705.0, $calculadora->calculateWithVat(15000), 0.001);
    }

    #[Test]
    #[TestDox('CP-06 (+) El resolver devuelve la estrategia que corresponde a cada tipo')]
    public function resolverDevuelveLaEstrategiaCorrecta(): void
    {
        $resolver = new PricingStrategyResolver();

        $this->assertInstanceOf(PrivatePatientStrategy::class, $resolver->forPatientType('particular'));
        $this->assertInstanceOf(InsuranceStrategy::class, $resolver->forPatientType('obra_social'));
        $this->assertInstanceOf(RetireeStrategy::class, $resolver->forPatientType('jubilado'));
        $this->assertInstanceOf(PrepaidStrategy::class, $resolver->forPatientType('prepaga'));
    }

    #[Test]
    #[TestDox('CP-07 (-) Un tipo de paciente desconocido es rechazado')]
    public function tipoDePacienteDesconocidoLanzaExcepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tipo de paciente no soportado: vip');

        (new PricingStrategyResolver())->forPatientType('vip');
    }
}
