<?php

declare(strict_types=1);

namespace Tests\Unit\Database;

use App\Database\Connection;
use Error;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Funcionalidad F3 — Conexión única a la base (patrón Singleton).
 */
final class ConnectionTest extends TestCase
{
    protected function setUp(): void
    {
        // El Singleton es estado global: hay que vaciarlo antes de cada prueba.
        Connection::getInstance()->limpiar();
    }

    #[Test]
    #[TestDox('CP-11 (+) getInstance() devuelve siempre la misma conexión')]
    public function getInstanceDevuelveSiempreLaMismaInstancia(): void
    {
        $this->assertSame(Connection::getInstance(), Connection::getInstance());
    }

    #[Test]
    #[TestDox('CP-12 (+) Lo que se inserta se puede leer desde otra referencia a la conexión')]
    public function insertarYLeerDesdeOtraReferencia(): void
    {
        Connection::getInstance()->insert('orders', ['id' => 1]);

        $this->assertSame([['id' => 1]], Connection::getInstance()->all('orders'));
    }

    #[Test]
    #[TestDox('CP-13 (-) No se puede crear la conexión con new (constructor privado)')]
    public function constructorEsPrivado(): void
    {
        $constructor = (new ReflectionClass(Connection::class))->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertTrue($constructor->isPrivate());
    }

    #[Test]
    #[TestDox('CP-14 (-) No se puede clonar la conexión')]
    public function noSePuedeClonar(): void
    {
        $this->expectException(Error::class);

        $copia = clone Connection::getInstance();
        unset($copia);
    }

    #[Test]
    #[TestDox('CP-15 (-) Insertar una fila vacía lanza un error en vez de ocultarlo')]
    public function insertarFilaVaciaLanzaExcepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Connection::getInstance()->insert('orders', []);
    }
}
