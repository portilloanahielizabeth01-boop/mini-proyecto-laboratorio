<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Database\Connection;
use App\Models\Order;
use App\Repositories\OrderRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Funcionalidad F5 — Persistencia de pedidos (patrón Repository).
 */
final class OrderRepositoryTest extends TestCase
{
    private OrderRepository $repository;

    protected function setUp(): void
    {
        Connection::getInstance()->limpiar();
        $this->repository = new OrderRepository(Connection::getInstance());
    }

    #[Test]
    #[TestDox('CP-20 (+) Un pedido guardado aparece en el listado con su total')]
    public function pedidoGuardadoApareceEnElListado(): void
    {
        $this->repository->save(new Order(3, 'Ana Gomez', 22000, 'particular'), 22000);

        $this->assertSame([[
            'id'       => 3,
            'paciente' => 'Ana Gomez',
            'monto'    => 22000.0,
            'tipo'     => 'particular',
            'total'    => 22000.0,
        ]], $this->repository->findAll());
    }

    #[Test]
    #[TestDox('CP-21 (-) Sin pedidos guardados el listado está vacío (no falla)')]
    public function sinPedidosElListadoEstaVacio(): void
    {
        $this->assertSame([], $this->repository->findAll());
    }
}
