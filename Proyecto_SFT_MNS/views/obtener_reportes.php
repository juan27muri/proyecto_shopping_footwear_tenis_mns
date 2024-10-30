<?php
require '../config/database.php';

// Obtener pedidos por día
$sqlPedidosPorDia = "SELECT DATE(fecha_pedido) as fecha, COUNT(*) as total_pedidos FROM pedidos GROUP BY DATE(fecha_pedido)";
$stmt = $base->query($sqlPedidosPorDia);
$pedidosPorDia = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener ganancias por día
$sqlGananciasPorDia = "SELECT DATE(fecha_pedido) as fecha, SUM(total_pedido) as total_ganancias FROM pedidos GROUP BY DATE(fecha_pedido)";
$stmt = $base->query($sqlGananciasPorDia);
$gananciasPorDia = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener pedidos por mes
$sqlPedidosPorMes = "SELECT DATE_FORMAT(fecha_pedido, '%Y-%m') as fecha, COUNT(*) as total_pedidos FROM pedidos GROUP BY DATE_FORMAT(fecha_pedido, '%Y-%m')";
$stmt = $base->query($sqlPedidosPorMes);
$pedidosPorMes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener ganancias por mes
$sqlGananciasPorMes = "SELECT DATE_FORMAT(fecha_pedido, '%Y-%m') as fecha, SUM(total_pedido) as total_ganancias FROM pedidos GROUP BY DATE_FORMAT(fecha_pedido, '%Y-%m')";
$stmt = $base->query($sqlGananciasPorMes);
$gananciasPorMes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retornar los datos como JSON
echo json_encode([
    'pedidosPorDia' => $pedidosPorDia,
    'gananciasPorDia' => $gananciasPorDia,
    'pedidosPorMes' => $pedidosPorMes,
    'gananciasPorMes' => $gananciasPorMes,
]);
?>
