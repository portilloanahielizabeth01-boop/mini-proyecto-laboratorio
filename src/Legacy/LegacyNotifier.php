<?php

declare(strict_types=1);

namespace App\Legacy;

/**
 * Sistema de avisos heredado de otro proveedor.
 * NO se modifica: lo usan también Recepción y Facturación.
 * (Se borraron el send() que habíamos agregado adentro y la copia pegada.)
 */
class LegacyNotifier
{
    /** Método original del proveedor. */
    public function sendMessage(string $text): void
    {
        echo "[LEGACY] {$text}<br>";
    }
}
