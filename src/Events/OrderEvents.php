<?php
/**
 * ============================================================================
 *  AVISOS AL CREARSE UN PEDIDO
 *  Patrón aplicado: OBSERVER (comportamiento)
 * ============================================================================
 */

/**
 * 1. Contrato común para todos los observadores (la interfaz que conoce el sujeto).
 */
interface OrderObserver
{
    public function update(Order $order): void;
}

/**
 * 2. Observador concreto para Emails.
 */
final class EmailObserver implements OrderObserver
{
    public function __construct(private EmailNotification $emailNotification) {}

    public function update(Order $order): void
    {
        $this->emailNotification->enviarEmail(
            'paciente@mail.com',
            'Pedido creado',
            "Pedido {$order->id}"
        );
    }
}

/**
 * 3. Observador concreto para SMS (Ejercicio 5: agregado sin tocar el sujeto).
 */
final class SmsObserver implements OrderObserver
{
    public function __construct(private SmsNotification $smsNotification) {}

    public function update(Order $order): void
    {
        $this->smsNotification->mandarSms(
            '3704000000',
            "Pedido {$order->id} creado"
        );
    }
}

/**
 * 4. Observador concreto para Dashboard.
 */
final class DashboardObserver implements OrderObserver
{
    public function update(Order $order): void
    {
        echo "[DASHBOARD] actualizado para el pedido {$order->id}<br>";
    }
}

/**
 * 5. El Sujeto (Subject): mantiene la colección de interfaces y notifica.
 */
final class OrderSubject
{
    /** @var OrderObserver[] Colección de interfaces, no de clases concretas */
    private array $observers = [];

    public function subscribe(OrderObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    public function notify(Order $order): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($order); // POLIMORFISMO puro
        }
    }
}

/*
 * ============================================================================
 *  EJEMPLO DE USO
 * ============================================================================
 */

// Se inicializa el sujeto y se suscriben dinámicamente los observadores
$subject = new OrderSubject();
$subject->subscribe(new EmailObserver(new EmailNotification()));
$subject->subscribe(new SmsObserver(new SmsNotification()));
$subject->subscribe(new DashboardObserver());

// Al crear el pedido, se notifica a todos los subscritos de forma desacoplada
// $subject->notify($order);
