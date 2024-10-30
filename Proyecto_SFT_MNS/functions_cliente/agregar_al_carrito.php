<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$producto_id = $_POST['producto_id'];
$cantidad = (int) $_POST['cantidad'];
$talla = $_POST['talla'];

// Obtener el ID del carrito del usuario
$queryCarrito = $base->prepare("SELECT id_carrito FROM carritos WHERE usuario_id = :usuario_id");
$queryCarrito->bindParam(':usuario_id', $usuario_id);
$queryCarrito->execute();
$carrito = $queryCarrito->fetch(PDO::FETCH_ASSOC);

if ($carrito) {
    $carrito_id = $carrito['id_carrito'];

    // Verificar si el producto con la talla específica ya está en el carrito
    $query_check = "SELECT cantidad FROM detalles_carrito WHERE carrito_id = :carrito_id AND producto_id = :producto_id AND talla = :talla";
    $stmt_check = $base->prepare($query_check);
    $stmt_check->bindParam(':carrito_id', $carrito_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':talla', $talla, PDO::PARAM_STR);
    $stmt_check->execute();
    $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Si el producto con la talla ya está en el carrito, actualizar la cantidad
        $query_update = "UPDATE detalles_carrito SET cantidad = cantidad + :cantidad WHERE carrito_id = :carrito_id AND producto_id = :producto_id AND talla = :talla";
        $stmt_update = $base->prepare($query_update);
        $stmt_update->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt_update->bindParam(':carrito_id', $carrito_id, PDO::PARAM_INT);
        $stmt_update->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
        $stmt_update->bindParam(':talla', $talla, PDO::PARAM_STR);
        $stmt_update->execute();
    } else {
        // Si el producto con la talla no está en el carrito, insertarlo
        $query_insert = "INSERT INTO detalles_carrito (carrito_id, producto_id, cantidad, talla) VALUES (:carrito_id, :producto_id, :cantidad, :talla)";
        $stmt_insert = $base->prepare($query_insert);
        $stmt_insert->bindParam(':carrito_id', $carrito_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt_insert->bindParam(':talla', $talla, PDO::PARAM_STR);
        $stmt_insert->execute();
    }
} else {
    // Si el usuario no tiene un carrito, crear uno nuevo
    $stmt_create_carrito = $base->prepare("INSERT INTO carritos (usuario_id) VALUES (:usuario_id)");
    $stmt_create_carrito->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_create_carrito->execute();
    $carrito_id = $base->lastInsertId(); // Obtener el ID del nuevo carrito

    // Insertar el producto en el nuevo carrito
    $query_insert = "INSERT INTO detalles_carrito (carrito_id, producto_id, cantidad, talla) VALUES (:carrito_id, :producto_id, :cantidad, :talla)";
    $stmt_insert = $base->prepare($query_insert);
    $stmt_insert->bindParam(':carrito_id', $carrito_id, PDO::PARAM_INT);
    $stmt_insert->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
    $stmt_insert->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
    $stmt_insert->bindParam(':talla', $talla, PDO::PARAM_STR);
    $stmt_insert->execute();
}

// Responder con un mensaje en formato JSON
echo json_encode(['success' => true, 'message' => 'Producto añadido al carrito']);
exit();
?>
