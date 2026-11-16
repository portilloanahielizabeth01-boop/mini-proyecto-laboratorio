<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Events\DashboardObserver;
use App\Events\EmailObserver;
use App\Events\OrderObserver;
use App\Events\OrderSubject;
use App\Models\Order;
use App\Notifications\EmailNotification;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Funcionalidad F6 — Avisos al crear un pedido (patrón Observer).
 */
final class OrderSubjectTest extends TestCase
{
    #[Test]
    #[TestDox('CP-22 (+) Al notificar, se avisa a todos los observadores suscriptos y en orden')]
    public function notificaATodosLosSuscriptos(): void
    {
        $subject = new OrderSubject();
        $subject->subscribe(new EmailObserver(new EmailNotification()));
        $subject->subscribe(new DashboardObserver());

        $this->expectOutputString(
            '[EMAIL] para paciente@mail.com: Pedido 5 creado<br>'
            . '[DASHBOARD] actualizado para el pedido 5<br>'
        );

        $subject->notify(new Order(5, 'Juan Perez', 15000, 'obra_social'));
    }

    #[Test]
    #[TestDox('CP-23 (+) Se puede sumar un observador nuevo sin modificar OrderSubject')]
    public function seSumaUnObservadorNuevoSinTocarElSujeto(): void
    {
        $espia = new class implements OrderObserver {
            /** @var list<int> */
            public array $recibidos = [];

            public function update(Order $order): void
            {
                $this->recibidos[] = $order->id;
            }
        };

        $subject = new OrderSubject();
        $subject->subscribe($espia);
        $subject->notify(new Order(9, 'Ana Gomez', 100, 'particular'));

        $this->assertSame([9], $espia->recibidos);
    }

    #[Test]
    #[TestDox('CP-24 (-) Sin observadores suscriptos, notificar no falla ni muestra nada')]
    public function sinObservadoresNoFalla(): void
    {
        $subject = new OrderSubject();

        $this->expectOutputString('');
        $subject->notify(new Order(1, 'Juan Perez', 15000, 'particular'));

        $this->assertSame(0, $subject->count());
    }
}
