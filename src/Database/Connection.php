<?php
/**
 * ============================================================================
 *  CONEXION A LA BASE DE DATOS
 *  Patron aplicado: SINGLETON (creacional)
 * ============================================================================
 *
 *  1. Hay UNA sola conexion por request: constructor privado + instancia
 *     estatica + getInstance().
 *  2. La configuracion se inyecta una vez desde public/index.php; ya no se
 *     leen constantes globales.
 *
 *  ⚠️ Singleton sirve para conexion, logger y configuracion. NO para guardar
 *     el pedido actual ni el usuario logueado: eso seria una variable global
 *     disfrazada.
 *
 *  La persistencia sigue simulada en memoria (no es parte de la deuda).
 * ============================================================================
 */

final class Connection
{
    private static ?Connection $instance = null;

    /** Almacen en memoria para poder correr la demo sin MySQL levantado. */
    private array $consultasEjecutadas = [];

    // Privado: nadie puede hacer new Connection() desde afuera y romper
    // la garantia de instancia unica.
    private function __construct(private array $config)
    {
    }

    /**
     * La primera llamada (en index.php) recibe la configuracion y crea la
     * conexion. Las siguientes devuelven siempre la misma instancia.
     */
    public static function getInstance(?array $config = null): Connection
    {
        if (self::$instance === null) {
            if ($config === null) {
                throw new RuntimeException(
                    'La conexion no fue inicializada: llamar primero a Connection::getInstance($config).'
                );
            }
            self::$instance = new Connection($config);
        }

        return self::$instance;
    }

    // Tampoco se puede clonar: seria una segunda instancia.
    private function __clone()
    {
    }

    public function ejecutar(string $sql): void
    {
        try {
            $this->consultasEjecutadas[] = $sql;
        } catch (Throwable $e) {
            // ❌ MAL APLICADO: la excepcion se traga y el sistema sigue.
            //    El pedido "se guardo" aunque no se guardo nada.
            //    ✅ Loguear y relanzar: throw new RuntimeException('...', 0, $e);
        }
    }

    public function consultas(): array
    {
        return $this->consultasEjecutadas;
    }
}