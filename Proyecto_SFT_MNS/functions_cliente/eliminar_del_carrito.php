<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit();
}

require '../config/database.php';

$usuarioId = $_SESSION['usuario_id'];
$productoId = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
$talla = isset($_POST['talla']) ? $_POST['talla'] : '';

if ($productoId <= 0 || empty($talla)) {
    echo json_encode(['error' => 'ID de producto o talla inválidos']);
    exit();
}

// Eliminar el producto de la tabla 'detalles_carrito'
try {
    $query = $base->prepare("
        DELETE FROM detalles_carrito 
        WHERE carrito_id = (
            SELECT id_carrito 
            FROM carritos 
            WHERE usuario_id = :usuario_id
        ) 
        AND producto_id = :producto_id
        AND talla = :talla
    ");

    $query->execute([
        'usuario_id' => $usuarioId,
        'producto_id' => $productoId,
        'talla' => $talla
    ]);

    // Calcular el nuevo total de artículos y el total del carrito
    $queryTotal = $base->prepare("
        SELECT 
            COALESCE(SUM(dc.cantidad), 0) as totalArticulos, 
            COALESCE(SUM(dc.cantidad * p.precio_producto), 0) as totalPrecio 
        FROM detalles_carrito dc
        INNER JOIN productos p ON dc.producto_id = p.codigo_producto
        WHERE dc.carrito_id = (
            SELECT id_carrito 
            FROM carritos 
            WHERE usuario_id = :usuario_id
        )
    ");

    $queryTotal->execute(['usuario_id' => $usuarioId]);
    $total = $queryTotal->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'totalArticulos' => $total['totalArticulos'],
        'totalPrecio' => $total['totalPrecio']
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
