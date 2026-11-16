<?php
/**
 * VISTA: listado de pedidos (MVC).
 * Recibe $pedidos ya resuelto desde el controlador y SOLO lo muestra:
 * sin conexión a la base, sin cálculos y con toda la salida escapada (evita XSS).
 *
 * @var list<array<string, mixed>> $pedidos
 */
$e = fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>
<h1>Pedidos</h1>

<table border="1">
    <tr><th>ID</th><th>Paciente</th><th>Total</th></tr>
    <?php foreach ($pedidos as $pedido) : ?>
        <tr>
            <td><?= $e($pedido['id']) ?></td>
            <td><?= $e($pedido['paciente']) ?></td>
            <td><?= $e($pedido['total']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
