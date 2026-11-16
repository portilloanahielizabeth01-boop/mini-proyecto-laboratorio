<?php

declare(strict_types=1);

namespace App;

use App\Controllers\OrderController;
use App\Database\Connection;
use App\Events\DashboardObserver;
use App\Events\EmailObserver;
use App\Events\OrderSubject;
use App\Events\SmsObserver;
use App\Models\Order;
use App\Notifications\NotificationSender;
use App\Pricing\PriceCalculator;
use App\Pricing\PricingStrategyResolver;
use App\Repositories\OrderRepository;
use App\Routing\Router;
use App\Services\OrderService;
use App\Services\OrderValidator;

/**
 * Arma todas las piezas del sistema en un solo lugar ("composition root").
 * Así public/index.php queda corto y las pruebas pueden armar lo mismo.
 */
final class Bootstrap
{
    /**
     * @param array<string, string> $config
     */
    public static function router(array $config, string $viewsPath): Router
    {
        $connection = Connection::getInstance($config);
        $repository = new OrderRepository($connection);
        $pricing = new PricingStrategyResolver();

        self::seedDemoData($repository, $pricing);

        $events = new OrderSubject();
        $events->subscribe(new EmailObserver(NotificationSender::make('email')));
        $events->subscribe(new SmsObserver(NotificationSender::make('sms')));
        $events->subscribe(new DashboardObserver());

        $service = new OrderService(new OrderValidator(), $pricing, $repository, $events);
        $controller = new OrderController($service, $viewsPath);

        $router = new Router();
        $router->add('crear', fn (): string => $controller->create($_GET));
        $router->add('listar', fn (): string => $controller->index());
        $router->add('reporte', fn (): string => $controller->report());

        return $router;
    }

    /**
     * Los mismos dos pedidos de ejemplo que mostraba la vista original.
     * (La persistencia es en memoria: se reinicia en cada request.)
     */
    private static function seedDemoData(OrderRepository $repository, PricingStrategyResolver $pricing): void
    {
        if ($repository->findAll() !== []) {
            return;
        }

        $demo = [
            new Order(1, 'Juan Perez', 15000, 'obra_social'),
            new Order(2, 'Ana Gomez', 22000, 'particular'),
        ];

        foreach ($demo as $order) {
            $total = (new PriceCalculator($pricing->forPatientType($order->patientType)))->calculate($order->amount);
            $repository->save($order, $total);
        }
    }
}
