<?php
/**
 * ============================================================================
 *  SISTEMA HEREDADO DEL LABORATORIO
 *  Patrón aplicado: ADAPTER (estructural)
 * ============================================================================
 */

/**
 * 1. Nuestro contrato (interfaz que espera nuestro sistema).
 */
interface Notification
{
    public function send(string $message): void;
}

/**
 * 2. Sistema heredado del proveedor (inalterable, sin modificaciones nuestras).
 */
class LegacyNotifier
{
    /** Método original del proveedor. */
    public function sendMessage(string $text): void
    {
        echo "[LEGACY] {$text}<br>";
    }
}

/**
 * 3. ✅ EL ADAPTER (La forma correcta)
 * Encierra la incompatibilidad en un único archivo usando composición.
 */
final class LegacyNotifierAdapter implements Notification
{
    // Composición: el adapter TIENE el sistema viejo adentro
    public function __construct(private LegacyNotifier $legacy) {}

    public function send(string $message): void
    {
        // Única traducción del sistema: adaptamos nuestro contrato al método viejo
        $this->legacy->sendMessage($message);
    }
}

/*
 * ============================================================================
 *  EJEMPLO DE USO
 * ============================================================================
 */

// Inyectamos el sistema heredado a través del adapter cumpliendo nuestro contrato
/** @var Notification $notificacion */
$notificacion = new LegacyNotifierAdapter(new LegacyNotifier());
$notificacion->send('Pedido creado correctamente a través del Adapter');
