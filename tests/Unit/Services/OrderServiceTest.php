<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Database\Connection;
use App\Events\OrderObserver;
use App\Events\OrderSubject;
use App\Models\Order;
use App\Pricing\PricingStrategyResolver;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use App\Services\OrderValidator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Funcionalidad F9 — Crear un pedido completo (patrón Facade).
 * Prueba de integración liviana: usa las piezas reales, salvo el observador,
 * que es un "espía" para saber a quién se notificó sin imprimir nada.
 */
final class OrderServiceTest extends TestCase
{
    private OrderService $service;
    private OrderRepository $repository;
    /** @var OrderObserver&object{avisados: list<int>} */
    private OrderObserver $espia;

    protected function setUp(): void
    {
        Connection::getInstance()->limpiar();
        $this->repository = new OrderRepository(Connection::getInstance());

        $this->espia = new class implements OrderObserver {
            /** @var list<int> */
            public array $avisados = [];

            public function update(Order $order): void
            {
                $this->avisados[] = $order->id;
            }
        };
        $events = new OrderSubject();
        $events->subscribe($this->espia);

        $this->service = new OrderService(
            new OrderValidator(),
            new PricingStrategyResolver(),
            $this->repository,
            $events
        );
    }

    #[Test]
    #[TestDox('CP-30 (+) Crear un pedido de obra social calcula 10500, lo guarda y avisa')]
    public function crearPedidoCalculaGuardaYAvisa(): void
    {
        $total = $this->service->createOrder(new Order(1, 'Juan Perez', 15000, 'obra_social'));

        $this->assertEqualsWithDelta(10500.0, $total, 0.001);
        $this->assertCount(1, $this->service->listOrders());
        $this->assertEqualsWithDelta(10500.0, $this->service->listOrders()[0]['total'], 0.001);
        $this->assertSame([1], $this->espia->avisados);
    }

    #[Test]
    #[TestDox('CP-31 (-) Un pedido con monto cero se rechaza: no se guarda ni se avisa')]
    public function montoCeroSeRechazaSinGuardarNiAvisar(): void
    {
        try {
            $this->service->createOrder(new Order(2, 'Ana Gomez', 0, 'particular'));
            $this->fail('Se esperaba una excepción por monto inválido');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Monto inválido', $e->getMessage());
        }

        $this->assertSame([], $this->repository->findAll());
        $this->assertSame([], $this->espia->avisados);
    }

    #[Test]
    #[TestDox('CP-32 (-) Un tipo de paciente desconocido se rechaza y no se guarda nada')]
    public function tipoDesconocidoNoGuardaNada(): void
    {
        try {
            $this->service->createOrder(new Order(3, 'Ana Gomez', 1000, 'vip'));
            $this->fail('Se esperaba una excepción por tipo desconocido');
        } catch (InvalidArgumentException) {
            // esperado
        }

        $this->assertSame([], $this->repository->findAll());
        $this->assertSame([], $this->espia->avisados);
    }
}
