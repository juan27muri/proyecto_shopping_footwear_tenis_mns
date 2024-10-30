<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir al índice con un mensaje
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}
$nombreUsuario = $_SESSION['nombre_usuario']; // Suponiendo que se guarda el nombre en la sesión
$fotoUsuario = $_SESSION['foto_usuario'] ?? 'default.png'; // User's profile picture or a default image if not set


// Conectar a la base de datos
require '../config/database.php';

// Obtener el ID del usuario de la sesión
$usuario_id = $_SESSION['usuario_id'];

// Consulta para obtener los productos en el carrito del usuario
$queryCarrito = $base->prepare("
    SELECT c.id_carrito, dc.producto_id, dc.cantidad, dc.talla, p.nombre_producto, p.precio_producto, p.imagen_producto 
    FROM carritos c 
    JOIN detalles_carrito dc ON c.id_carrito = dc.carrito_id 
    JOIN productos p ON dc.producto_id = p.codigo_producto 
    WHERE c.usuario_id = :usuario_id
");
$queryCarrito->bindParam(':usuario_id', $usuario_id);
$queryCarrito->execute();
$productosCarrito = $queryCarrito->fetchAll(PDO::FETCH_ASSOC);

$totalArticulos = 0;
$totalPrecio = 0.0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title> 
    <link rel="stylesheet" href="../css/carrito2.css">
    <link rel="icon" href="../imagenes/favicon.png" type="image/x-icon">
</head>
<body>
<header>
        <div class="container">
            <div class="logo">
                <h1>ㅤShopping Footwear Tenis MNS</h1> <br> 
            </div>
            
            <div class="search-bar">
            ㅤㅤㅤ<input type="text" id="search-bar" placeholder="Buscar productos...">ㅤ
                <button id="search-button">Buscar</button>
                <a href="carrito.php">
                    <div class="move-car">
                        <img src="../imagenes/carrito.png" alt="Carrito" width="30px" height="30px">
                    
                </a>
            </div>


            <div class="user-options">
                <span>usuario: <?php echo htmlspecialchars($nombreUsuario); ?></span>
                <a href="soporte.php">Soporte</a>
                <a href="index_cliente.php">productos</a>
                <a href="mis_pedidos.php">Ver mis pedidos</a>ㅤ
            </div>ㅤㅤ
            <div class="move-sesion">
                <form id="logout-form" method="POST" action="cerrar_sesion.php">
                    <button type="button" id="logout-button" class="buttonC">Cerrar sesión</button>
                </form>
            </div> <br> <br>
        </div>
    </header>
    
    <div class="container_carrito">
        <main>
            <section class="cart-section">
                <h1 class="title_car">Carrito de Compras</h1>
                <div class="cart-items">
                    <?php if (count($productosCarrito) > 0): ?>
                        <?php foreach ($productosCarrito as $producto): ?>
                            <div class="cart-item">
                                <img src="../cruds/productos/images/<?php echo htmlspecialchars($producto['imagen_producto']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>" class="cart-item-image">
                                <div class="cart-item-details">
                                    <h2><?php echo htmlspecialchars($producto['nombre_producto']); ?></h2>
                                    <p>Talla: <?php echo htmlspecialchars($producto['talla']); ?></p>
                                    <p>Precio: $<span class="product-price"><?php echo number_format($producto['precio_producto'], 2); ?></span></p>
                                    <p>Cantidad: <?php echo htmlspecialchars($producto['cantidad']); ?></p>
                                    <div class="cart-item-actions">
                                    <button class="remove-item" data-producto-id="<?php echo $producto['producto_id']; ?>" data-talla="<?php echo $producto['talla']; ?>">Eliminar</button>

                                    </div>
                                </div>
                            </div>

                            <?php
                            $subtotal = $producto['cantidad'] * $producto['precio_producto'];
                            $totalArticulos += $producto['cantidad'];
                            $totalPrecio += $subtotal;
                            ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay productos en el carrito.</p>
                    <?php endif; ?>
                </div>
                <div class="cart-summary">
                    <h2>Resumen del Pedido</h2>
                    <p>Total de Artículos: <span id="total-items"><?php echo $totalArticulos; ?></span></p>
                    <p>Total: $<span id="total-price"><?php echo number_format($totalPrecio, 2); ?></span></p>
                    <form action="realizar_pedido.php" method="get">
                        <button type="submit" class="checkout-button">Proceder al Pago</button>
                    </form>

                </div>
            </section>
        </main>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 Shopping Footwear Tenis MNS. Todos los derechos reservados.</p>
        </div>
    </footer>
    <script src="../js/carrito.js"></script>
</body>
</html>
