<?php

class Order
{
    public readonly int $id;
    public readonly string $patient;
    public readonly float $amount;
    public readonly string $patientType;

    public function __construct(int $id, string $patient, float $amount, string $patientType)
    {
        // Validaciones básicas opcionales para evitar que nazca inválido
        if ($amount < 0) {
            throw new InvalidArgumentException("El monto no puede ser negativo.");
        }

        $this->id = $id;
        $this->patient = $patient;
        $this->amount = $amount;
        $this->patientType = $patientType;
    }
}