<?php

declare(strict_types=1);

namespace App\Database;

use InvalidArgumentException;
use LogicException;

/**
 * ============================================================================
 *  CONEXIÓN A LA BASE DE DATOS
 *  Patrón aplicado: SINGLETON (creacional)
 * ============================================================================
 *
 *  Antes: obtener() creaba una conexión NUEVA en cada llamada y los errores
 *  se tragaban en silencio.
 *
 *  Ahora:
 *    1. Constructor privado: nadie puede hacer new Connection().
 *    2. getInstance() crea la conexión la primera vez y después devuelve
 *       SIEMPRE la misma instancia.
 *    3. __clone() y __wakeup() bloqueados: no se puede duplicar la instancia.
 *    4. Si algo falla, se lanza la excepción (fallar rápido y fuerte).
 *
 *  La persistencia sigue simulada en memoria para que el proyecto corra en
 *  XAMPP sin MySQL (la consigna aclara que eso NO es deuda a corregir).
 *  La configuración (host, base, usuario) llega desde config/, no desde
 *  constantes escritas en el código.
 *
 *  ⚠️ Consecuencia negativa: la instancia única se comporta como un estado
 *  global. En las pruebas hay que vaciarla con limpiar() antes de cada test,
 *  porque los datos de un test quedan vivos para el siguiente.
 * ============================================================================
 */
final class Connection
{
    private static ?Connection $instance = null;

    /** @var array<string, list<array<string, mixed>>> tablas simuladas en memoria */
    private array $tables = [];

    /**
     * @param array<string, string> $config
     */
    private function __construct(private array $config)
    {
    }

    /**
     * @param array<string, string> $config solo se usa la primera vez
     */
    public static function getInstance(array $config = []): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function insert(string $table, array $row): void
    {
        if (trim($table) === '') {
            throw new InvalidArgumentException('La tabla es obligatoria.');
        }

        if ($row === []) {
            throw new InvalidArgumentException('No se puede insertar una fila vacía.');
        }

        $this->tables[$table][] = $row;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function all(string $table): array
    {
        return $this->tables[$table] ?? [];
    }

    /**
     * @return array<string, string>
     */
    public function config(): array
    {
        return $this->config;
    }

    /** Vacía las tablas en memoria (lo usan las pruebas). */
    public function limpiar(): void
    {
        $this->tables = [];
    }

    private function __clone()
    {
    }

    public function __wakeup(): void
    {
        throw new LogicException('No se puede deserializar una instancia de Singleton.');
    }
}
