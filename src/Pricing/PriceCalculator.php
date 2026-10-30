<?php

namespace App\Pricing;

class PriceCalculator {
    private PricingStrategy $strategy;

    public function __construct(PricingStrategy $strategy) {
        $this->strategy = $strategy;
    }

    public function setStrategy(PricingStrategy $strategy): void {
        $this->strategy = $strategy;
    }

    public function calculate(float $amount): float {
        return $this->strategy->calculate($amount);
    }
}