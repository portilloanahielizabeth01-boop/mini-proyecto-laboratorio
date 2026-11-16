<?php
/**
 * VISTA: confirmación de pedido creado (MVC).
 *
 * @var \App\Models\Order $pedido
 * @var float $total
 */
$e = fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>
<h1>Pedido creado</h1>
<p>Paciente: <?= $e($pedido->patient) ?></p>
<p>Total: $ <?= $e($total) ?></p>
