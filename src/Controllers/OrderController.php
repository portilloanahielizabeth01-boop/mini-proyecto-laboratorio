<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Order;
use App\Reports\BasicReport;
use App\Reports\DigitalSignatureDecorator;
use App\Reports\PdfReportDecorator;
use App\Reports\WatermarkDecorator;
use App\Services\OrderService;

/**
 * ============================================================================
 *  CONTROLADOR DE PEDIDOS
 *  Patrón aplicado: MVC + SRP
 * ============================================================================
 *
 *  Antes el controlador armaba SQL, calculaba descuentos, decidía qué
 *  notificación mandar e imprimía HTML con echo.
 *
 *  Ahora solo: 1) recibe la entrada, 2) llama al servicio,
 *  3) elige qué vista mostrar. No tiene SQL, ni reglas de negocio, ni HTML.
 * ============================================================================
 */
final class OrderController
{
    public function __construct(
        private OrderService $service,
        private string $viewsPath
    ) {
    }

    /**
     * @param array<string, string> $input normalmente $_GET
     */
    public function create(array $input): string
    {
        $order = new Order(
            (int) ($input['id'] ?? 1),
            (string) ($input['paciente'] ?? 'Juan Perez'),
            (float) ($input['monto'] ?? 15000),
            (string) ($input['tipo'] ?? 'obra_social')
        );
        $total = $this->service->createOrder($order);

        return $this->render('order-created', ['pedido' => $order, 'total' => $total]);
    }

    public function index(): string
    {
        return $this->render('orders', ['pedidos' => $this->service->listOrders()]);
    }

    public function report(): string
    {
        $reporte = new WatermarkDecorator(
            new PdfReportDecorator(
                new DigitalSignatureDecorator(
                    new BasicReport('Pedidos del dia')
                )
            )
        );

        return $this->render('report', ['reporte' => $reporte->generate()]);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function render(string $view, array $data): string
    {
        extract($data);
        ob_start();
        require $this->viewsPath . "/{$view}.php";

        return (string) ob_get_clean();
    }
}
