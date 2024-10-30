<?php
session_start();

// Conectar a la base de datos
require '../config/database.php';

// Verificar si se ha pasado el producto_id en la URL
if (!isset($_GET['producto_id'])) {
    // Redirigir al índice si no se especifica un producto
    header('Location: ../index.php?message=Producto%20no%20especificado');
    exit();
}

$producto_id = $_GET['producto_id'];

// Consulta para obtener los detalles del producto
$queryProducto = $base->prepare("SELECT * FROM productos WHERE codigo_producto = :producto_id LIMIT 1");
$queryProducto->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
$queryProducto->execute();

$producto = $queryProducto->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró el producto
if (!$producto) {
    // Redirigir al índice si no se encuentra el producto
    header('Location: ../index.php?message=Producto%20no%20encontrado');
    exit();
}

$tallasDisponibles = explode(',', $producto['tallas']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre_producto']); ?> - Shopping Footwear Tenis MNS</title>
    <link rel="stylesheet" href="../css/modelo_zapato1.css">
    <link rel="icon" href="../imagenes/favicon.png" type="image/x-icon">
</head>
<body>
<header>
        <div class="container">
            <div class="logo">
                <h1>ㅤShopping Footwear Tenis MNS</h1> <br> 
            </div>
            <div class="search-bar">
                ㅤㅤㅤ<form method="get" action="">
                    <input type="text" name="search" placeholder="Buscar productos..." value="">
                    <button type="submit" class="button">Buscar</button>
                </form>
                </div> <br> <br></div><br> <br>
            <div class="user-optionss">
                <a href="login.php">ㅤㅤIniciar Sesion</a>
                <a href="admin_dashboard.html"></a>
            </div>
        </div>
    </header>

<div class="container">
    <nav>
        <div class="container">
            <ul>
                <img src="https://w7.pngwing.com/pngs/403/367/png-transparent-adidas-logo-adidas-logo-cdr-angle-text.png" alt="" width="60" height="40"> <br>ㅤㅤㅤ
                <img src="https://e7.pngegg.com/pngimages/14/927/png-clipart-swoosh-nike-nike-angle-triangle.png" alt="" width="60" height="40">      ㅤㅤㅤ
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTQnJTRO8uTVTxTbKeRDLo2Q5tCDtFSk995CQ&usqp=CAU" alt="" width="60" height="40">ㅤㅤㅤ
                <img src="https://e7.pngegg.com/pngimages/848/331/png-clipart-brand-logo-skechers-shoe-sneakers-asics-logo-blue-text.png" alt="" width="60" height="40">    ㅤㅤㅤ
                <img src="https://e7.pngegg.com/pngimages/256/226/png-clipart-logo-new-balance-brand-shoe-trademark-new-balance-logo.png" alt="" width="60" height="40">    ㅤㅤㅤ
                <img src="https://w7.pngwing.com/pngs/889/412/png-transparent-converse-logo-converse-chuck-taylor-all-stars-sneakers-shoe-clothing-brand-text-fashion-logo.png" alt="" width="100" height="40">ㅤㅤㅤ
            </ul>
        </div>
    </nav>

    <div class="container_zapato">
        <img src="../cruds/productos/images/<?php echo htmlspecialchars($producto['imagen_producto']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>" class="product-image">
        <div class="product-details">
            <h2 class="product-title"><?php echo htmlspecialchars($producto['nombre_producto']); ?></h2>
            <p class="product-price">$<?php echo htmlspecialchars($producto['precio_producto']); ?></p>
            <p class="product-description"><?php echo htmlspecialchars($producto['descripcion_producto']); ?></p>

            <!-- Mensaje para usuarios no autenticados -->
            <p class="message"><a href="login.php">Inicia sesión</a> para poder comprar</p>
        </div>
    </div>

    <!-- Categorías -->
    <div class="move-text">
        <h1>Nuestras Categorías</h1>
    </div>
    <div class="categories-container">
    <div class="category-item">
        <a href="productos_por_categoria_NoLogin.php?categoria_id=1">
            <img src="../imagenes/imagen7.jpg" alt="Hombre">
            <span>Hombres</span>
        </a>
    </div>
    <div class="category-item">
        <a href="productos_por_categoria_NoLogin.php?categoria_id=2">
            <img src="../imagenes/imagen8.jpg" alt="Mujer">
            <span>Damas</span>
        </a>
    </div>
    <div class="category-item">
        <a href="productos_por_categoria_NoLogin.php?categoria_id=3">
            <img src="../imagenes/imagen9.jpg" alt="Niños">
            <span>Niños</span>
        </a>
    </div>
    <div class="category-item">
        <a href="productos_por_categoria_NoLogin.php?categoria_id=4">
            <img src="../imagenes/imagen10.jpg" alt="Deportivos">
            <span>Deportivos</span>
        </a>
    </div>
    <div class="category-item">
        <a href="productos_por_categoria_NoLogin.php?categoria_id=5">
            <img src="../imagenes/imagen11.jpg" alt="Botas">
            <span>Botas</span>
        </a>
    </div>
    <div class="category-item">
        <a href="productos_por_categoria_NoLogin.php?categoria_id=6">
            <img src="../imagenes/imagen13.jpg" alt="Chanclas">
            <span>Chanclas</span>
        </a>
    </div>
</div>

    <footer>
        <div class="container">
            <p>&copy; 2024 Shopping Footwear Tenis MNS. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="../js/pag_principal.js"></script>
    <?php if (isset($_GET['message'])): ?>
        <script>
            alert("<?php echo htmlspecialchars($_GET['message']); ?>");
        </script>
    <?php endif; ?>
</body>
</html>
