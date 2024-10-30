<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No estás logueado']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

try {
    // Actualizar la cantidad en la base de datos
    $stmt = $base->prepare("UPDATE detalles_carrito SET cantidad = :cantidad WHERE carrito_id = (SELECT id_carrito FROM carritos WHERE usuario_id = :usuario_id) AND producto_id = :producto_id");
    $stmt->execute([
        ':cantidad' => $cantidad,
        ':usuario_id' => $usuario_id,
        ':producto_id' => $producto_id
    ]);

    // Obtener el total de artículos y el precio total
    $stmt = $base->prepare("SELECT SUM(cantidad) as totalArticulos, SUM(cantidad * p.precio_producto) as totalPrecio FROM detalles_carrito dc JOIN productos p ON dc.producto_id = p.codigo_producto WHERE dc.carrito_id = (SELECT id_carrito FROM carritos WHERE usuario_id = :usuario_id)");
    $stmt->execute([':usuario_id' => $usuario_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'totalArticulos' => $result['totalArticulos'],
        'totalPrecio' => $result['totalPrecio']
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
