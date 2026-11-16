<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers;

use App\Controllers\OrderController;
use App\Database\Connection;
use App\Events\OrderSubject;
use App\Models\Order;
use App\Pricing\PricingStrategyResolver;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use App\Services\OrderValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Funcionalidad F10 — Controlador y vistas (MVC).
 */
final class OrderControllerTest extends TestCase
{
    private OrderController $controller;
    private OrderRepository $repository;

    protected function setUp(): void
    {
        Connection::getInstance()->limpiar();
        $this->repository = new OrderRepository(Connection::getInstance());
        $service = new OrderService(
            new OrderValidator(),
            new PricingStrategyResolver(),
            $this->repository,
            new OrderSubject()   // sin observadores: no imprime avisos
        );
        $this->controller = new OrderController($service, dirname(__DIR__, 3) . '/views');
    }

    #[Test]
    #[TestDox('CP-33 (+) Crear un pedido muestra el paciente y el total calculado')]
    public function crearMuestraPacienteYTotal(): void
    {
        $html = $this->controller->create([
            'id' => '1', 'paciente' => 'Juan Perez', 'monto' => '15000', 'tipo' => 'obra_social',
        ]);

        $this->assertStringContainsString('<h1>Pedido creado</h1>', $html);
        $this->assertStringContainsString('Paciente: Juan Perez', $html);
        $this->assertStringContainsString('Total: $ 10500', $html);
    }

    #[Test]
    #[TestDox('CP-34 (-) Un nombre con código HTML se muestra escapado (sin XSS)')]
    public function nombreConScriptSeEscapa(): void
    {
        $html = $this->controller->create(['paciente' => '<script>alert(1)</script>']);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
    }

    #[Test]
    #[TestDox('CP-35 (+) El listado muestra los pedidos guardados')]
    public function listadoMuestraLosPedidos(): void
    {
        $this->repository->save(new Order(2, 'Ana Gomez', 22000, 'particular'), 22000);

        $html = $this->controller->index();

        $this->assertStringContainsString('<td>Ana Gomez</td>', $html);
        $this->assertStringContainsString('<td>22000</td>', $html);
    }

    #[Test]
    #[TestDox('CP-36 (+) El reporte del día sale con firma, PDF y marca de agua')]
    public function reporteSaleDecorado(): void
    {
        $this->assertSame(
            'Reporte: Pedidos del dia + firma digital + PDF + marca de agua',
            trim($this->controller->report())
        );
    }
}
